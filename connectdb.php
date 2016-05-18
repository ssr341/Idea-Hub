<?php

$db = pg_connect( "host=pdc-amd01.poly.edu port=5432 dbname=bk1355 user=bk1355 password=*ca9c3k3" )
  or die('Could not connect: ' . pg_last_error());


// start session, check session IP with client IP, if no match start a new session
session_start();
if(isset($SESSION["REMOTE_ADDR"]) && $SESSION["REMOTE_ADDR"] != $SERVER["REMOTE_ADDR"]) {
  session_destroy();
  session_start();
}

?>
