<?php if( $atts['name'] == 'post_featured' ) :?>
<input data-behaviour="orbit-field-files" type="file"  name="<?php _e( $atts['name'] );?>" />
<?php else: ?>
<input data-behaviour="orbit-field-files" type="file" multiple="multiple" name="<?php _e( $atts['name'] );?>[]" />
<?php endif; ?>
