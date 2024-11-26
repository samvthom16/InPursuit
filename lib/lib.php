<?php

$inc_files = array(
  'class-inpursuit-wp-util.php',
  'email-notifications/email-notifications.php'
);

foreach( $inc_files as $inc_file ){
  require_once( $inc_file );
}
