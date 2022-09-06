<?php

require('db_connection.php');
session_start();

$url = 'https://www.brandonspangler.com';
$uri = $_SERVER['REQUEST_URI'];
$url = $url.$uri;

// For Debugging 
// echo $url;

$url_components = parse_url($url);
parse_str($url_components['query'], $params);

$_SESSION['code'] = $params['code'];
$_SESSION['state'] = $params['state'];

// For Debugging 
// echo '<br><br> Code: <br>'.$code;
// echo '<br><br> State: <br>'.$state;

header("Location: home.php");
?>