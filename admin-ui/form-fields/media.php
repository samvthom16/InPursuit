<?php

  $value = isset( $atts['value'] ) ? $atts['value'] : "";

  if( !$value && isset( $atts['default'] ) && $atts['default'] ){
    $value = $atts['default'];
  }

	
?>
<div data-behaviour='orbit-media-picker'>
	<input type="text" placeholder="<?php _e( $atts['placeholder'] ? $atts['placeholder'] : '' );?>" name="<?php _e( $atts['name'] );?>" value="<?php _e( $value );?>" />
	<button class='button'><?php _e( $atts['label'] );?></button>
</div>
