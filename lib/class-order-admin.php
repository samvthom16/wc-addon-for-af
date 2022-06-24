<?php

namespace WC_ADDON_FOR_AF;

class ORDER_ADMIN extends BASE{

  function __construct(){

    add_action( 'admin_menu', function(){
      global $menu, $submenu;

      // Change WooCommerce to Store
      $menu['55.5'][0] = 'Auto France';
      $menu['55.5'][6] = 'dashicons-database-view';
    } );

    /*
    * ORDER DETAILS
    */
    add_action( 'woocommerce_admin_order_data_after_order_details', function( $order ){
      include "templates/order_details.php";
    } );

    /*
    * ADDITIONAL LINKS OF THE PDF DOCUMENTS
    */
    add_action( 'woocommerce_order_actions_start', array( $this, 'showLinks' ) );

    /*
    * LOCATIONS TAXONOMY FOR DROP-OFF & PICK-UP
    */
    add_action( 'init', array( $this, 'addLocationTaxonomy' ) );

  }

  /*
  * ADDITIONAL LINKS OF THE PDF DOCUMENTS
  */
  function showLinks( $post_id ){
    $docs = array(
      'order_contract' => 'View Contract',
      'order_invoice'  => 'View Invoice'
    );

    $version = time();

    foreach( $docs as $slug => $label ){
      $doc_link = admin_url( "admin-ajax.php?action=af_test&test=$slug&order_id=$post_id&version=$version" );
      if( $doc_link ){
        echo "<li class='wide' style='border:none;padding-bottom: 0;'><a href='$doc_link' target='_blank'>$label</a></li>";
      }
    }
  }

  /*
  * LOCATIONS TAXONOMY FOR DROP-OFF & PICK-UP
  */
  function addLocationTaxonomy(){

    register_taxonomy( 'location', 'shop_order', array(
      // Hierarchical taxonomy (like categories)
      'hierarchical' => false,
      // This array of options controls the labels displayed in the WordPress Admin UI
      'labels' => array(
        'name' => _x( 'Locations', 'taxonomy general name' ),
        'singular_name' => _x( 'Location', 'taxonomy singular name' ),
        'search_items' =>  __( 'Search Locations' ),
        'all_items' => __( 'All Locations' ),
        'parent_item' => __( 'Parent Location' ),
        'parent_item_colon' => __( 'Parent Location:' ),
        'edit_item' => __( 'Edit Location' ),
        'update_item' => __( 'Update Location' ),
        'add_new_item' => __( 'Add New Location' ),
        'new_item_name' => __( 'New Location Name' ),
        'menu_name' => __( 'Locations' ),
      ),
      // Control the slugs used for this taxonomy
      'rewrite' => array(
        //'slug' => 'locations', // This controls the base slug that will display before each term
        //'with_front' => false, // Don't display the category base before "/locations/"
        //'hierarchical' => true // This will allow URL's like "/locations/boston/cambridge/"
      ),
    ) );
  }


}

ORDER_ADMIN::getInstance();
