<?php

	//$this->test( $user->ID );

	$user_db = INPURSUIT_DB_USER::getInstance();

	$taxonomy = $user_db->getTaxonomy();

	$terms = get_terms( array(
		'taxonomy' 		=> $taxonomy,
		'hide_empty' 	=> false,
	) );

	$selected_groups = $user_db->getLimitedGroups( $user->ID );

	//$this->test( $selected_groups );
?>
<table class="form-table">
	<tr>
		<th><label>Limit User Access</label></th>
		<td>
			<ul>
			<?php foreach( $terms as $term ):?>
			<li style="display: inline-block;margin-right: 10px;">
				<label>
					<input type="checkbox" name="<?php _e( $taxonomy );?>[]" value="<?php _e( $term->term_id );?>" <?php if( in_array( $term->term_id, $selected_groups ) ) echo "checked='checked'"; ?> />
					<?php _e( $term->name );?>
				</label>
			</li>
			<?php endforeach;?>
		</ul>
		</td>
	</tr>
</table>
