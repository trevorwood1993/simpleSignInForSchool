<?php 


$database_hostname = "localhost";
$database_name = "simpleSignInExample";
$database_port = "8889";
$database_username = "root";
$database_password = "root";


$database_string = "mysql:host=$database_hostname;dbname=$database_name;port=$database_port";


try {
  $db = new PDO($database_string,$database_username,$database_password);
  $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
  $db->exec("SET NAMES 'utf8'");
} catch (Exception $e){
  echo 'Could not connect to the database.105';
  exit();
}