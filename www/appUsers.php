<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '../../mysqlConnect.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../settings.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../randomFunctions.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../hubFunctions.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/userSession.php');

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){}else{
	require_once("head.php");
}
if($_SESSION['logged']){
?>
<pre>
<?
//Viewing a specific user
if(isset($_GET['view'])){
	$user = $mysqli->real_escape_string($_GET['view']);
	$check = $mysqli->query("SELECT id FROM users WHERE name = '$user'");
	if($check->num_rows){
		echo $user . "<br>";
		$forumTopics = mysqli_fetch_row($mysqli->query("SELECT COUNT(*) FROM forumTopics WHERE createdby = '$user'"));
		$forumReplies = mysqli_fetch_row($mysqli->query("SELECT COUNT(*) FROM forumReplies WHERE createdby = '$user'"));
		echo "Forum Topics: " . $forumTopics[0] . "<br>";
		echo "Forum Replies: " . $forumReplies[0] . "<br>";
		echo "Send [<a class='ajax' href='appNotes.php?id=newnote&share=$user'> Note </a>]<br>";
	}else echo "Could not find $user";
	echo "\n Back to [<a class='ajax' href='appUsers.php'> Users </a>]";
}else{
?>
Registered Users
<form method='post' action='appUsers.php'><input type="text" placeholder="Name starts with" name="search"><input type='submit' class='ajaxForm' value='Search'></input></form>
<table><tr><td><center><a class='ajax' href='appUsers.php?name=1'>^ </a>Name<a class='ajax' href='appUsers.php?name'> v</a></center></td><td><center><a class='ajax' href='appUsers.php?lastlogin'>Last Login</a></center></td><td><center><a class='ajax' href='appUsers.php?joined'>Joined</a></center></td></tr>
<?php
	$search = "";
	//Searching for a user
	if(isset($_POST['search'])){
		$like = $mysqli->real_escape_string($_POST['search']);
		$search = "WHERE name LIKE '$like%'";
	}
	if(!empty($_GET)){
		if(isset($_GET['name']) && $_GET['name'] == 1) $query = $mysqli->query("SELECT name, status, lastlogin, created FROM users ORDER BY name ASC"); 
		elseif(isset($_GET['name'])) $query = $mysqli->query("SELECT name, status, lastlogin, created FROM users ORDER BY name DESC");
		elseif(isset($_GET['lastlogin'])) $query = $mysqli->query("SELECT name, status, lastlogin, created FROM users ORDER BY lastlogin DESC");
		elseif(isset($_GET['joined'])) $query = $mysqli->query("SELECT name, status, lastlogin, created FROM users ORDER BY created");
		else $query = $mysqli->query("SELECT name, status, lastlogin, created FROM users $search");
		}else $query = $mysqli->query("SELECT name, status, lastlogin, created FROM users $search");
		while($row = mysqli_fetch_array($query)){
			$joined = differenceTime($row['created']);
			$lastLogin = substr($row['lastlogin'], 0, 10);
			echo "<tr><td><a class='ajax' href='appUsers.php?view=" . $row['name'] . "'>" . $row['name'] . "</a></td><td>$lastLogin</td><td><center>$joined</center></td></tr>";
		}
		echo "</table>";
	}
}else echo "Only members can view other members"; //If user isn't logged in
?></pre><?php
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){}else{
	require_once("foot.php");
}
?>



