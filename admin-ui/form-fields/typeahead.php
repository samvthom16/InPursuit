<?php
	$places = array();
	foreach ( $atts['items'] as $key => $value) { array_push( $places , $value['slug'] ); }
?>
<p>
	<input type="text" data-behaviour="typeahead" name="<?php _e( $atts['name'] );?>" data-arr='<?php echo json_encode( $places );?>' placeholder="<?php _e( $atts['placeholder'] );?>" value="<?php _e( $atts['value'] );?>" />
</p>
