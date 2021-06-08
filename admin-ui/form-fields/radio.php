<ul class="list-unstyled">
<?php $i = 0;foreach( $atts['items'] as $item ): if( isset( $item['slug'] ) && $item['slug'] ):?>
	<?php

		/*
		* CHECKED FLAG CAN BE TRUE FOR TWO CONDITION
		* 1. WHEN NO VALUE HAS BEEN PASSED THEN SET THE FIRST OPTION AS DEFAULT
		* 2. WHEN VALUE HAS BEEN PASSED, MATCH IT AGAINST THE CORRESPONDING RADIO BUTTON
		*/
		$checked_flag = false;
		if( ( ( !isset( $atts['value'] ) || !$atts['value'] ) && !$i ) || ( isset( $atts['value'] ) && $item['slug'] == $atts['value'] ) ){
			$checked_flag = true;
		}
	?>
	<li class="radio">
		<label>
			<?php //ORBIT_UTIL::getInstance()->test( $item );?>
			<?php //ORBIT_UTIL::getInstance()->test( $atts );?>

			<input type="radio" <?php if( $checked_flag ){_e("checked='checked'");}?> name="<?php _e( $atts['name'] );?>" value="<?php _e( $item['slug'] );?>" />&nbsp;<?php _e( $item['name'] );?>

		</label>
	</li>
<?php $i++;endif; endforeach;?>
</ul>
