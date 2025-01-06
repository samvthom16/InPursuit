<?php
/*
* RELATION TABLE BETWEEN INPURSUIT_COMMENTS AND INPURSUIT_COMMENTS_CATEGORY
*/

class INPURSUIT_DB_COMMENTS_CATEGORY_RELATION extends INPURSUIT_DB_BASE {

	function __construct(){

		$this->setTableSlug( 'comments_category_relation' );

		parent::__construct();

		add_action( 'inpursuit_before_delete_comment', array( $this, 'before_delete_comment' ) );
		add_action( 'inpursuit_before_delete_comments_category', array( $this, 'before_delete_comments_category' ) );

	}

	function create(){
		global $wpdb;
		$table = $this->getTable();
		$charset_collate = $this->get_charset_collate();

		$comments_table 				 = $this->getTablePrefix() . 'comments' . '(ID)';
		$comments_category_table = $this->getTablePrefix() . 'comments_category' . '(term_id)';

		$sql = "CREATE TABLE IF NOT EXISTS $table (
			ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			term_id BIGINT(20) UNSIGNED NOT NULL,
			comment_id BIGINT(20) UNSIGNED NOT NULL,
			PRIMARY KEY(ID),
			FOREIGN KEY (term_id) REFERENCES $comments_category_table,
			FOREIGN KEY (comment_id) REFERENCES $comments_table
		) $charset_collate;";

		$this->query( $sql );

	}

	function delete_comments_category_relations( $args ){
		$table      = $this->getTable();
		$column			= $args['column_name'];
		$sql        = "DELETE FROM $table WHERE $column = %d;";
		$this->query( $this->prepare( $sql, $args['ID'] ) );
	}

	function before_delete_comments_category( $term_id ){
		$this->delete_comments_category_relations( array( 'column_name' => 'term_id', 'ID' => $term_id ) );
	}

	function before_delete_comment( $comment_id ){
		$this->delete_comments_category_relations( array( 'column_name' => 'comment_id', 'ID' => $comment_id ) );
	}

	function get_validated_category_id( $category_id ){

		if( '' === trim( $category_id ) || !is_numeric( $category_id ) ){
			return 0;
		}

		$category_id = (int) $category_id;

		if( $category_id <=0 ){
			return 0;
		}

		return $category_id;

	}

	function get_comment_categories( $comment_id ){
		global $wpdb;
		$table = $this->getTable();
		$comment_categories = $wpdb->get_col( "SELECT term_id FROM $table WHERE comment_id = $comment_id" );

		if( $comment_categories ){
			return array_map('intval', $comment_categories );
		}

		return $comment_categories;
	}

}

INPURSUIT_DB_COMMENTS_CATEGORY_RELATION::getInstance();
