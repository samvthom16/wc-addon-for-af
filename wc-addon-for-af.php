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

define( 'AF_CONTRACT_TEMPLATE', plugin_dir_path( __FILE__ ) . 'pdf/contract-fixed.pdf' );
define( 'AF_INVOICE_TEMPLATE', plugin_dir_path( __FILE__ ) . 'pdf/invoice-fixed.pdf' );
define( 'AF_STATEMENT_TEMPLATE', plugin_dir_path( __FILE__ ) . 'pdf/statement-fixed.pdf' );

$inc_files = array(
  'lib/filters.php',
  'lib/functions.php',
  'lib/class-base.php',
  'lib/class-pdf.php',
  'lib/class-admin.php',
  'lib/class-order-admin.php',
  'lib/class-order.php',
  'lib/class-test.php',
);

foreach( $inc_files as $inc_file ){
  require_once( $inc_file );
}





/*
add_action( 'woocommerce_order_item_add_action_buttons', 'wc_order_item_add_action_buttons_callback', 10, 1 );
function wc_order_item_add_action_buttons_callback( $order ) {
    $label = esc_html__( 'Add Payment(s)', 'woocommerce' );
    $slug  = 'payment';
    ?>
    <button type="button" class="button <?php echo $slug; ?>-items"><?php echo $label; ?></button>
    <?php
}
*/

function get_country_name( $country_code ){
  return WC()->countries->countries[ $country_code ];
}

function get_state_name( $country, $state_code ){
  return WC()->countries->get_states( $country )[ $state_code ];
}


add_action( 'woocommerce_new_order', 'create_fees_for_wc_order',  1, 1  );
function create_fees_for_wc_order( $order_id ) {
  $order = new WC_Order( $order_id );
  \WC_ADDON_FOR_AF\ORDER::getInstance()->setFeesByDefault( $order );
}


//print_r(  );

/*
add_action( 'admin_init', function(){

  if( is_admin( 'post.php' ) && isset( $_GET['post'] ) && $_GET['post'] ){

    try{
      $order_id = $_GET['post'];
      $order = new WC_ORDER( $order_id );

      \WC_ADDON_FOR_AF\ORDER::getInstance()->setFeesByDefault( $order );
    }
    catch( Exception $e ){

    }
  }
} );


/*
add_filter( 'woocommerce_order_get_items', function( $items, $obj, $types ){
  echo '<pre>';
  print_r( $items );
  echo '</pre>';
  return $items;
}, 10, 3 );
*/
