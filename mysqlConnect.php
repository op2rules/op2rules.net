<?php
require_once('settings.php');
//$connection = mysql_connect($dbhost, $dbuser, $dbpass) or die("MySQL Error: " . mysql_error());
//mysql_select_db($dbname, $connection) or die("MySQL Error: " . mysql_error());

$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

//Returns a string with random row selected from db
function ircQuote($sql){
	$query = $sql->query("SELECT quote, quotee from quotes AS r1 JOIN (SELECT CEIL(RAND() * (SELECT MAX(ID) FROM quotes)) AS id) AS r2 WHERE r1.ID >= r2.id ORDER BY r1.ID ASC LIMIT 1");
	$row = mysqli_fetch_array($query);
	$output = $row['quotee'] . ": " . $row['quote'];
	return $output;
}

//Returns 1 for success, 0 for failure
function confirmUserID($name,$id){ 
	$name = mysql_real_escape_string($name);
	$check = mysql_query("SELECT id FROM users WHERE name = '$name'");
	if(!$check || (mysql_num_rows($check) < 1)){
		return 0;
	}
	$user = mysql_fetch_array($check);
	$user['id'] = intval($user['id']);
	$id = intval($id);
	if($id == $user['id']){
		return 1;
	}
	else{
		return 0;
	}
}

//Returns 0  if user not found
function getUserInfo($name){
	$user = mysql_query("SELECT * FROM users WHERE name='$name'");
	if(!$user || (mysql_num_rows($user) < 1)){
		return 0;
	}
	$userInfo = mysql_fetch_array($user);
	return $userInfo;
}



?>