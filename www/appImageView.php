<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '../../mysqlConnect.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../settings.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../randomFunctions.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/userSession.php');
try
{
	if (!isset($_GET['id'])){throw new Exception('ID not specified');}
	$id = (int) $_GET['id'];
	if ($id <=0){throw new Exception('invalid ID specified');}
	$id = $mysqli->real_escape_string($id);
	$query = sprintf('select * from user_images where image_id = %d', $id);
	$result = $mysqli->query($query);
	if (mysqli_num_rows($result) == 0){throw new Exception('image with specified ID not found');}
	$image = mysqli_fetch_array($result);
}
catch (Exception $ex)
{
	header('HTTP/1.0 404 Not Found');
	exit;
}
header('Content-type: ' . $image['mime_type']);
header('Content-lenght: ' . $image['file_size']);
echo $image['file_data'];
?>