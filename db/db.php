<?php


	$inc_files = array(
		'class-inpursuit-db-base.php',
		'class-inpursuit-db-member.php',
		'class-inpursuit-db-event-member-relation.php',
	);

	foreach( $inc_files as $inc_file ){
		require_once( $inc_file );
	}
