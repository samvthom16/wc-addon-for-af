<?php


  global $post_id;

  //echo $post_id;

  $payments = get_post_meta( $post_id, 'af_payments', true );

  //$this->test( $payments );


?>

<div data-behaviour='wc-af-payments' data-payments='<?php echo wp_json_encode( $payments );?>'></div>
