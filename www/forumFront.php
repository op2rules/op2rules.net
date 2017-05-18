<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/userSession.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../settings.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../randomFunctions.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../mysqlConnect.php');

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){}else{
	require_once("head.php");
}
?><pre><?php
if($_SESSION['logged']){
$query = $mysqli->query("SELECT * from forumCategory WHERE class = 1");
while($row = mysqli_fetch_array($query)){
	if($row['status'] <= $_SESSION['status']) {
		echo $row['name'];
		$query2 = $mysqli->query("SELECT * from forumCategory WHERE parentid = ". $row['id'] ." AND class < 3");
		while($row2 = mysqli_fetch_array($query2)){ //subCategory loop
			if($row2['status'] <= $_SESSION['status']){
				echo "<table><tr><td style='width: 300px'><center>";
				echo "[ <a class='ajax' href='forumPosts.php?id=" . $row2['id'] . "'>" . $row2['name'] . "</a> ]";
				echo "<br>" . $row2['description'];
				echo "</center></td><td style='width: 560px'>";
				$query3 = $mysqli->query("SELECT * from forumTopics WHERE category = " . $row2['id'] ." ORDER BY created DESC LIMIT 0, 3");
				while($row3 = mysqli_fetch_array($query3)){ //Topics loop
					echo "<a class='ajax' href='forumView.php?id=" . $row3['id'] . "'>" . $row3['name'] . "</a> " . lastReply($mysqli,$row3['id']) . "<br>";
				}
				echo "</td></tr></table>";
			}
		}
	}
}

// Hub Stuff
$i = 0;
while(isset($_SESSION['hub'][$i])){
	$hub = $_SESSION['hub'][$i];
	echo "<br>" . $hub; 
	$query = $mysqli->query("SELECT forumCategory.id, forumCategory.name, forumCategory.description, forumCategory.status AS forumStatus, hubUsers.status AS hubStatus FROM forumCategory JOIN hubs ON forumCategory.parentid = hubs.id JOIN hubUsers ON hubUsers.hub = hubs.name WHERE class = 3 AND hubs.name = '$hub' AND user = '". $_SESSION['name'] ."'");
	while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
		if($row['forumStatus'] <= $row['hubStatus']){
			// Display the single-level forumCats
			echo "<table><tr><td style='width: 300px'><center>";
			echo "[ <a class='ajax' href='forumPosts.php?id=" . $row['id'] . "'>" . $row['name'] . "</a> ]";
			echo "<br>" . $row['description'];
			echo "</center></td><td style='width: 560px'>";
			$query2 = $mysqli->query("SELECT * from forumTopics WHERE category = " . $row['id'] ." ORDER BY created DESC LIMIT 0, 3");
			while($row2 = mysqli_fetch_array($query2, MYSQLI_ASSOC)){
				echo "<a class='ajax' href='forumView.php?id=" . $row2['id'] . "'>" . $row2['name'] . "</a> " . lastReply($mysqli,$row2['id']) . "<br>";
			}
			echo "</td></tr></table>";
		}
	}
	$i++;
}



}else echo "Login to use the message board";
?></pre><?php
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){}else{
	require_once("foot.php");
}
?>

