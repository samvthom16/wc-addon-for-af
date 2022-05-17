<?php

namespace WC_ADDON_FOR_AF;

use WC_Order_Item_Fee;

class ORDER extends BASE{

  // SET DELIVERY, DROP OFF & ACCESSORIES FEES

  function setFeesByDefault( $order ){

    if( count( $order->get_fees() ) < 1 ){

      $fees = array(
        'Delivery Fee'  => '300',
        'Drop Off Fee'  => '200',
        'Accessories'   => '0'
      );

      //print_r( $fees );

      foreach ($fees as $fee_name => $fee_amount) {
        $item_fee = new WC_Order_Item_Fee();
        $item_fee->set_name( $fee_name );
        $item_fee->set_amount( $fee_amount );
        $item_fee->set_total( $fee_amount );
        $order->add_item( $item_fee );
      }

      $order->calculate_totals();
      $order->save();

    }

  }

  function getDataForContract( $order_id ){
    $order = wc_get_order( $order_id );

    $meta_order = $this->getOrderMeta( $order );
    $address = $this->getAddressData( $order );
    $customer = $this->getCustomerData( $order );
    $vehicle = $this->getVehicleData( $order );

    $data = array_merge( $meta_order, $customer, $address, $vehicle );

    $data['order_id'] = $order_id;

    $data['total_price'] = number_format( $order->get_total() );
    $data['advance_price'] = number_format( $order->get_total() );
    $data['price'] = number_format( $data['price'] );

    $checkbox_fields = array( 'title', 'language', 'purpose' );
    foreach( $checkbox_fields as $checkbox_field ){
      if( isset( $data[ $checkbox_field ] ) ){
        $new_slug = strtolower( $data[ $checkbox_field ] ) . '_check';
        $data[ $new_slug ] = 'yes';
        unset( $data[ $checkbox_field ] );
      }
    }

    $data['place'] = 'Paris';
    return $data;
  }

  function getAddressData( $order ){
    return array(
      'primary_address'   => $order->get_billing_address_1(),
      'secondary_address' => $order->get_billing_address_2(),
      'code_postal'       => $order->get_billing_postcode(),
      'city'              => $order->get_billing_city(),
      'country'           => get_country_name( $order->get_billing_country() )
    );
  }

  function getCustomerData( $order ){
    $customer = $order->get_user();

    $data = array(
      'first_name' => $customer->user_firstname,
      'last_name'  => $customer->user_lastname,
      'email'      => $customer->user_email
    );

    $meta_user = get_user_meta( $customer->ID, 'af_meta', true );

    return array_merge( $data, $meta_user );
  }

  function getVehicleData( $order ){
    $data = array();
    foreach ( $order->get_items() as $item_id => $item ) {
      $data['vehicle'] = $item->get_name();
    }
    return $data;
  }

  function getFees( $order ){
    $data = array();

    // CALCULATION OF FEES
    foreach( $order->get_fees() as $fee ){
      if( strpos( strtolower( $fee->get_name() ), 'delivery' ) !== false ){
        $data['delivery_fee'] = $fee->get_amount();
      }
      elseif( strpos( strtolower( $fee->get_name() ), 'accessories' ) !== false ){
        $data['accessories_price'] = $fee->get_amount();
      }
      else{
        $data['drop_off_fee'] = $fee->get_amount();
      }
    }

    return $data;
  }

  function getPaymentsData( $order ){
    $data = array( 'total_paid' => 0 );
    $payments = get_post_meta( $order->get_id(), 'af_payments', true );

    $i = 1;
    foreach ( $payments as $payment ) {
      if( isset( $payment['date'] ) && isset( $payment['amount'] ) ){
        $data[ 'total_paid' ] += $payment['amount'];
        $data[ "payment_rcvd_amount_$i" ] = number_format( $payment['amount'] );
        $data[ "payment_rcvd_date_$i" ] = $this->formatDate( $payment['date'] );
        $i++;
      }
    }
    return $data;
  }

  function getOrderMeta( $order ){
    $data = get_post_meta( $order->get_id(), 'af_meta', true );

    // CALCULATION TERM DURATION
    if( isset( $data['date_start'] ) && isset( $data['date_end'] ) ){
      $datediff = strtotime( $data['date_end'] ) - strtotime( $data['date_start'] );
      $data['duration'] = round( $datediff / ( 60 * 60  * 24 ) );
    }

    // ADD CURRENT DATE FOR INVOICE & CONTRACT
    $data['date'] = date( "d M Y" );

    // FORMAT ALL DATE FIELDS
    $date_fields = array( 'date_start', 'date_end', 'date' );
    foreach( $date_fields as $date_field ){
      if( isset( $data[ $date_field ] ) ){
        $data[ $date_field ] = $this->formatDate( $data[ $date_field ] );
      }
    }
    return $data;
  }

  function formatDate( $date_field ){
    return date_format( date_create( $date_field ), "d M Y" );
  }

  function getDataForInvoice( $order_id ){
    $order = wc_get_order( $order_id );

    $meta_order = $this->getOrderMeta( $order );
    $customer = $this->getCustomerData( $order );
    $fees = $this->getFees( $order );
    $address = $this->getAddressData( $order );
    $vehicle = $this->getVehicleData( $order );
    $payments = $this->getPaymentsData( $order );

    //$this->test( $customer );

    $data = array_merge( $customer, $fees, $address, $vehicle, $meta_order, $payments );

    $data['order_id'] = $order_id;

    $data['price'] = number_format( $order->get_subtotal() );
    $data['total_price'] = number_format( $order->get_total() );

    $data['balance_due'] = $order->get_total();
    if( isset( $data['total_paid'] ) ){
      $data['balance_due'] -= $data['total_paid'];
    }
    if( $data['balance_due'] < 1 ) $data['balance_due'] = 0;
    $data['balance_due'] = number_format( $data['balance_due'] );

    $data['name'] = '';
    if( isset( $data[ 'first_name' ] ) ){
      $data['name'] .= $data[ 'first_name' ] . ' ';
    }
    if( isset( $data[ 'last_name' ] ) ){
      $data['name'] .= $data[ 'last_name' ];
    }



    return $data;
  }

  function generateContract( $order_id ){
    $data = $this->getDataForContract( $order_id );
    $this->test( $data );

    $newfileslug = 'af_contract_' . $data['order_id'];
    $pdf = PDF::getInstance();
    return $pdf->download( 'contract', $data, $newfileslug );
  }

  function generateInvoice( $order_id ){
    $data = $this->getDataForInvoice( $order_id );
    $this->test( $data );

    $newfileslug = 'af_invoice_' . $data['order_id'];
    $pdf = PDF::getInstance();
    return $pdf->download( 'invoice', $data, $newfileslug );
  }



}
