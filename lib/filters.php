<?php

/*
* SET QUANTITY OF THE CART TO 1
*/
add_filter( 'woocommerce_is_sold_individually', function(){ return true; } );

/*
* PDF FIELDS FOR THE CONTRACT
*/
add_filter( 'af_pdf_fields_contract', function( $fields ){

  $fields = array(
    'ref_rep'   => array( 1, 3, 4 ),
    'ref_car'   => array( 1, 3, 4 ),

    'mr_check'      => array( 1, 3, 4 ),
    'mrs_check'     => array( 1, 3, 4 ),
    'miss_check'    => array( 1, 3, 4 ),

    'last_name'     => array( 1, 3, 4 ),
    'first_name'    => array( 1, 3, 4 ),
    'birth_country' => array( 1, 3, 4 ),
    'nationality'   => array( 1, 3, 4 ),
    'telephone'     => array( 1, 3, 4 ),
    'passport_check'=> array( 1, 3, 4 ),
    'mobile_check'  => array( 1, 3, 4 ),
    'passport_no'   => array( 1, 3, 4 ),
    'date_issue'    => array( 1, 3, 4 ),

    'fr_check'      => array( 1, 3, 4 ),
    'en_check'      => array( 1, 3, 4 ),
    'es_check'      => array( 1, 3, 4 ),
    'pt_check'      => array( 1, 3, 4 ),

    'dob'           => array( 1, 3, 4 ),
    'birth_city'    => array( 1, 3, 4 ),
    'profession'    => array( 1, 3, 4 ),
    'email'         => array( 1, 3, 4 ),
    'place_issue'   => array( 1, 3, 4 ),

    'primary_address'   => array( 1, 3, 4 ),
    'secondary_address' => array( 1, 3, 4 ),
    'code_postal'       => array( 1, 3, 4 ),
    'city'              => array( 1, 3, 4 ),
    'country'           => array( 1, 3, 4 ),
    'europe_address'    => array( 1, 3, 4 ),

    'tourist_check'     => array( 1, 3, 4 ),
    'student_check'     => array( 1, 3, 4 ),
    'interns_check'     => array( 1, 3, 4 ),
    'mission_check'     => array( 1, 3, 4 ),
    'journalist_check'  => array( 1, 3, 4 ),

    'vehicle'         => array( 3, 4 ),
    'accessories'     => array( 3, 4 ),
    'observations'    => array( 3, 4 ),

    'date_start'      => array( 3, 4 ),
    'date_end'        => array( 3, 4 ),
    'duration'        => array( 3, 4 ),
    'delivery_place'  => array( 3, 4 ),
    'flight_no'       => array( 3, 4 ),
    'time_flight'     => array( 3, 4 ),
    'morning_check'   => array( 3, 4 ),
    'afternoon_check' => array( 3, 4 ),
    'return_place'    => array( 3, 4 ),

    'price'             => array( 3, 4 ),
    'advance_price'     => array( 3, 4 ),
    'accessories_price' => array( 3, 4 ),
    'delivery_price'    => array( 3, 4 ),
    'return_price'      => array( 3, 4 ),
    'total_price'       => array( 3, 4 ),
    'due_price'         => array( 3, 4 ),

    //'date'    => array( 3, 4 ),
    //'place'   => array( 3, 4 ),
  );

  return $fields;
} );

/*
* PDF FIELDS FOR THE INVOICE
*/
add_filter( 'af_pdf_fields_invoice', function( $fields ){

  $field_slugs = array(
    'date', 'order_id', 'ref_rep', 'ref_car',

    'name', 'primary_address', 'secondary_address', 'city_state', 'country',

    'vehicle', 'product_description', 'duration', 'accessories',

    'delivery_place', 'date_start', 'delivery_remark',

    'return_place', 'date_end', 'return_remark',

    'price', 'accessories_price', 'discount', 'delivery_fee', 'drop_off_fee', 'total_price',

    'payment_rcvd_amount_1', 'payment_rcvd_date_1', 'payment_rcvd_amount_2', 'payment_rcvd_date_2',

    'balance_due', 'insurance_expiry'
  );

  foreach( $field_slugs as $field_slug ){
    $fields[ $field_slug ] = array();
  }

  $fields['date_end'] = array( 1 );

  return $fields;
} );


/*
* PDF FILEPATH FOR THE CONTRACT
*/
add_filter( 'af_pdf_filepath_contract', function( $filepath ){
  return AF_CONTRACT_TEMPLATE;
} );

/*
* PDF FILEPATH FOR THE INVOICE
*/
add_filter( 'af_pdf_filepath_invoice', function( $filepath ){
  return AF_INVOICE_TEMPLATE;
} );



/*
* DROPDOWN OPTIONS FOR RELIVERY AND RETURN PLACES
*/
add_filter( 'delivery_place_af_options', 'af_filter_dropdown_delivery_and_return_place' );
add_filter( 'return_place_af_options', 'af_filter_dropdown_delivery_and_return_place' );

/*
* DISPLAY SETTING VALUE
*/
add_filter( 'delivery_place_af_setting_value', 'af_filter_setting_value_delivery_and_return_place' );
add_filter( 'return_place_af_setting_value', 'af_filter_setting_value_delivery_and_return_place' );
add_filter( 'accessories_af_setting_value', 'af_filter_setting_value_accessories' );
add_filter( 'price_af_setting_value', 'number_format' );

add_filter( 'af_data_invoice', function( $data ){

  $data['price'] = $data['subtotal_price'];
  $data['delivery_remark'] = $data['delivery_place_remark'];
  $data['return_remark'] = $data['return_place_remark'];

  //echo "<pre>";
  //print_r( $data );
  //echo "</pre>";



  $data['city_state'] = $data['city'] . ', ' . $data['state'];
  return $data;
} );

add_filter( 'af_data_contract', function( $data ){

  $data['price'] = number_format( $data['price'] );

  $checkbox_fields = array( 'title', 'language', 'purpose' );
  foreach( $checkbox_fields as $checkbox_field ){
    if( isset( $data[ $checkbox_field ] ) ){
      $new_slug = strtolower( $data[ $checkbox_field ] ) . '_check';
      $data[ $new_slug ] = 'yes';
      unset( $data[ $checkbox_field ] );
    }
  }

  if( isset( $data['product_description'] ) ){
    $data['observations'] = $data['product_description'];
  }

  if( isset( $data['time_flight_slot'] ) ){
    $temp_key = strtolower( $data['time_flight_slot'] ) . '_check';
    $data[ $temp_key ] = 'yes';
    unset( $data['time_flight_slot'] );
  }

  $data['passport_check'] = 'yes';
  $data['mobile_check'] = 'yes';
  //$data['home_check'] = 'no';

  return $data;
} );
