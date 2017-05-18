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
	$hubs = hubsIn($mysqli, $_SESSION['name']);
	$adminHubs = hubsIn($mysqli, $_SESSION['name'], 255);
?>
<script>
$(document).ready(function(){
	$("#login").click(function(e){
		e.preventDefault();
		$.ajax({type: "POST",
            url: "/appHub.php",
            data: { hubLogin: $("#hubLogin").val()},
            success:function(result){
				$("#container").html(result);
			}
		});
  });
  $("#hubLogin").focus();
});
$(document).ready(function(){
	$("#newHub").click(function(e){
		e.preventDefault();
		$.ajax({type: "POST",
            url: "/appHub.php?haha",
            data: { hubName: $("#hubName").val()},
            success:function(result){
				$("#container").html(result);
			}
		});
  });
  $("#hubLogin").focus();
});


</script>
<div class="icon"><img src="styling/Hub.png"><br>
[<a class="ajax" href='appHub.php'> Hub Home </a>]</div>
<div class="iconWide"><img src="styling/documentation.png"><br>
[<a class="ajax" id="newhub" href='appHub.php?documentation'> Documentation </a>]</div>
<?php
if(isset($_SESSION['hub'])) echo "<div class='icon'><img src='styling/logout.png'><br>[<a class='ajax' href='appHub.php?logout'> Logout </a>]</div>";
?>
<div class="icon"><img src="styling/settings.png"><br>
[<a class="ajax" id="newhub" href='appHub.php?settings'> Settings </a>]</div>
<div class="icon"><img src="styling/create.png"><br>
[<a class="ajax" id="newhub" href='appHub.php?newhub'> Create Hub </a>]</div>
<div class="icon"><img src="styling/delete.png"><br>
[<a class="ajax" id="newhub" href='appHub.php?deletehub'> Delete Hub </a>]</div>
<div class="icon"><img src="styling/findHubs.png"><br>
[<a class="ajax" id="newhub" href='appHub.php?findhubs'> Find Hubs </a>]</div>
<pre>
<?php
	if(!isset($_SESSION['hub']) && empty($_GET) && $hubs){
// Hub Login 
?>
<form method="post" action="appHub.php">
	<select id='hubLogin' name='hubName' id='hubName'><?php foreach ($hubs as $hub) echo "<option value='". $hub . "'>$hub</option>"; echo "<option value='All1337'>All</option>"; ?></select> <input type="submit" id="login" name="commit" value="Login">
</form>
<?php
	}
	if(isset($_GET['logout'])){ 
		hubLogout();
		echo "<script>setTimeout(function(){window.location.assign('/appHub.php')})</script>";
	}
	if(isset($_POST['hubLogin'])){  // THis is very dirty to have in the app but it's limiting to put inside the login function because there could be a hub named "all1337"
		if($_POST['hubLogin'] == 'All1337'){
			$hubQuery = $mysqli->query("SELECT hub, status FROM hubUsers WHERE user = '" . $_SESSION['name'] . "' AND status > 0");
			if(mysqli_num_rows($hubQuery)){
				$i = 0;
				while($hubInfo = mysqli_fetch_array($hubQuery, MYSQLI_ASSOC)){
					$_SESSION['hub'][$i] = $hubInfo['hub'];
					$_SESSION['hubStatus'][$i] = $hubInfo['status'];
					$i++;
				}
			}
		}else{
			hubLogin($mysqli,$_SESSION['name'], $_POST['hubLogin']);
		}
		echo "<script>setTimeout(function(){window.location.assign('/appHub.php')})</script>";
	}
	if(isset($_GET['newhub'])){
?>
<form method="post" action="appHub.php?haha">
<input type="text" name="hubName" id="hubName" placeholder='Name your Hub' maxlength='25' size='25'>
<input type="submit" id="newHub" name="commit" value="Create Hub"></form>
<?php
	}
	if(isset($_POST['hubName'])){
		echo hubCreate($mysqli,$_POST['hubName'],$_SESSION['name']);
	}
	
	if(isset($_GET['documentation'])){
		echo "Hubs were designed to provide an easy way to centralize a group of people with a common interest. A hub has its own forums which can be customized, as well as its own IRC channel. <br><br>Currently, there are three distinct types of hubs: <br> Public: Anyone can see and join the hub<br>Private/Moderated: Anyone can see and apply to the hub, but one of the hub administrators must approve whether a member may actually join or not<br>Private/Passworded: Unlisted hub which requires a password to join and the name of the hub<br><br>The hub administrator is responsible for setting up the forum categories and managing the IRC channel";
	}
	
	if(isset($_GET['deletehub'])){
		$hubs = hubsIn($mysqli, $_SESSION['name'], 255);
		if($hubs){
			echo "You are currently allowed to delete the following hubs: <br>";
			foreach ($hubs as $hub){
				echo "[<a class='ajax' href='appHub.php?del=$hub'> $hub </a>]";
			}
		}else echo "You are not allowed to delete any hubs";
	}
	
	if(isset($_GET['del'])){
		echo "Are you sure you want to delete the " . $_GET['del'] . " hub?<br><br><br><br><br><br>";
		echo "[<a class='ajax' href='appHub.php?del2=" . $_GET['del'] . "'> YESSS DELETE IT </a>]<br>";
		echo "[<a class='ajax' href='apps.php'> Whoops dang just get me out of here </a>]";
	}
	
	if(isset($_GET['del2'])){
		echo hubDelete($mysqli, $_GET['del2'], $_SESSION['name']);
	}
	
	if(isset($_GET['leave'])){
		echo hubLeave($mysqli, $_SESSION['name'], $_POST['whichHub']);
	}
	
	if(isset($_GET['settings'])){
		if(!empty($_POST)){
			if(isset($_GET['hub'])){ // Updating settings
				if(!isset($_POST['hubPassword'])) $_POST['hubPassword'] = false;
				echo updateSettings($mysqli, $_POST['whichHub'], $_SESSION['name'], $_POST['availability'], $_POST['description'], $_POST['hubPassword']);
			}
			if(isset($_GET['applicant'])){ // Approving/Denying users
				echo hubApplicantControl($mysqli, $_POST['whichHub'], $_SESSION['name'], $_POST['applicant'], $_POST['applicantControl']);
			}
		}
		if($adminHubs){
			foreach($adminHubs as $hub){
				$row = mysqli_fetch_array($mysqli->query("SELECT * FROM hubs WHERE name = '$hub'"));
				echo "<h1>$hub</h1>";
				echo "<button class='ajax' href='appHub.php?manageForums=$hub'>Add/Remove Forums</button>";
				if($row['availability'] == "moderated"){
					if($applyingUsers = hubApplicants($mysqli, $hub)){
						echo "Applying Users:<br>";
						foreach($applyingUsers as $user){
							echo "<form style='display: inline;' method='post' action='appHub.php?settings&applicant'><a class='ajax' href='appUsers.php?view=" . $user['user'] . "'>" . $user['user'] . "</a> - " . $user['message'] . " <input type='hidden' name='whichHub' value='$hub'><input type='hidden' name='applicant' value='" . $user['user'] . "'><input type='hidden' name='applicantControl' value='0'><input type='submit' value='Deny' class='ajaxForm'></form><form style='display: inline;' method='post' action='appHub.php?settings&applicant'><input type='hidden' name='whichHub' value='$hub'><input type='hidden' name='applicant' value='" . $user['user'] . "'><input type='hidden' name='applicantControl' value='1'><input type='submit' value='Approve' class='ajaxForm'></form>";
							echo "<br>";
						}
					}
				}
?> 
<form method="post" action="appHub.php?settings&hub">
Hub Availability: <select name='availability' id='availability'><option value='public' <?php if($row['availability'] == 'public') echo "selected ='selected'"; ?>>Public</option><option value='moderated' <?php if($row['availability'] == 'moderated') echo "selected ='selected'"; ?>>Public/Moderated</option><option value='private' <?php if($row['availability'] == 'private') echo "selected ='selected'"; ?>>Private/Passworded</option></select>
<?php if($row['availability'] == "private") { 
	echo "<input type='text' size='25' maxlength='25' name='hubPassword' value = '" . $row['password'] . "' placeholder = 'private password'></input><br>";
	echo "<input type='hidden' name='description' value='" . $row['description'] . "'>";
}else{
?>
<input type="text" size="60" maxlength="60" name="description" placeholder="description" value="<?php echo $row['description']; ?>"></input>
<?php } ?>
<input type='hidden' name='whichHub' value='<?php echo $hub; ?>'>
<input type='submit' class='ajaxForm' value='Save Settings for <?php echo $hub; ?>'>
</form>
<?php
			}
		}
		// Loop to display settings for hubs that are not admin hubs!
		if($hubs){
			$hubs = array_diff($hubs, $adminHubs);
			foreach($hubs as $hub){
			echo "<h1>$hub</h1>";
?>
<form method="post" action="appHub.php?leave"><input type='hidden' name='whichHub' value='<?php echo $hub; ?>'><input type='hidden' name='leave'>
<input type='submit' value='Leave <?php echo $hub; ?>' class='formAjax'></form>
<?php
			}
		}
	}
	
	if(isset($_GET['manageForums'])){
		//Hub Forum Create
		if(isset($_POST['category'])){
			echo createForum($mysqli, $_GET['manageForums'], $_SESSION['name'], $_POST['category']);
		}
		echo "<h2>Forum Management for " . $_GET['manageForums'] . "</h2><form method='post' action='appHub.php?manageForums=" . $_GET['manageForums'] ."'><input type='text' name='category' placeholder='Forum Category Name'><input type='submit' class='formAjax' value='New Category'></form>";
		$forums = hubForums($mysqli, $_GET['manageForums']);
		if($forums){
			echo "The following forums exist:";
			foreach($forums as $forum){
				print_r($forum);
				echo "<form method='post' action='appHub.php?deleteForum'><input type='hidden' name='category' value='" . $forum['name'] . "'><input type='hidden' name='hub' value='" . $_GET['manageForums'] . "'><input type='submit' class='formAjax' value='Delete " . $forum['name'] . "'>
						</form>";
			}
		}
	}
	
	if(isset($_GET['deleteForum'])){
			echo deleteForum($mysqli, $_POST['hub'], $_SESSION['name'], $_POST['category']);
	}
	
	if(isset($_GET['hubInfo'])){
		hubExamine($mysqli, $_GET['hubInfo']);
	}
	
	if(isset($_GET['findhubs'])){
		$openHubs = hubFinder($mysqli,$_SESSION['name']);
		echo "<table><tr><td>Name</td><td>Users</td><td>Description</td></tr>";
		foreach($openHubs as $hub){
			echo "<tr><td><a class='ajax' href='appHub.php?hubInfo=". $hub['name'] . "'>" . $hub['name'] . "</a></td><td><center>" . $hub['COUNT(*)'] . "</center></td><td>" . $hub['description'] . "</td></tr>";
		}
		echo "</table>";
	}
	
	if(isset($_GET['join'])){
		if($hubInfo = hubQuery($mysqli, $_GET['join'])){
			echo "Would you like to join the " . $hubInfo['name'] . " hub?";
?>
<form method="post" action="appHub.php?join<?php echo "=" . $_GET['join']; ?>">
<?php
	if($hubInfo['availability'] == 'moderated') echo "<textarea rows='4' cols='40' placeholder='This hub requires an admin to moderate which users join. You may attach a message with your application' name='message'></textarea><br>";
	elseif($hubInfo['availability'] == "private") echo "<input type='password' name='password' placeholder='Key'>";
?>
<input type='hidden' name='join' value='<?php echo "=" . $_GET['join']; ?>'>
<input type='submit' class='ajaxForm' value='Join Hub'>
</form>
<?php

		}else echo "Bad Join!!";
	}

	//Hub Join
	if(isset($_POST['join'])){ 
		$userInfo = "";
		if(isset($_POST['message'])) $userInfo = $_POST['message'];
		if(isset($_POST['password'])) $userInfo = $_POST['password'];
		echo hubJoin($mysqli, $_SESSION['name'], $_GET['join'], $userInfo);
	}
}else echo "Hubs aren't available for public use"; //If user isn't logged in
?></pre><?php
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){}else{
	require_once("foot.php");
}
?>