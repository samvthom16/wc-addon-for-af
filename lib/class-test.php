<?php

namespace WC_ADDON_FOR_AF;


class TEST extends BASE{

  function __construct(){
    add_action( 'wp_ajax_af_test', array( $this, 'ajaxCallback' ) );
    add_action( 'af_test_contract', array( $this, 'downloadContract' ) );
    add_action( 'af_test_order_contract', array( $this, 'downloadOrderContract' ) );
  }

  function ajaxCallback(){
    if( isset( $_GET[ 'test' ] ) ){
      do_action( 'af_test_' . $_GET[ 'test' ] );
    }
    wp_die();
  }

  function downloadContract(){

    $values = array(
      'ref_rep'   => '247623',
      'ref_car'   => '11508479',

      'mr_check'      => 'yes',
      'mrs_check'     => '',
      'miss_check'    => '',

      'last_name'     => 'HENRY',
      'first_name'    => 'RICHARD',
      'birth_country' => 'FRANCE',
      'nationality'   => 'UNITED STATES',
      'telephone'     => '(301) 655-3116',
      'passport_check'=> 'yes',
      'passport_no'   => '668906889',
      'date_issue'    => 'JUL 14, 2021',

      'fr_check'      => '',
      'en_check'      => 'yes',
      'es_check'      => '',
      'pt_check'      => '',

      'dob'           => 'JAN 28, 1950',
      'birth_city'    => 'PARIS',
      'profession'    => 'ECONOMIST',
      'email'         => 'HRICHARD27@GMAIL.COM',
      'place_issue'   => 'US STATE DEPARTMENT',

      'primary_address'   => '6710 BRADLEY BLVD',
      'secondary_address' => '',
      'code_postal'       => '20817',
      'city'              => 'BETHESDA MD',
      'country'           => 'UNITED STATES',
      'europe_address'    => 'Europe Address: XXX',

      'tourist_check'     => 'yes',
      'student_check'     => '',
      'interns_check'     => '',
      'mission_check'     => '',
      'journalist_check'  => '',

      'vehicle'         => 'G21 - 208 - DIESEL - BVM',
      'accessories'     => 'LOYALTY - FREE FUEL FILL UP, REPEAT CLIENT DISCOUNT',
      'observations'    => 'A/C, RADIO, MP3 READY, AUTOMATIC, GPS',
      'date_start'      => 'DEC 20, 2021',
      'date_end'        => 'JAN 18, 2022',
      'duration'        => '30',
      'delivery_place'  => 'PARIS - CDG AIRPORT',
      'flight_no'       => 'AF # 0055',
      'time_flight'     => '08:05 AM',
      'morning_check'   => 'yes',
      'afternoon_check' => '',
      'return_place'    => 'MARSEILLE AIRPORT',

      'price'             => '47,167',
      'advance_price'     => '1,979',
      'accessories_price' => '0',
      'delivery_price'    => '0',
      'return_price'      => '0',
      'total_price'       => '47167',
      'due_price'         => '45000',

      'date'    => '29 April 2022',
      'place'   => 'PARIS',
    );

    $pdf = PDF::getInstance();
    echo $pdf->download( 'contract', $values );
  }

  function downloadOrderContract(){
    if( isset( $_GET[ 'order_id' ] ) ){
      echo ORDER::getInstance()->generateContract( $_GET[ 'order_id' ] );
    }
  }

}

TEST::getInstance();
