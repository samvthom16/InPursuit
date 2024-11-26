<?php

$inc_files = array(
  'class-inpursuit-rest-authentication.php',
  'class-inpursuit-rest-otp-auth.php'
);

foreach( $inc_files as $inc_file ){
	require_once( $inc_file );
}
