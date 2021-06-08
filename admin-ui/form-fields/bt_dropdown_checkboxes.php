
<!-- ASSUMES THAT THE BOOTSTRAP DROPDOWN IS BEING USED -->
<div class="orbit-dropdown" data-behaviour="bt-dropdown-checkboxes">
  <button type="button">
    <span class='btn-label'><?php _e( 'Select' );?></span>
    <span class="caret"></span>
  </button>
  <ul class="orbit-dropdown-menu" role="menu" aria-labelledby="menu1">
    <li style="padding: 0 10px; margin-bottom: 10px;"><a href="#" data-btn="reset">Clear All</a></li>
    <?php foreach( $atts['items'] as $item ): if( isset( $item['slug'] ) && $item['slug'] ):?>
    <li class="checkbox" <?php if( isset( $item['parent'] ) ){ _e("data-parent='".$item['parent']."'");}?>>
    	<label>
    		<input type="checkbox" <?php if( in_array( $item['slug'], $atts['value']) ){_e("checked='checked'");}?> name="<?php _e( $atts['name'] );?>[]" value="<?php _e( $item['slug'] );?>" />
        <span><?php _e( $item['name'] );?></span>
    	</label>
    </li>
    <?php endif;endforeach;?>
  </ul>
</div>
