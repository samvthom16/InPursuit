<?php

  if( !isset( $atts['rules'] ) ){
    $atts['rules'] = array(
      'options'	=> array(
        'hide'	=> array(
          'type'	=> array( 'text', 'textarea' )
        ),
        'show'	=> array(
          'type'	=> array( 'dropdown', 'checkbox' )
        )
      ),
      'placeholder'	=> array(
        'show'	=> array(
          'type'	=> array( 'text', 'textarea' )
        ),
        'hide'	=> array(
          'type'	=> array( 'dropdown', 'checkbox' )
        )
      ),
    );
  }

  if( !isset( $atts['items'] ) || count( $atts['items'] ) == 0 ){
    $atts['items'] = array(
      'type' => array(
        'type' 		=> 'dropdown',
        'text' 		=> 'Select Field Type',
        'options'	=> array(
          'text'			=> 'Text',
					'media'			=> 'Media',
          'textarea'	=> 'Textarea',
          'dropdown'	=> 'Dropdown',
          'checkbox'	=> 'Checkboxes'
        )
      ),
      'text' => array(
        'type' 		=> 'text',
        'text' 		=> 'Label',
      ),
      'placeholder'	=> array(
        'type'	=> 'text',
        'text'	=> 'Placeholder',
        'help'	=> 'Appears within the input text fields'
      ),
      'options' => array(
        'type' 		=> 'repeater-options',
        'text' 		=> 'Options',
        'help'		=> 'Only valid for dropdown or checkboxes. Enter each item on a new line.'
      ),
    );
  }

  //ORBIT_UTIL::getInstance()->test( $atts );

  $atts['slug'] = $atts['name'];
  $atts['rows'] = isset( $atts['value'] ) && $atts['value'] ? $atts['value'] : array();
  $atts['fields'] = $atts['items'];

  $params = array( 'rules', 'slug', 'rows', 'fields' );

  _e("<div data-behaviour='orbit-repeater-cf'");

  foreach ($params as $param) {
    $param_value = json_encode( isset( $atts[ $param ] ) && $atts[ $param ] ? $atts[ $param ] : array() );
    if( isset( $atts[ $param ] ) && !is_array( $atts[ $param ] ) ){
      $param_value = $atts[ $param ];
    }
    _e(" data-$param='$param_value'");
  }

  _e("></div>");
