<?php
$content = '';
$editor_id = $atts['name'];
$settings = array(
    'media_buttons' => false,
    'quicktags'     => false,
    // 'textarea_name' => "input_{$editor_id}",
    // 'tinymce'       => false,
);
wp_editor( $content, $editor_id, $settings );
?>
