<?php

  global $post_id;

  $checklist_values = get_post_meta( $post_id, 'af_checklist', true );
  if( !is_array( $checklist_values ) ) $checklist_values = array();

  //$this->test( $checklist_values );

  $options = array(
    'PASSPORT',
    'DRIVER’S LICENSE',
    'ETA',
    'PORTUGAL DOCUMENT (WHEN NEEDED – delivery in Lisbon or Port only)',
    'DECLARATION (WHEN NEEDED) sent for those with EU passports',
    'SIGNED CONRACT RECEIVED (EMAIL TO BE SENT also asking for missing info if not received)'
  );

  //$this->test( $options );

  foreach( $options as $option ){
    ?>
    <p>
      <label>
        <input type='checkbox' name='af_checklist[]' <?php if( in_array( $option, $checklist_values ) ) _e( "checked='checked'" );?> value='<?php echo $option;?>' />
        <?php echo $option;?>
      </label>
    </p>
    <?php
  }

?>
