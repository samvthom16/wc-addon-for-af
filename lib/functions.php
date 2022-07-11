<?php

/*
* DISPLAY CHECKBOXES
*/
function af_checkboxes( $field ){
  \WC_ADDON_FOR_AF\ADMIN::getInstance()->displayCheckboxes( $field );
}

/*
* DISPLAY ACCESSORIES
* SHOW COMMA SEPERATED VALUES FOR EACH ARRAY ITEM
*/
function af_filter_setting_value_accessories( $accessories ){
  if( is_array( $accessories ) ) return implode( ', ', $accessories );
  if( $accessories ) return $accessories;
  return 'Not Set';
}

/*
* DISPLAY DELIVERY AND RETURN PLACE
* SHOW TERM NAME FOR TERM ID PASSEd
*/
function af_filter_setting_value_delivery_and_return_place( $term_id ){
  $term = get_term( $term_id );
  return $term->name;
}

/*
* DROPDOWN OPTIONS FOR DELIVERY AND RETURN PLACE
* TERMS OF TAXONOMY: LOCATION
*/
function af_filter_dropdown_delivery_and_return_place( $options ){
  $locations = get_terms( array(
    'taxonomy'    => 'location',
    'hide_empty'  => false
  ) );

  foreach( $locations as $location ){
    $options[ $location->term_id ] = $location->name;
  }
  return $options;
}

/*
* PDF FIELDS FOR THE INVOICE & STATEMENT
*/
function af_pdf_fields_invoice_statement( $fields ){
  $field_slugs = array(
    'date', 'order_id', 'ref_rep', 'ref_car',

    'name', 'primary_address', 'secondary_address', 'city_state', 'country',

    'vehicle', 'product_description', 'duration', 'accessories',

    'delivery_place', 'date_start', 'delivery_remark',

    'return_place', 'date_end', 'return_remark',

    'price', 'accessories_price', 'discount', 'delivery_fee', 'drop_off_fee', 'total_price',

    'payment_rcvd_amount_1', 'payment_rcvd_date_1', 'payment_rcvd_amount_2', 'payment_rcvd_date_2',

    'balance_due', 'insurance_expiry'
  );

  foreach( $field_slugs as $field_slug ){
    $fields[ $field_slug ] = array();
  }

  $fields['date_end'] = array( 1 );

  return $fields;
}

function af_data_invoice_statement( $data ){
  $data['price'] = $data['subtotal_price'];
  $data['delivery_remark'] = $data['delivery_place_remark'];
  $data['return_remark'] = $data['return_place_remark'];
  $data['city_state'] = $data['city'] . ', ' . $data['state'];
  return $data;
}
