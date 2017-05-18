<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '../../mysqlConnect.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../settings.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../randomFunctions.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/userSession.php');

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){}else{
	require_once("head.php");
}

if($_SESSION['logged']){

echo "<pre>";
$newItems = userNotifications($mysqli,$_SESSION['name']);
if($newItems > 1) echo "You have $newItems new items";
if($newItems == 0) echo "You don't have any new items";
if($newItems == 1) echo "You have 1 new item";



$newNotes = $mysqli->query("SELECT * FROM notes WHERE seen = 0 AND createdfor = '" . $_SESSION['name'] . "'");
while($row = mysqli_fetch_array($newNotes)){
	if($row['name'] == '') $row['name'] = "[ ]";
	echo "<br><a class='ajax' href='appNotes.php?note=" . $row['id'] . "'>" . $row['name'] . "</a>";
}

echo "</pre>";
}

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){}else{
	require_once("foot.php");
}