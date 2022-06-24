<!-- https://rudrastyh.com/woocommerce/customize-order-details.html -->
<br class="clear" />
<br>
<h3>Details<a href="#" class="edit_address">Edit</a></h3>
<?php

  $meta = get_post_meta( $order->get_id(), 'af_meta', true );

  $fields = array(
    'ref_rep' => array(
      'type'  => 'text',
      'label' => 'Ref. of the Representative:'
    ),
    'ref_car' => array(
      'type'  => 'text',
      'label' => 'Ref. CAR-2-EUROPE:'
    ),
    'date_start' => array(
      'type'  => 'date',
      'label' => 'Delivery Date:'
    ),
    'delivery_place' => array(
      'type'  => 'select',
      'label' => 'Delivery Place:',
      'options' => array(
        ''  => 'Not Set',
      )
    ),
    'date_end' => array(
      'type'  => 'date',
      'label' => 'Return Date:'
    ),
    'return_place' => array(
      'type'  => 'select',
      'label' => 'Return Place:',
      'options' => array(
        ''  => 'Not Set',
      )
    ),
    'flight_no' => array(
      'type'  => 'text',
      'label' => 'Flight Number:'
    ),
    'time_flight' => array(
      'type'  => 'time',
      'label' => 'Flight Time:'
    ),
    'time_flight_slot' => array(
      'type'  => 'select',
      'label' => 'Flight Time Slot:',
      'options' => array(
        ''          => 'Not Set',
        'Morning'   => 'Morning',
        'Afternoon' => 'Afternoon'
      )
    ),
    'purpose' => array(
      'type'  => 'select',
      'label' => 'Purpose:',
      'options' => array(
        ''                => 'Not Set',
        'Tourist'         => 'Tourist',
        'Student'         => 'Student',
        'Mission Leaders' => 'Mission Leaders',
        'Journalist'      => 'Journalist'
      )
    ),
    'europe_address' => array(
      'type'  => 'text',
      'label' => 'Europe Address'
    ),
    'price' => array(
      'type'  => 'text',
      'label' => 'Total price of the car (Euros):'
    ),
    'accessories' => array(
      'type'  => 'checkboxes',
      'label' => 'Accessories:',
      'options' => array(
        'Snow Chains',
        'Anti-Slip Tire Socks',
        'Roof Bars',
        'Child Seat Rear Facing Upto 28lbs',
        'Child Seat 20-40 lbs',
        'Child Seat 33-80 lbs',
        'Public Charging Cable',
        'Fuel Fill Up'
      )
    ),
  );

?>

<div class="address" style='display:grid; grid-template-columns: 1fr 1fr;'>
  <?php foreach( $fields as $slug => $field ):?>
	<p>
    <strong><?php echo $field['label'];?></strong>
    <?php
      $setting_value = isset( $meta[ $slug ] ) ? $meta[ $slug ] : 'Not Set';
      echo apply_filters( $slug . '_af_setting_value', $setting_value );
    ?>
  </p>
  <?php endforeach;?>
</div>

<div class="edit_address">
  <?php
    foreach( $fields as $slug => $field ){

      $wc_field = array(
        'name'          => "af_meta[$slug]",
        'id'            => $slug,
        'label'         => $field['label'],
        'value'         => isset( $meta[ $slug ] ) ? $meta[ $slug ] : '',
        'wrapper_class' => 'form-field-wide',
      );

      if( $field['type'] == 'select' ){
        $wc_field['options'] = apply_filters( $slug . '_af_options', $field['options'] );
        woocommerce_wp_select( $wc_field );
      }
      elseif ( $field['type'] == 'checkboxes' ) {
        echo "<p class='form-field'>" . $field['label'] . "</p>";
        $wc_field['name'] = $wc_field['name'] . '[]';
        $wc_field['selected'] = $wc_field['value'];
        $wc_field['options'] = apply_filters( $slug . '_af_options', $field['options'] );
        af_checkboxes( $wc_field );
      }
      else{
        $wc_field['type'] = $field['type'];
        woocommerce_wp_text_input( $wc_field );
      }
    }
  ?>
</div>
