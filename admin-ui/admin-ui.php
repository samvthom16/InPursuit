<?php


	$inc_files = array(
		'class-inpursuit-admin-settings.php',
		'class-inpursuit-admin-ui.php',
		'class-inpursuit-post-admin-ui-base.php',
		'class-inpursuit-member-admin-ui.php',
		'class-inpursuit-event-admin-ui.php',
	);

	foreach( $inc_files as $inc_file ){
		require_once( $inc_file );
	}
