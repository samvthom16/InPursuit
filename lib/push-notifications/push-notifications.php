<?php

$inc_files = array(
	'class-inpursuit-push-sender.php',
);

foreach ( $inc_files as $inc_file ) {
	require_once( $inc_file );
}

INPURSUIT_PUSH_SENDER::getInstance();
