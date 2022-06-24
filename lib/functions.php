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
