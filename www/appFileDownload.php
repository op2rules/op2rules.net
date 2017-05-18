<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '../../mysqlConnect.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../settings.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../randomFunctions.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/userSession.php');

if($_SESSION['status'] < 1) exit(); // No banned user/guest bs
if(isset($_POST['id'])){
	$row = mysqli_fetch_array($mysqli->query("SELECT type, name, size, content FROM userFiles WHERE id = '" . $_POST['id'] . "' AND uploader = '" . $_SESSION['id'] . "'")); // Only the uploader of a file can download it
	$type = $row['type'];
	$name = $row['name'];
	header("content-type: $type");
	header("content-disposition: download; filename=$name");
    header('Cache-Control: private');
    header('Pragma: private');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Content-Length: ' . $row['size']);
	print $row['content'];
}

?>