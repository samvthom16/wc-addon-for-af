<?php

namespace WC_ADDON_FOR_AF;

class ADMIN extends BASE{

  function __construct(){
    add_action( 'admin_menu', array( $this, 'add_menu' ) );


    add_action( 'woocommerce_admin_order_data_after_order_details', function( $order ){
      include "templates/order_details.php";
    } );

    add_action( 'woocommerce_process_shop_order_meta', function( $order_id ){
      if( isset( $_POST['af_meta'] ) ){
        //$this->test( $_POST['af_meta'] );
        update_post_meta( $order_id, 'af_meta', $_POST['af_meta'] );
      }
    } );




    add_action( 'woocommerce_order_actions', array( $this, 'add_order_actions' ) );

    add_action( 'woocommerce_order_action_af_generate_contract', array( $this, 'generateContract' ) );

    add_action( 'woocommerce_order_actions_start', function( $post_id ){
      $contract_link = get_post_meta( $post_id, '_af_contract', true );
      if( $contract_link ){
        echo "<li class='wide' style='border:none;'><a href='$contract_link' target='_blank'>View Contract</a></li>";
      }
    } );

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
   }

  function submenu_page(){
    include( 'templates/new_customer.php' );
  }

  function add_order_actions( $actions ){
    $actions = array();
    $actions['af_generate_contract'] = 'Regenerate Contract';
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


}

ADMIN::getInstance();
