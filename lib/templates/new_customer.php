<?php



  if( $_POST ){
    $meta = $_POST['meta'];
    unset( $_POST['meta'] );

    $_POST['user_login'] = $_POST['user_email'];
    $wp_error = wp_insert_user( $_POST );

    if( !is_wp_error( $wp_error ) ){
      update_user_meta( $wp_error, 'af_meta', $meta );
      echo '<div class="notice notice-info is-dismissible"><p>Customer has been added.</p></div>';
    }
    else{
      foreach( $wp_error->errors as $errors ){
        $errormsg = $errors[0];
      }
      echo "<div class='notice notice-error is-dismissible'><p>$errormsg</p></div>";
    }



    //print_r( $wp_error );
  }


  $form_fields = array(
    'ID'  => array(
      'label'   => 'ID',
      'type'    => 'text',
      'default' => 0
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
      'default' => 'customer'
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
  );

  if( isset( $_GET[ 'id' ] ) && $_GET[ 'id' ] ){
    $user = get_user_by( 'ID', $_GET[ 'id' ] );

    $form_fields['ID']['default'] = $_GET[ 'id' ];
    $form_fields['first_name']['default'] = $user->user_firstname;
    $form_fields['last_name']['default'] = $user->user_lastname;
    $form_fields['user_email']['default'] = $user->user_email;

    $meta = get_user_meta( $_GET[ 'id' ], 'af_meta', true );
    if( is_array( $meta ) && count( $meta ) ){
      foreach( $meta as $slug => $value ){
        $form_fields["meta[$slug]"]['default'] = $value;
      }
    }

  }

  $button_text = isset( $_GET['id'] ) ? 'Update Customer' : 'Add Customer';


?>
<div class='wrap'>
  <h1><?php echo $button_text;?></h1>
  <form method='POST'>
    <table class='form-table' role='presentation'><tbody>

    <?php foreach( $form_fields as $slug => $form_field ):?>
      <tr class='row-<?php echo $slug;?>'>
        <th scope='row'>
          <label for='<?php echo $slug;?>'><?php echo $form_field['label'];?></label>
        </th>
        <td>
          <input id='<?php echo $slug;?>' class='regular-text' type='<?php echo $form_field['type'];?>' name='<?php echo $slug;?>' value='<?php echo isset( $form_field['default'] ) ? $form_field['default'] : '';?>' />
        </td>
      </tr>
    <?php endforeach;?>
    </tbody></table>
    <p style='margin-top: 30px;'>
      <input class='button button-primary' type='submit' value='<?php echo $button_text;?>' />
    </p>
  </form>
</div>
<style>
  .row-role, .row-ID{
    display: none;
  }
</style>
