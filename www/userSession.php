<?php
session_set_cookie_params(349009865456789);
session_start();

if(!isset($_SESSION['name'])){
	$_SESSION['name'] = 'Guest';
	$_SESSION['id'] = 0;
	$_SESSION['logged'] = 0;
	$_SESSION['response'] = 0;
	$_SESSION['status'] = 0;
	$_SESSION['theme'] = 255;
}

if(isset($_POST['kill'])){
	session_destroy();
	echo "Session destroyed";
}