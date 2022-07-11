<?php

namespace WC_ADDON_FOR_AF;

class ORDER_ADMIN extends BASE{

  function __construct(){

    /*
    * UPDATE WooCommerce MENU ITEM to AUTOFRANCE STORE
    */
    add_action( 'admin_menu', function(){
      global $menu, $submenu;
      $menu['55.5'][0] = 'Auto France';
      $menu['55.5'][6] = 'dashicons-database-view';
    } );

    /*
    * ORDER META DETAILS
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

    /*
    * CREATE METABOXES FOR PAYMENTS AND CHECKLIST
    */
    add_action( 'add_meta_boxes', function(){
       add_meta_box( 'wc_payments', 'Payments', array( $this, 'metabox' ), 'shop_order' );
       add_meta_box( 'wc_checklist', 'Checklist', array( $this, 'metabox' ), 'shop_order' );
    } );

    /*
    * PROCESS INFORMATION ON ORDER FORM SUBMIT
    * META INFORMATION
    * PAYMENT INFORMATION
    * CHECKLIST INFORMATION
    */
    add_action( 'woocommerce_process_shop_order_meta', function( $order_id ){
      $fields = array( 'af_meta', 'af_payments', 'af_checklist' );

      // ITERATE THROUGH EACH FIELDS
      foreach( $fields as $key ){

        // DEFAULT DATA AS ARRAY
        $data = array();

        if( isset( $_POST[ $key ] ) ){

          // GET DATA FROM USER SELECTION VALUES
          $data = $_POST[ $key ];
        }

        // UPDATE TO POST META DATABASE
        update_post_meta( $order_id, $key, $data );
      }
    } );

  }



  function metabox( $post, $box ){

    if( isset( $box['id'] ) ){
      switch( $box['id'] ){

        case 'wc_payments':
          include( 'templates/payments.php' );
          break;

        case 'wc_checklist':
          include( 'templates/checklist.php' );
          break;
      }
    }

  }

  /*
  * ADDITIONAL LINKS OF THE PDF DOCUMENTS
  */
  function showLinks( $post_id ){
    $docs = array(
      'order_contract'  => 'View Contract',
      'order_statement' => 'View Statement',
      'order_invoice'   => 'View Invoice'
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
      'hierarchical' => false,
      'labels' => array(
        'name'              => _x( 'Locations', 'taxonomy general name' ),
        'singular_name'     => _x( 'Location', 'taxonomy singular name' ),
        'search_items'      =>  __( 'Search Locations' ),
        'all_items'         => __( 'All Locations' ),
        'parent_item'       => __( 'Parent Location' ),
        'parent_item_colon' => __( 'Parent Location:' ),
        'edit_item'         => __( 'Edit Location' ),
        'update_item'       => __( 'Update Location' ),
        'add_new_item'      => __( 'Add New Location' ),
        'new_item_name'     => __( 'New Location Name' ),
        'menu_name'         => __( 'Locations' ),
      ),
    ) );
  }


}

ORDER_ADMIN::getInstance();
