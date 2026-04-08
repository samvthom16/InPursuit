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
		add_action( 'inpursuit_insert_comment_category_relation', array( $this, 'insert_comment_category_relation' ), 10, 2 );

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

	function insert_comment_category_relation( $comment_id, $request ){
		$params = $request->get_params();
		$comment_categories = isset( $params['comments_category'] ) && $params['comments_category'] ? explode(",", $params['comments_category'] ): array();


		// USE ONLY 1ST CATEGORY_ID FROM THE ARRAY IF THERE ARE MULTIPLE CATEGORY_IDS IN THE REQUEST
		$first_comment_category = count( $comment_categories ) && $comment_categories[0] ? $comment_categories[0] : 0;

		$first_comment_category = $this->get_validated_category_id( $first_comment_category );

		// RETURN IF NOT VALID
		if( !$first_comment_category ){
			return;
		}

		// RETURN IF CATEGORY DOESN'T EXIST
		if( !INPURSUIT_DB_COMMENTS_CATEGORY::getInstance()->comment_category_id_exists( $first_comment_category ) ){
			return;
		}

		// RETURN IF COMMENT CATEGORY RELATION EXISTS
		if( $this->comment_category_relation_exists( $first_comment_category, $comment_id ) ){
			return;
		}

		$item = array(
			'term_id'		 => $first_comment_category,
			'comment_id' => $comment_id
		);

		// INSERT INTO TABLE
		$this->insert( $item );

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
		$query = $this->prepare( "SELECT term_id FROM $table WHERE comment_id = %d", intval( $comment_id ) );
		$comment_categories = $wpdb->get_col( $query );

		if( $comment_categories ){
			return array_map('intval', $comment_categories );
		}

		return $comment_categories;
	}

	function get_comment_ids_by_category_ids( $category_ids ){
		global $wpdb;
		$ids	 = array();
		$table = $this->getTable();

		foreach( explode(",", $category_ids ) as $category ){
			$category_id = $this->get_validated_category_id( $category );

			if( !$category_id ){
				continue;
			}

			array_push( $ids, $category_id );

		}

		if( !count( $ids ) ) return $ids;

		// Use parameterized query with placeholders
		$placeholders = implode( ',', array_fill( 0, count( $ids ), '%d' ) );
		$query = $this->prepare(
			"SELECT comment_id FROM $table WHERE term_id IN ($placeholders)",
			$ids
		);

		$comment_categories = $wpdb->get_col( $query );

		return $comment_categories;
	}

	function comment_category_relation_exists( $term_id, $comment_id ){
		global $wpdb;
		$table = $this->getTable();
		$count_query = $this->prepare(
			"SELECT COUNT(*) FROM $table WHERE term_id = %d AND comment_id = %d",
			intval( $term_id ),
			intval( $comment_id )
		);

		if( $this->get_var( $count_query ) ){
			return true;
		}

		return false;

	}

}

INPURSUIT_DB_COMMENTS_CATEGORY_RELATION::getInstance();
