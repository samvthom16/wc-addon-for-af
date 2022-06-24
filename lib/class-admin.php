<?php

namespace WC_ADDON_FOR_AF;

class ADMIN extends BASE{

  function __construct(){

    add_action( 'admin_menu', array( $this, 'add_menu' ) );

    add_action( 'admin_enqueue_scripts', array( $this, 'loadAssets' ) );



    






    /*
    * REDIRECT TO NEW CUSTOMER FORM IF THE DEFAULT WOOCOMMERCE CUSTOMER FORM IS VISITED
    */
    add_action( 'admin_init', function(){

      if( is_admin( 'user-edit.php' ) && isset( $_GET['user_id'] ) && $_GET['user_id'] ){
        $user_id = $_GET['user_id'];

        $user_meta = get_userdata($user_id);

        if( in_array( 'customer', $user_meta->roles ) ){
          $url = admin_url( "admin.php?page=new-wc-customer&id=$user_id" );
          wp_redirect( $url );
          exit;

        }
      }
    } );




  }



  function add_menu(){

    add_submenu_page(
      'woocommerce',
      'Add Customer',
      'Add Customer',
      'manage_woocommerce',
      'new-wc-customer',
      array( $this, 'submenu_page' )
    );

    add_submenu_page(
      'woocommerce',
      'Locations',
      'Locations',
      'manage_woocommerce',
      'order-locations',
      array( $this, 'submenu_page' )
    );

   }



  function submenu_page(){

    if( isset( $_GET['page'] ) && $_GET['page'] == 'new-wc-customer' ){
      include( 'templates/new_customer.php' );
    }

    if( isset( $_GET['page'] ) && $_GET['page'] == 'order-locations' ){

      $url = admin_url( 'edit-tags.php?taxonomy=location&post_type=shop_order' );

      /* REDIRECT VIA JS */
			_e("<script>location.href='" . $url . "';</script>");
    }

  }



  /*
  function add_order_actions( $actions ){
    $actions = array();
    $actions['af_generate_contract'] = 'Regenerate Contract';
    $actions['af_generate_invoice'] = 'Regenerate Invoice';
	  return $actions;
  }

  function generateContract( $order ){

    $contract_link = ORDER::getInstance()->generateContract( $order->id );

    if( $contract_link ){
      $message = 'Contract generated';
  	  $order->add_order_note( $message, 0, true );
      update_post_meta( $order->id, '_af_contract', $contract_link );
    }

  }

  function generateInvoice( $order ){

    $invoice_link = ORDER::getInstance()->generateInvoice( $order->id );

    if( $invoice_link ){
      $message = 'Invoice generated';
  	  $order->add_order_note( $message, 0, true );
      update_post_meta( $order->id, '_af_invoice', $invoice_link );
    }

  }
  */

  function loadAssets(){
    wp_enqueue_script( 'wcaf-repeater', plugins_url( 'assets/js/repeater.js' , dirname(__FILE__) ), array( 'jquery' ), time() );
    wp_enqueue_script( 'wcaf-admin', plugins_url( 'assets/js/admin.js' , dirname(__FILE__) ), array( 'jquery', 'wcaf-repeater' ), time() );

    wp_localize_script( 'wcaf-admin', 'billing_meta_data', array(
      'country_states' => array_merge( WC()->countries->get_allowed_country_states(), WC()->countries->get_shipping_country_states() )
    ) );

    wp_enqueue_style( 'wcaf-admin', plugins_url( 'assets/css/admin.css' , dirname(__FILE__) ), array(), time() );
  }

  function displayCheckboxes( $field ){
    $field['selected'] = isset( $field['selected'] ) && $field['selected'] ? $field['selected'] : array();

    if( is_array( $field['options'] ) ){
      foreach( $field['options'] as $option ){
        woocommerce_wp_checkbox( array(
          'label'         => $option,
          'wrapper_class' => 'af-checkbox',
          'name'          => $field['name'],
          'cbvalue'       => $option,
          'value'         => in_array( $option, $field['selected'] ) ? $option : ''
        ) );
      }
    }
  }


}

ADMIN::getInstance();
