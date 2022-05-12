<?php

namespace WC_ADDON_FOR_AF;

class ORDER extends BASE{

  function getDataForContract( $order_id ){
    $order = wc_get_order( $order_id );
    $meta_order = get_post_meta( $order_id, 'af_meta', true );

    $customer = $order->get_user();
    $meta_user = get_user_meta( $customer->ID, 'af_meta', true );

    $data = array_merge( $meta_order, $meta_user );
    $data['order_id'] = $order_id;
    $data['last_name'] = $customer->user_lastname;
    $data['first_name'] = $customer->user_firstname;
    $data['email'] = $customer->user_email;

    $data['primary_address'] = $order->get_billing_address_1();
    $data['secondary_address'] = $order->get_billing_address_2();
    $data['code_postal'] = $order->get_billing_postcode();
    $data['city'] = $order->get_billing_city();
    $data['country'] = get_country_name( $order->get_billing_country() );

    // PRODUCT DETAILS
    foreach ( $order->get_items() as $item_id => $item ) {
      $data['vehicle'] = $item->get_name();
    }

    //$data['price'] = wc_price( $data['price'], array( 'currency' => 'EUR' ) );
    //$data['total_price'] = wc_price( $order->get_total(), array( 'currency' => 'USD' ) );
    //$data['advance_price'] = wc_price( $order->get_total(), array( 'currency' => 'USD' ) );

    $data['total_price'] = $order->get_total();
    $data['advance_price'] = $order->get_total();

    /* CALCULATION OF FEES - NOT NEEDED
    foreach( $order->get_fees() as $fee ){
      if( strpos( strtolower( $fee->get_name() ), 'delivery' ) !== false ){
        $data['delivery_price'] = $fee->get_amount();
      }
      elseif( strpos( strtolower( $fee->get_name() ), 'accessories' ) !== false ){
        $data['accessories_price'] = $fee->get_amount();
      }
      else{
        $data['return_price'] = $fee->get_amount();
      }
    }
    $data['due_price'] = $data['total_price'] - $data['advance_price'];
    */

    $checkbox_fields = array( 'title', 'language', 'purpose' );
    foreach( $checkbox_fields as $checkbox_field ){
      if( isset( $data[ $checkbox_field ] ) ){
        $new_slug = strtolower( $data[ $checkbox_field ] ) . '_check';
        $data[ $new_slug ] = 'yes';
        unset( $data[ $checkbox_field ] );
      }
    }



    $data['date'] = date( "Y-m-d" );
    $data['place'] = 'Paris';
    return $data;
  }

  function generateContract( $order_id ){
    $data = $this->getDataForContract( $order_id );
    $this->test( $data );

    $newfileslug = 'af_contract_' . $data['order_id'];
    $pdf = PDF::getInstance();
    return $pdf->download( 'contract', $data, $newfileslug );
  }

}
