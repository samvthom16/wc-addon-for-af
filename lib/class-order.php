<?php

namespace WC_ADDON_FOR_AF;

use WC_Order_Item_Fee;

class ORDER extends BASE{

  /*
  * SET DELIVERY, DROP OFF & ACCESSORIES FEES
  * THIS VALUE IS SET BY DEFAULT EVEN IF IT WAS NOT ADDED
  * BY THE USER
  */
  function setFeesByDefault( $order ){

    // CHECKING IF ANY FEES WERE ENTERED
    if( count( $order->get_fees() ) < 1 ){

      // STANDARD LIST OF FEES TO BE ADDED
      $fees = array(
        'Delivery Fee'  => '300',
        'Drop Off Fee'  => '200',
        'Accessories'   => '0'
      );

      // ITERATE THROUGH EACH FEE AND SET THE DEFAULT AMOUNT
      foreach ( $fees as $fee_name => $fee_amount ) {
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

  /*
  * GET ADDRESS INFORMATION FROM THE BILLING ADDRESS OF THE CUSTOMER
  */
  function getAddressData( $order ){
    return array(
      'primary_address'   => $order->get_billing_address_1(),
      'secondary_address' => $order->get_billing_address_2(),
      'code_postal'       => $order->get_billing_postcode(),
      'city'              => $order->get_billing_city(),
      'state_code'        => $order->get_billing_state(),
      'state'             => get_state_name( $order->get_billing_country(), $order->get_billing_state() ),
      'country'           => get_country_name( $order->get_billing_country() )
    );
  }

  /*
  * GET CUSTOMER DATA
  * INCLUDES META INFORMATION AS WELL
  */
  function getCustomerData( $order ){
    $customer = $order->get_user();
    $data = array(
      'first_name' => $customer->user_firstname,
      'last_name'  => $customer->user_lastname,
      'email'      => $customer->user_email
    );

    // MERGE LAST NAME & FIRST NAME
    $data['name'] = '';
    if( isset( $data[ 'first_name' ] ) ){
      $data['name'] .= $data[ 'first_name' ] . ' ';
    }
    if( isset( $data[ 'last_name' ] ) ){
      $data['name'] .= $data[ 'last_name' ];
    }

    // GET META USER DATA
    $meta_user = get_user_meta( $customer->ID, 'af_meta', true );
    return array_merge( $data, $meta_user );
  }

  /*
  * INFORMATION OF THE VEHICLE THAT WAS SELECTED
  */
  function getVehicleData( $order ){
    $data = array();
    foreach ( $order->get_items() as $item_id => $item ) {
      $data['vehicle'] = $item->get_name();
      $product_instance = wc_get_product( $item->get_product_id() );
      $data['product_description'] = $product_instance->get_short_description();
    }
    return $data;
  }

  /*
  * ARRAY OF ALL THE FEES THAT HAS BEEN LEVIED ON THIS ORDER
  * ALSO INCLUDES THE SUBTOTAL AND TOTAL PRICE
  */
  function getFees( $order ){
    $data = array();

    // CALCULATION OF FEES
    foreach( $order->get_fees() as $fee ){
      if( strpos( strtolower( $fee->get_name() ), 'delivery' ) !== false ){
        $data['delivery_fee'] = $fee->get_amount();
      }
      elseif( strpos( strtolower( $fee->get_name() ), 'accessories' ) !== false ){
        $data['accessories_price'] = number_format( $fee->get_amount() );
      }
      else{
        $data['drop_off_fee'] = $fee->get_amount();
      }
    }

    $data['total_price'] = number_format( $order->get_total() );
    $data['subtotal_price'] = number_format( $order->get_subtotal() );

    return $data;
  }

  /*
  * ARRAY OF THE PAYMENTS RECEIVED
  * WITH DATE AND AMOUNT
  * BALANCE DUE IS ALSO CALCULATED
  */
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

    // CALCULATING BALANCE DUE
    $data['balance_due'] = $order->get_total();
    if( isset( $data['total_paid'] ) ){
      $data['balance_due'] -= $data['total_paid'];
    }
    if( $data['balance_due'] < 1 ) $data['balance_due'] = 0;
    $data['balance_due'] = number_format( $data['balance_due'] );

    return $data;
  }

  /*
  * ARRAY OF THE ORDER INFORMATION
  * META ORDER DATA
  */
  function getOrderMeta( $order ){
    $data = get_post_meta( $order->get_id(), 'af_meta', true );

    // CALCULATION TERM DURATION
    if( isset( $data['date_start'] ) && isset( $data['date_end'] ) ){
      $datediff = strtotime( $data['date_end'] ) - strtotime( $data['date_start'] );
      $data['duration'] = round( $datediff / ( 60 * 60  * 24 ) );
    }

    // ADD CURRENT DATE FOR INVOICE
    $data['date'] = date( "d M Y" );

    // FORMAT ALL DATE FIELDS
    $date_fields = array( 'date_start', 'date_end', 'date' );
    foreach( $date_fields as $date_field ){
      if( isset( $data[ $date_field ] ) ){
        $data[ $date_field ] = $this->formatDate( $data[ $date_field ] );
      }
    }

    // GET TERM NAMES FOR LOCATIONS
    $location_fields = array( 'delivery_place', 'return_place' );
    foreach( $location_fields as $location_field ){
      if( isset( $data[ $location_field ] ) ){
        $term_id = $data[ $location_field ];
        $term = get_term( $term_id );
        $data[ $location_field ] = $term->name;
        $data[ $location_field . '_remark' ] = $term->description;
      }
    }

    // ACCESSORIES TO BE FORMATTED AS COMMA SEPERATED VALUES
    if( isset( $data['accessories'] ) ){
      $data['accessories'] = af_filter_setting_value_accessories( $data['accessories'] );
    }

    return $data;
  }

  /*
  * FORMAT DATE
  */
  function formatDate( $date_field ){
    return date_format( date_create( $date_field ), "d M Y" );
  }

  /*
  * GENERATES PDF DOCUMENT FOR EACH ORDER
  */
  function generateDocument( $order_id, $slug ){

    $order = wc_get_order( $order_id );
    $meta_order = $this->getOrderMeta( $order );
    $customer = $this->getCustomerData( $order );
    $fees = $this->getFees( $order );
    $address = $this->getAddressData( $order );
    $vehicle = $this->getVehicleData( $order );
    $payments = $this->getPaymentsData( $order );
    $data = array_merge( $customer, $fees, $address, $vehicle, $meta_order, $payments );
    $data['order_id'] = $order_id;
    $data = apply_filters( 'af_data_' . $slug,  $data );

    $pdf = PDF::getInstance();
    return $pdf->download( $slug, $data, true );
    //return $pdf->download( 'contract', $data, true, $newfileslug );
  }

}
