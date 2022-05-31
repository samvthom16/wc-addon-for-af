<?php

namespace WC_ADDON_FOR_AF;

class ADMIN extends BASE{

  function __construct(){

    add_action( 'admin_menu', array( $this, 'add_menu' ) );

    add_action( 'admin_enqueue_scripts', array( $this, 'loadAssets' ) );

    add_action( 'woocommerce_admin_order_data_after_order_details', function( $order ){
      include "templates/order_details.php";
    } );

    add_action( 'woocommerce_process_shop_order_meta', function( $order_id ){

      //echo "test";
      //wp_die();

      if( isset( $_POST['af_meta'] ) ){
        //$this->test( $_POST['af_meta'] );
        update_post_meta( $order_id, 'af_meta', $_POST['af_meta'] );
      }

      $payments = array();
      if( isset( $_POST['af_payments'] ) ){
        //$this->test( $_POST['af_meta'] );
        $payments = $_POST['af_payments'];
      }
      update_post_meta( $order_id, 'af_payments', $payments );

      $checklist = array();
      if( isset( $_POST['af_checklist'] ) ){
        //$this->test( $_POST['af_checklist'] );
        $checklist = $_POST['af_checklist'];
      }
      update_post_meta( $order_id, 'af_checklist', $checklist );
      //wp_die();


    } );




    add_action( 'woocommerce_order_actions', array( $this, 'add_order_actions' ) );

    add_action( 'woocommerce_order_action_af_generate_contract', array( $this, 'generateContract' ) );

    add_action( 'woocommerce_order_action_af_generate_invoice', array( $this, 'generateInvoice' ) );

    add_action( 'woocommerce_order_actions_start', function( $post_id ){

      $docs = array(
        '_af_contract' => 'View Contract',
        '_af_invoice'  => 'View Invoice'
      );

      $version = time();

      foreach( $docs as $slug => $label ){
        $doc_link = get_post_meta( $post_id, $slug, true );
        if( $doc_link ){
          echo "<li class='wide' style='border:none;'><a href='$doc_link?version=$version' target='_blank'>$label</a></li>";
        }
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

    add_action( 'add_meta_boxes', function(){
       add_meta_box( 'wc_payments', 'Payments', array( $this, 'metabox' ), 'shop_order' );
       add_meta_box( 'wc_checklist', 'Checklist', array( $this, 'metabox' ), 'shop_order' );
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

  function loadAssets(){
    wp_enqueue_script( 'wcaf-repeater', plugins_url( 'assets/js/repeater.js' , dirname(__FILE__) ), array( 'jquery' ), time() );
    wp_enqueue_script( 'wcaf-admin', plugins_url( 'assets/js/admin.js' , dirname(__FILE__) ), array( 'jquery', 'wcaf-repeater' ), time() );

    wp_enqueue_style( 'wcaf-admin', plugins_url( 'assets/css/admin.css' , dirname(__FILE__) ), array(), time() );
  }


}

ADMIN::getInstance();
