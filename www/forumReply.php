<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/userSession.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../settings.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../randomFunctions.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../mysqlConnect.php');

function addReply($sql){
	if(isset($_POST['id']) && isset($_POST['body']) && $_POST['body'] != ""){ // Verify category status 
		$query = "SELECT * FROM forumTopics WHERE id = '" . $_POST['id'] . "'";
		$row = mysqli_fetch_array($sql->query($query));
		$error = "You are not allowed";
		if ($row['status'] > $_SESSION['status']) return $error;
		// Parse data and submit to SQL
		$_POST['body'] = $sql->real_escape_string(nl2br(htmlspecialchars($_POST['body']))); 
		$_POST['id'] = $sql->real_escape_string($_POST['id']);
		// Ensure a topic actually exists
		if($result = $sql->query("SELECT * FROM forumTopics WHERE id = " . $_POST['id'])){
				$count = $result->num_rows;
		}
		if($count == 0) return "That topic doesn't exist";
		$postQuery = $sql->query("INSERT INTO forumReplies (content, createdby, topic) VALUES ('" . $_POST['body'] . "', '" . $_SESSION['name'] . "' , '" .  $_POST['id'] . "')");
		logThis($sql,"New Reply",$_SESSION['name'],$_POST['id']); 
		//return "<pre>Your message has been posted to [ <a class='ajax' href='/forumView.php?id=" . $_POST['id'] . "'>" . $row['name']	 . "</a> ]</pre> ";
	}
		return 0;
		$error = "Well that post won't go far";
		return $error;
}

if($_SESSION['logged']){
	if(isset($_POST['body']) && $_POST['body'] == ""){ // No code sent
	echo "Enter Content";
	}else{
		if(isset($_POST['body'])) $_SESSION['response'] = addReply($mysqli); 
	}
} else $_SESSION['response'] = "You gotta login";
?>
</pre>