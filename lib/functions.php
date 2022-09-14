<?php

/*
* DISPLAY CHECKBOXES
*/
function af_checkboxes( $field ){
  \WC_ADDON_FOR_AF\ADMIN::getInstance()->displayCheckboxes( $field );
}

/*
* DISPLAY ACCESSORIES
* SHOW COMMA SEPERATED VALUES FOR EACH ARRAY ITEM
*/
function af_filter_setting_value_accessories( $accessories ){
  if( is_array( $accessories ) ) return implode( ', ', $accessories );
  if( $accessories ) return $accessories;
  return 'Not Set';
}

/*
* DISPLAY DELIVERY AND RETURN PLACE
* SHOW TERM NAME FOR TERM ID PASSEd
*/
function af_filter_setting_value_delivery_and_return_place( $term_id ){
  $term = get_term( $term_id );
  return $term->name;
}

/*
* DROPDOWN OPTIONS FOR DELIVERY AND RETURN PLACE
* TERMS OF TAXONOMY: LOCATION
*/
function af_filter_dropdown_delivery_and_return_place( $options ){
  $locations = get_terms( array(
    'taxonomy'    => 'location',
    'hide_empty'  => false
  ) );

  foreach( $locations as $location ){
    $options[ $location->term_id ] = $location->name;
  }
  return $options;
}

/*
* PDF FIELDS FOR THE INVOICE & STATEMENT
*/
function af_pdf_fields_invoice_statement( $fields ){
  $field_slugs = array(
    'date', 'order_id', 'ref_rep', 'ref_car',

    'name', 'address_information',

    //'primary_address', 'secondary_address', 'city_state', 'country',

    'vehicle', 'product_description', 'duration', 'accessories',

    //'delivery_place', 'date_start',

    'delivery_remark',

    //'return_place', 'date_end',

    'return_remark',

    'price', 'accessories_price', 'discount', 'delivery_fee', 'drop_off_fee', 'total_price',

    'payment_rcvd_amount_1', 'payment_rcvd_date_1', 'payment_rcvd_amount_2', 'payment_rcvd_date_2',

    'balance_due', 'insurance_expiry',

    'date_end'
  );

  foreach( $field_slugs as $field_slug ){
    $fields[ $field_slug ] = array();
  }

  //$fields['date_end'] = array( 1 );

  return $fields;
}

function af_combine_field( $fields, $data, $seperator = "\r\n" ){
  $combined_data_field = '';
  $i = 0;
  foreach( $fields as $field ){
    if( isset( $data[ $field ] ) && $data[ $field ] ){
      if( $i ) $combined_data_field .= $seperator;
      $combined_data_field .= $data[ $field ];
      $i++;
    }
  }



  return $combined_data_field;
}

function af_data_invoice_statement( $data ){

  //$data['city_state'] = af_combine_field( array( 'city', 'state' ), $data, ', ' );
  $data['country'] = af_combine_field( array( 'city', 'state_code', 'code_postal' ), $data, ' ' );
  $data['address_information'] = af_combine_field( array( 'primary_address', 'secondary_address', 'country' ), $data );

  $data['delivery_title'] = af_combine_field( array( 'date_start', 'delivery_place' ), $data, ', ' );
  $data['delivery_remark'] = $data['delivery_title'] . "\r\n\r\n" . $data['delivery_place_remark'];
  $data['return_title'] = af_combine_field( array( 'date_end', 'return_place' ), $data, ', ' );
  $data['return_remark'] = $data['return_title'] . "\r\n\r\n" . $data['return_place_remark'];


  // CONCATENATING USD BEFORE ALL THE PRICES
  $data['price'] = $data['subtotal_price'];
  $price_fields = array(
    'price', 'accessories_price', 'delivery_fee', 'drop_off_fee', 'total_price', 'discount',
    'payment_rcvd_amount_1', 'payment_rcvd_amount_2', 'payment_rcvd_amount_3', 'balance_due'
  );
  foreach( $price_fields as $price_field ){
    if( isset( $data[ $price_field ] ) && $data[ $price_field ] ){
      $data[ $price_field ] = 'USD ' . $data[ $price_field ];
    }
  }

  //$data['discount'] = 100;

  return $data;
}
