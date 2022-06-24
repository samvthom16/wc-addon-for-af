<?php



  if( $_POST ){
    $meta = $_POST['meta'];
    $billing = $_POST['billing'];
    unset( $_POST['meta'] );
    unset( $_POST['billing'] );

    //$this->test( $billing );

    $_POST['user_login'] = $_POST['user_email'];
    $_POST['display_name'] = $_POST['first_name'] . ' ' . $_POST['last_name'];
    $wp_error = wp_insert_user( $_POST );

    if( !is_wp_error( $wp_error ) ){
      update_user_meta( $wp_error, 'af_meta', $meta );

      if( is_array( $billing ) && !empty( $billing ) ){
        $billing['billing_first_name'] = $_POST['first_name'];
        $billing['billing_last_name'] = $_POST['last_name'];
        $billing['billing_phone'] = isset( $meta['telephone'] ) ? $meta['telephone'] : '';
        $billing['billing_email'] = $_POST['user_email'];
        foreach( $billing as $key => $value ){
          update_user_meta( $wp_error, $key, $value );
        }
      }

      echo '<div class="notice notice-info is-dismissible"><p>Customer has been updated.</p></div>';
    }
    else{
      foreach( $wp_error->errors as $errors ){
        $errormsg = $errors[0];
      }
      echo "<div class='notice notice-error is-dismissible'><p>$errormsg</p></div>";
    }



    //print_r( $wp_error );
  }

  $countries_object  =   new WC_Countries();
  $countries         =   $countries_object->__get('countries');

  $countries_states = array_merge( WC()->countries->get_allowed_country_states(), WC()->countries->get_shipping_country_states() );

  //$this->test( $states );


  $form_fields = array(
    'ID'  => array(
      'label'   => 'ID',
      'type'    => 'text',
    ),
    'meta[title]' => array(
      'label' => 'Title',
      'type'  => 'select',
      'options' => array(
        'Mr'  => 'Mr',
        'Mrs'  => 'Mrs',
        'Miss'  => 'Miss',
      ),
    ),
    'meta[language]' => array(
      'label' => 'Language',
      'type'  => 'select',
      'options' => array(
        'FR'  => 'FR',
        'EN'  => 'EN',
        'ES'  => 'ES',
        'PT'  => 'PT'
      ),
    ),
    'first_name' => array(
      'label' => 'First Name',
      'type'  => 'text'
    ),
    'last_name' => array(
      'label' => 'Last Name',
      'type'  => 'text'
    ),
    'user_email' => array(
      'label' => 'Email Address',
      'type'  => 'text'
    ),
    'role' => array(
      'label'   => 'Role',
      'type'    => 'text',
    ),
    'meta[dob]' => array(
      'label'   => 'Date Of Birth',
      'type'    => 'date'
    ),
    'meta[birth_country]' => array(
      'label'   => 'Country of Birth',
      'type'    => 'text'
    ),
    'meta[birth_city]' => array(
      'label'   => 'City of Birth',
      'type'    => 'text'
    ),
    'meta[nationality]' => array(
      'label'   => 'Nationality',
      'type'    => 'text'
    ),
    'meta[telephone]' => array(
      'label'   => 'Telephone',
      'type'    => 'text'
    ),
    'meta[passport_no]' => array(
      'label'   => 'Passport Number',
      'type'    => 'text'
    ),
    'meta[date_issue]' => array(
      'label'   => 'Passport Issue Date',
      'type'    => 'date'
    ),
    'meta[place_issue]' => array(
      'label'   => 'Passport Issue Place',
      'type'    => 'text'
    ),
    'meta[profession]' => array(
      'label'   => 'Profession',
      'type'    => 'text'
    ),
    'billing[billing_address_1]' => array(
      'label'   => 'Billing Address Line 1',
      'type'    => 'text'
    ),
    'billing[billing_address_2]' => array(
      'label'   => 'Billing Address Line 2',
      'type'    => 'text'
    ),
    'billing[billing_city]' => array(
      'label'   => 'Billing City',
      'type'    => 'text'
    ),
    'billing[billing_postcode]' => array(
      'label'   => 'Billing Postcode',
      'type'    => 'text'
    ),
    'billing[billing_state]' => array(
      'id'      => 'billing_state',
      'label'   => 'Billing State',
      'type'    => 'select'
    ),
    'billing[billing_country]' => array(
      'id'      => 'billing_country',
      'label'   => 'Billing Country',
      'type'    => 'select',
      'options' => $countries
    ),
  );

  $data = array();

  $data['role'] = 'customer';

  if( isset( $_GET[ 'id' ] ) && $_GET[ 'id' ] ){
    $user = get_user_by( 'ID', $_GET[ 'id' ] );

    $data['ID'] = $_GET[ 'id' ];

    $data['first_name'] = $user->user_firstname;
    $data['last_name'] = $user->user_lastname;
    $data['user_email'] = $user->user_email;

    $meta = get_user_meta( $_GET[ 'id' ], 'af_meta', true );
    if( is_array( $meta ) && count( $meta ) ){
      foreach( $meta as $slug => $value ){
        $data["meta[$slug]"] = $value;
      }
    }

    $billing_fields = array(
      'billing_address_1',
      'billing_address_2',
      'billing_city',
      'billing_country',
      'billing_postcode',
      'billing_state'
    );
    foreach( $billing_fields as $billing_field ){
      $value = get_user_meta( $_GET[ 'id' ], $billing_field, true );
      $data["billing[$billing_field]"] = $value;
    }

    //$this->test( $data );

  }

  $button_text = isset( $_GET['id'] ) ? 'Update Customer' : 'Add Customer';


?>
<div class='wrap'>
  <h1><?php echo $button_text;?></h1>
  <form method='POST'>
    <div style='display: grid; grid-template-columns: 1fr 1fr;'>
      <?php
        //$this->test( $data );

        foreach( $form_fields as $slug => $field ){
          $wc_field = array(
            'id'            => isset( $field['id'] ) ? $field['id'] : $slug,
            'name'          => $slug,
            'label'         => $field['label'],
            'value'         => isset( $data[ $slug ] ) ? $data[ $slug ] : '',
            'wrapper_class' => 'form-field-wide',
          );

          if( $field['type'] == 'select' ){
            $wc_field['options'] = $field['options'];
            woocommerce_wp_select( $wc_field );
          }
          else{
            $wc_field['type'] = $field['type'];
            woocommerce_wp_text_input( $wc_field );
          }
        }
      ?>
    </div>
    <p style='margin-top: 30px;'>
      <input class='button button-primary' type='submit' value='<?php echo $button_text;?>' />
    </p>
  </form>
</div>
<script>
  window.selectedState = '<?php echo $data['billing[billing_state]']?>';
</script>
<style>
  .role_field, .ID_field{
    display: none;
  }
  label{ display: block; }
</style>
