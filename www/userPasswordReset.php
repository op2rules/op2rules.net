<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/userSession.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../settings.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../randomFunctions.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../mysqlConnect.php');

if(!isset($_POST['recovery'])) echo "Enter a recovery code";
elseif(!isset($_POST['password'])) echo "Enter a new password";
elseif(!isset($_POST['username'])) echo "Enter your username";
elseif(isset($_POST['recovery']) && isset($_POST['password']) && isset($_POST['username'])){
	$response = resetPassword2($mysqli, $_POST['username'],$_POST['password'],$_POST['recovery']);
	echo $response;
}
?>