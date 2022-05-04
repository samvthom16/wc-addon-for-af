<?php add_thickbox(); ?>
<p class='form-field form-field-wide wc-customer-user'>
  <!--a href="#TB_inline?width=600&height=450&inlineId=modal-window-id" class="thickbox">Add Customer</a-->
  <a href="http://localhost/anurag/wp-admin/admin.php?page=new-wc-customer" class="thickbox">Add Customer</a-->
</p>

<div id="modal-window-id" style="display:none;">
  <?php
    $form_fields = array(
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
      )
    );
  ?>
  <h2>Add Customer</h2>

  <table class='form-table' role='presentation'><tbody>

  <?php foreach( $form_fields as $slug => $form_field ):?>
    <tr class='row-<?php echo $slug;?>'>
      <th scope='row'>
        <label for='<?php echo $slug;?>'><?php echo $form_field['label'];?></label>
      </th>
      <td>
        <input id='<?php echo $slug;?>' required='true' class='regular-text' type='text' name='<?php echo $slug;?>' value='<?php echo isset( $form_field['default'] ) ? $form_field['default'] : '';?>' />
      </td>
    </tr>
  <?php endforeach;?>
  </tbody></table>
</div>
