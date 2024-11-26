<?php



$inc_files = array(
	'class-inpursuit-rest-base.php',
	'class-inpursuit-rest-post-base.php',
	'class-inpursuit-rest-member.php',
	'class-inpursuit-rest-event.php',
	'class-inpursuit-rest-custom.php',
	'class-inpursuit-rest-comment.php',
	'class-inpursuit-rest-analytics.php',
	'class-inpursuit-rest-user.php'
);

foreach( $inc_files as $inc_file ){
	require_once( $inc_file );
}
