<?php

$fields = array();

$taxonomies = $this->getTaxonomiesForDropdown();
foreach( $taxonomies as $slug => $title ){
	array_push( $fields, array(
		'slug'	=> $slug,
		'title'	=> $title,
		'field'	=> 'taxonomy'
	) );
}

$wp_util = INPURSUIT_WP_UTIL::getInstance();
?>

<div id="inpursuit-misc" class="misc-pub-section">
	<?php foreach( $fields as $field ):?>
	<div class='inpursuit-form-field'>
		<label><span class="dashicons <?php _e( $this->getDashIcon( $field['slug'] ) );?>"></span><?php _e( $field['title'] );?></label>
		<?php $this->formField( $wp_util->getAttsForTermsDropdown( $field['slug'], $post ) ); ?>
	</div>
	<?php endforeach; ?>
</div>
