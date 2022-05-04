<?php
	/*
    Plugin Name: WC Add-on for Autofrance
    Plugin URI:
    Description: WooCommerce Add-on
    Author: Samuel Thomas
    Version: 1.0.0
    Author URI:
    */


defined( 'ABSPATH' ) || exit;

define( 'AF_CONTRACT_TEMPLATE', plugin_dir_path( __FILE__ ) . 'pdf/template-fixed.pdf' );

$inc_files = array(
  'lib/filters.php',
  'lib/class-base.php',
  'lib/class-pdf.php',
  'lib/class-admin.php',
  'lib/class-order.php',
  'lib/class-test.php',
);

foreach( $inc_files as $inc_file ){
  require_once( $inc_file );
}

/**
  * Add a standard $ value surcharge to all transactions in cart / checkout
**/
add_action( 'woocommerce_cart_calculate_fees','wc_add_surcharge' );
function wc_add_surcharge() {
  global $woocommerce;
  $fee = 1.00;
  $woocommerce->cart->add_fee( 'Surcharge', $fee, true, 'standard' );
}


function custom_remove_all_quantity_fields( $return, $product ) {return true;}
add_filter( 'woocommerce_is_sold_individually','custom_remove_all_quantity_fields', 10, 2 );

add_action( 'woocommerce_order_item_add_action_buttons', 'wc_order_item_add_action_buttons_callback', 10, 1 );
function wc_order_item_add_action_buttons_callback( $order ) {
    $label = esc_html__( 'Add Payment(s)', 'woocommerce' );
    $slug  = 'payment';
    ?>
    <button type="button" class="button <?php echo $slug; ?>-items"><?php echo $label; ?></button>
    <?php
}


function get_country_name( $country_code ){
  return WC()->countries->countries[ $country_code ];
}


/*
add_action( 'wp_ajax_af_test', function(){



  $filepath = plugin_dir_path( __FILE__ ) . 'pdf/template-fixed.pdf';

  //$pdf = PDF::getInstance(); //->download( $filepath, $value_fields );

  //echo $pdf;

  /*

  $pdf = new FPDM( $filepath );
  $pdf->useCheckboxParser = true;
  $pdf->Load( $value_fields, false ); // second parameter: false if field values are in ISO-8859-1, true if UTF-8
  $pdf->Merge();
  $pdf->Output();


  wp_die();
} );
*/

//https://github.com/woocommerce/woocommerce/blob/4.3.2/includes/class-wc-ajax.php#L1830-L1914
