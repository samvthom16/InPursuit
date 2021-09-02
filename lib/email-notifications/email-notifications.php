<?php

$inc_files = array(
  'class-inpursuit-mailer.php',
  'class-inpursuit-notify-admins.php',
  'class-inpursuit-notify-members.php'
);

foreach( $inc_files as $inc_file ){
  require_once( $inc_file );
}
