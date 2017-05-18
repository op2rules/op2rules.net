<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '../../mysqlConnect.php');


$id = $_GET['code'];
$info = $mysqli->query("SELECT url, hits FROM urls WHERE code='$id'");

if($info->num_rows == 1){ 
	$mysqli->query("UPDATE urls SET hits=hits+1 WHERE code='" . $id . "'");
	$url = $info->fetch_array(MYSQLI_ASSOC);
	if(substr( $url['url'], 0, 4 ) === "http"){
		header("location:".$url['url']);
	}
	else{
		header("location:http://".$url['url']);
	}
}else 
{
	header("location:http://op2rules.net/");
}

?> 