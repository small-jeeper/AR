<?php
// database handler
$dbh = new PDO('mysql:host=localhost;dbname=test', 'root', '');
register_shutdown_function('close_connection', $dbh); # close connection 
function close_connection($dbh) {
//  $dbh=null;
}
