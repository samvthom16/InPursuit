<select name="<?php _e( $atts['name'] );?>">
	<option value=""><?php _e( isset( $atts['default_option'] ) ? $atts['default_option'] : "Select" ); ?></option>
	<?php foreach( $atts['items'] as $item ):?>
	<option <?php if( isset( $item['parent'] ) ){ _e("data-parent='".$item['parent']."'");}?> <?php if( isset( $atts['value'] ) && $item['slug'] == $atts['value'] ){_e("selected='selected'");}?>  value="<?php _e( $item['slug'] );?>"><?php _e( $item['name'] );?></option>
	<?php endforeach;?>
</select>
