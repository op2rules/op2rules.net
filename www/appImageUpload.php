<?php 
require_once($_SERVER['DOCUMENT_ROOT'] . '../../mysqlConnect.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../settings.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../randomFunctions.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/userSession.php');
$username = $_SESSION['name'];
function validateUpload($test)
{
	if ($test == UPLOAD_ERR_OK){return;}
	switch ($test)
	{		
		case UPLOAD_ERR_PARTIAL:
			$message = 'image was only partially uploaded :o';
			break;
		
		case UPLOAD_ERR_NO_FILE:
			$message = 'nothing was uploaded 0.o';
			break;
		
		case UPLOAD_ERR_NO_TMP_DIR:
			$message = 'could not find temporary upload folder :P';
			break;
		
		case UPLOAD_ERR_CANT_WRITE:
			$message = 'could not write image :|';
			break;
			
		default:
			$message = 'unknown error, please notify an admin about this, which you can do on Webirc :)';
	}
	throw new Exception($message);
}
$errors = array();
try
{
	if (!array_key_exists('image', $_FILES)){throw new Exception('image not found in uploaded data :(');}
	if ($_FILES["image"]["size"] > 8388608){throw new Exception('image is larger then 8MB');}
	if (!($_FILES["image"]["type"] == "image/jpeg") && !($_FILES["image"]["type"] == "image/png")){throw new Exception('image is not of file type jpg or png');}
	$image = $_FILES['image'];
	validateUpload($image['error']);
	if (!is_uploaded_file($image['tmp_name'])){throw new Exception('Image is not an uploaded file??');}
	$info = getImageSize($image['tmp_name']);
	if (!$info){throw new Exception('there will be a file host for files.. >:(');}
}
catch (Exception $ex){$errors[] = $ex->getMessage();}
if (count($errors) == 0)
{
		$query = sprintf
	(
		"INSERT INTO user_images (username, filename, mime_type, file_size, file_data)
		values ('$username', '%s', '%s', '%d', '%s')",
		$mysqli->real_escape_string($image['name']),
		$mysqli->real_escape_string($info['mime']),
		$image['size'],
		$mysqli->real_escape_string
		(
			file_get_contents($image['tmp_name'])
		)
	);
	$check = "SELECT * FROM user_images WHERE username = '$username';";
	$res = $mysqli->query($check);
	if (mysqli_num_rows($res) >= 0 && mysqli_num_rows($res) < 20){$mysqli->query($query) or die (mysqli_error());}
	else
	{
		echo "You have too many uploaded images, the maximum is 10";
		exit;
	}
	$id = (int) mysqli_insert_id($mysqli);
	header('Location: appImageView.php?id=' . $id);
	exit;
}
echo "The following errors occured: ";
foreach ($errors as $error){echo $error;}
?>