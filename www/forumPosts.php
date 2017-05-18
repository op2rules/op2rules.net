<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/userSession.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../settings.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../randomFunctions.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../mysqlConnect.php');

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){}else{
	require_once("head.php");
}
?> <pre><?php
if($_SESSION['logged']){
	if(!empty($_POST)){ // The only things posted to this file are from forumPostAdd.php, which create a new topic
		echo addPost($mysqli); 
	}
	
	if(!empty($_GET['del']) && is_numeric($_GET['del'])){ //Trying to delete something? Let's do it but check for permissions/status codes first
		$_GET['del'] = $mysqli->real_escape_string($_GET['del']);
		$result = $mysqli->query("SELECT * FROM forumTopics WHERE id = " . $_GET['del']);
		$count = $result->num_rows;
		if($count == 0) echo "Topic doesn't exist";
		else { //Found topic
			$row = mysqli_fetch_array($result);
			$_GET['id'] = $row['category']; // Quick little hack to load the category after (:
			if($row['createdby'] == $_SESSION['name'] or $_SESSION['status'] > 99){
				$mysqli->query("DELETE FROM forumTopics WHERE ID = " . $_GET['del']);
				$mysqli->query("DELETE FROM forumReplies WHERE topic = ". $_GET['del']);
				echo "<pre>The post quickly fades out of sight<br><br>";
				logThis($mysqli,"Delete Topic",$_SESSION['name'],$_GET['del']); 
			}else{
				echo "You lack power, my young padawan";
			}
		}
	}
	
	if (isset($_GET['id']) && is_numeric($_GET['id'])) {
		$_GET['id'] = $mysqli->real_escape_string($_GET['id']);
		if($row = mysqli_fetch_array($mysqli->query("SELECT forumCategory.class, hubs.name AS hubName FROM forumCategory JOIN hubs ON hubs.id = forumCategory.parentid WHERE forumCategory.id = " . $_GET['id']),MYSQLI_ASSOC)){
			if($row['class'] == 3){// Check if the user is logged into the hub
				if(isset($_SESSION['hub'])){
					if(!in_array($row['hubName'], $_SESSION['hub'])){
						login:
						echo "Login to Hub";
						if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){}else{
							require_once("foot.php");
						}
						exit();
					}
				}else{ // Not even logged into a hub...
					goto login;
				}
			}
		}
		echo forumTrace($mysqli,$_GET['id']);
		if(!isset($_GET['pg'])) $pg = 0; // Pagination
		else { $pg = $_GET['pg']; }
		$pg2 = $pg + 10;
		$query = $mysqli->query("SELECT * from forumTopics WHERE category = " . $_GET['id'] ." ORDER BY created DESC LIMIT 10 OFFSET $pg");
		echo "<table><tr><td valign=left>";
		while($row = mysqli_fetch_array($query)){
			if($row['status'] <= $_SESSION['status']) {
			echo "[ <a class='ajax' href='forumView.php?id=" . $row['id'] . "'>" . $row['name'] . " </a>] "; 
			echo lastReply($mysqli,$row['id']) . "<br>";
			}
		}
		echo "</td></tr></table>";
		if($pg != 0) { 
			$pgBack = $pg - 10;
			echo "[<a class='ajax' id='fuckme' href='forumPosts.php?id=" . $_GET['id'] . "&pg=$pgBack'> < </a>] ";
		}
		if(mysqli_num_rows($mysqli->query("SELECT * FROM forumTopics WHERE category = ". $_GET['id'])) > $pg2){
			echo "[<a class='ajax' id='fuckme' href='forumPosts.php?id=" . $_GET['id'] . "&pg=$pg2'> > </a>] ";
		}
		echo "<br><br>[ <a class='ajax' href='forumPostAdd.php?id=" . $_GET['id'] . "'>New Title</a> ]";
	}else{echo "Bad reference"; }
}else{
	echo "Login to your account";
}
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){}else{
	require_once("foot.php");
}

function addPost($sql){
	if(!isset($_POST['title']) or $_POST['title'] == "") return "No Title";
	if(isset($_POST['body']) && $_POST['body'] == "") return "No content"; // Make sure a content was sent
	elseif(isset($_POST['body']) && isset($_POST['title']) && isset($_POST['id'])){ // Verify category status 
		$query = "SELECT * FROM forumCategory WHERE id = '" . $_POST['id'] . "'";
		$row = mysqli_fetch_array($sql->query($query));
		$error = "You are not allowed";
		if ($row['status'] > $_SESSION['status']) return $error;
		// Parse data 
		$_POST['body'] = $sql->real_escape_string(nl2br(htmlspecialchars($_POST['body']))); 
		$_POST['title'] = $sql->real_escape_string(htmlspecialchars($_POST['title']));
		$_POST['id'] = $sql->real_escape_string($_POST['id']);
		// Check that a category exists for the topic
		if($result = $sql->query("SELECT * FROM forumCategory WHERE id = " . $_POST['id'])){$count = $result->num_rows;}
		else {$count = 0;}
		if($count == 0) { return $error; }
		$hub = "NULL";
		if($row = mysqli_fetch_array($sql->query("SELECT forumCategory.id, forumCategory.class, forumCategory.name, hubs.name as hubName FROM forumCategory JOIN hubs ON forumCategory.parentid = hubs.id WHERE forumCategory.id = " . $_POST['id']))){
			if($row['class'] == 3){
				$hub = "'" . $row['hubName'] . "'";
			}
		}
		$postQuery = $sql->query("INSERT INTO forumTopics (name, content, category, createdby, hub) VALUES ('" . $_POST['title'] . "', '" . $_POST['body'] . "' , '" .  $_POST['id'] . "' , '" . $_SESSION['name'] . "', $hub)");
		logThis($sql,"New Post",$_SESSION['name'],$_POST['title']); 
		if($hub != "NULL"){
			return "Your message has been posted to [ <a class='ajax' href='forumPosts.php?id=" . $row['id'] ."'> " . $row['hubName'] . " </a> ]<br>";
		} 
		else return;
		
	}
		return "Well that post won't get far";
	
}
?>
</pre>