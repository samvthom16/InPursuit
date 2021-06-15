<?php
/*
* Model: POST TYPE BASE
*/

class INPURSUIT_DB_POST_BASE extends INPURSUIT_DB_BASE{

	private $post_type_options;
	private $post_type;

	function __construct(){

		parent::__construct();

		add_action( 'init', array( $this, 'registerPostType' ) );

	}

	function setPostTypeOptions( $post_type_options ){ $this->post_type_options = $post_type_options; }
	function getPostTypeOptions(){ return $this->post_type_options; }

	function setPostType( $post_type ){ $this->post_type = $post_type; }
	function getPostType(){ return $this->post_type; }

	function registerPostType(){
		$options = $this->getPostTypeOptions();
		if( is_array( $options ) && count( $options ) && isset( $options['slug'] ) ){
			$args = array(
				'labels'        => $this->_labelsArray( $options ),
				'description'   => $options['description'],
				'public'        => true,
				'menu_icon'			=> $options['menu_icon'],
				'supports'			=> $options['supports'],
				'show_in_rest'	=> true
			);
			register_post_type( $options['slug'], $args );
		}
	}

}
