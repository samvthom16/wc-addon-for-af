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

function custom_change_admin_label() {
    global $menu, $submenu;

    // Change WooCommerce to Store
    $menu['55.5'][0] = 'Autofrance';
}
add_action( 'admin_menu', 'custom_change_admin_label' );
