<?php

add_filter( 'woocommerce_is_sold_individually', function(){ return true; } );

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

    'date'    => array( 3, 4 ),
    'place'   => array( 3, 4 ),
  );

  return $fields;
} );

add_filter( 'af_pdf_fields_invoice', function( $fields ){

  $field_slugs = array(
    'date', 'order_id', 'peugeot_no',

    'name', 'email', 'primary_address', 'secondary_address', 'country',

    'vehicle', 'product_description', 'duration',

    'delivery_place', 'date_start', 'return_place', 'date_end',

    'price', 'accessories_price', 'discount', 'delivery_fee', 'drop_off_fee', 'total_price',

    'payment_rcvd_amount_1', 'payment_rcvd_date_1', 'payment_rcvd_amount_2', 'payment_rcvd_date_2',

    'balance_due', 'insurance_expiry'
  );

  foreach( $field_slugs as $field_slug ){
    $fields[ $field_slug ] = array();
  }

  return $fields;
} );



add_filter( 'af_pdf_filepath_contract', function( $filepath ){
  return AF_CONTRACT_TEMPLATE;
} );

add_filter( 'af_pdf_filepath_invoice', function( $filepath ){
  return AF_INVOICE_TEMPLATE;
} );

add_action( 'admin_menu', function(){
  global $menu, $submenu;

  /*
  echo '<pre>';
  print_r( $menu );
  echo '</pre>';
  wp_die();
  */

  // Change WooCommerce to Store
  $menu['55.5'][0] = 'Auto France';
  $menu['55.5'][6] = 'dashicons-database-view';
} );
