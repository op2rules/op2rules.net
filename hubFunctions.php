<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '../../mysqlConnect.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../settings.php');
// Because now we can just call these functions from appHub.php...
/*@@
hubName = varChar(25)

@@@@@ ______ TODO:
hubStatusChange() // Allow a hub admin to promote users
hubCategory($parent = null) 
hubCategoryDelete() // Removes posts with option to migrate them as well


hubLimit() check to return a 1 if a user has status their name listed 3 times with status 255 in the hubUsers table or 5 times in total
@@*/

function hubCreate($sql, $hubName, $username){
	if(!alphanumericUnderscore($hubName)) return "Hub name may only contain alpha numeric and underscore characters";
	$hubName = $sql->real_escape_string($hubName); 
	$error = false;
	$errormsg = "Could not create hub. You can either contact op2rules/Patman1 by posting a message on the [ <a class='ajax' href='forumFront.php'> Forum </a>], or alternatively try different settings";
	if(!$sql->query("INSERT INTO hubs (name) VALUES ('$hubName')")){
		$error = $sql->error;
		if($sql->errno == 1062) $errormsg = "That hub name already exists";
		logThis($sql,"HubCreate",$username,$error);
		return $errormsg;
	}
	if(!$sql->query("INSERT INTO hubUsers (hub, user, status) VALUES ('$hubName', '$username', 255)")){
		$error = $sql->error;
		logThis($sql,"hubCreate",$username,$error);
		return $errormsg;
	}
	return "Hub with name $hubName was created by $username!<br>You are the only administrator for this hub right now and can begin creating forum categories or tweaking with the settings";
}

//Checks if a user has admin(255) or user defined status code to allow for function calls to proceed. Returns 1 if user has access or 0 if they're dicking around
function checkStatus($sql, $username, $hubName, $status = 255){
	$hubName = $sql->real_escape_string($hubName);
	if($result = $sql->query("SELECT status FROM hubUsers WHERE hub = '$hubName' AND user = '$username'")){
		$row = $result->fetch_assoc();
	}else{
		$error = $sql->error;
		logThis($sql,"checkStatus",$username,$error);
	}	
	if($row['status'] >= $status) return true;
	return false;
}

// hubDelete should delete all forum posts and warn user about doing so
function hubDelete($sql, $hubName, $username){
	$hubName = $sql->real_escape_string($hubName);
	if(!checkStatus($sql, $username, $hubName)) return "You have insufficient privileges to delete this hub";
	$sql->query("DELETE FROM hubs WHERE name = '$hubName'");
	$sql->query("DELETE FROM hubUsers WHERE hub = '$hubName'");
	return "$hubName vanishes into the void";
}

// Returns an array of each hub the user is a member of or false if no hubs found
function hubsIn($sql, $username, $status = 1){
	if($result = $sql->query("SELECT hub FROM hubUsers WHERE user = '$username' AND status >= $status")){
		$i = mysqli_num_rows($result);
		if($i == 0) return 0;
		$hubs = array();
		for($i; $row = $result->fetch_array(MYSQLI_NUM);$i--){
			$hubs[$i] = $row[0];
		}
		return $hubs;
	}
	return false;
}

function hubLogin($sql, $username, $hubName){
	$hubName = $sql->real_escape_string($hubName);
	if(!$result = $sql->query("SELECT * FROM hubUsers WHERE hub = '$hubName'")){
		$error = $sql->error;
		logThis($sql,"hubLogin",$username,$error);
		return "Can't login for some odd reason. Contact op2rules/Patman1 via forums for more help";
	}
	$row = $result->fetch_array(MYSQLI_ASSOC);
	if($row['status'] == 0) return "You are not allowed in this hub";
	$_SESSION['hub'][0] = $hubName;
	$_SESSION['hubStatus'][0] = $row['status'];
	return "Logged into " . $hubName;
}

function hubLogout(){
	unset($_SESSION['hub']);
	unset($_SESSION['hubStatus']);
	return "Your hub is now out of session";
}

// Admin functions for hub parameters
function updateSettings($sql, $hubName, $username, $availability, $description, $password){
	$hubName = $sql->real_escape_string($hubName);
	if(!checkStatus($sql, $username, $hubName)) return "You have insufficient privileges";
	if($availability != "private" && $availability != "public" && $availability != "moderated") return "Bad availability... stop messing with things";
	$hubName = htmlentities($hubName);
	$description = htmlentities($description);
	$availability = $sql->real_escape_string($availability);
	$description = $sql->real_escape_string($description);
	$password = $sql->real_escape_string($password);
	if(!$sql->query("UPDATE hubs SET availability = '$availability', description = '$description', password = '$password' WHERE name = '$hubName'")){
		$error = $sql->error;
		logThis($sql,"updateSettings()", $username, $error);
	}
	return "Settings for $hubName have been updated<br>";
}

//returns an array of hubs available to public only reads database
function hubFinder($sql,$username){
	if($result = $sql->query("SELECT name, availability, description, COUNT(*) FROM hubs JOIN hubUsers ON hubs.name = hubUsers.hub WHERE availability != 'private' GROUP BY name ORDER BY COUNT(*) DESC")){
		while($row = mysqli_fetch_array($result)){
			$return[] = $row;
		}
	}else{
		$error = $sql->error;
		logThis($sql,"hubFinder",$username,$error);
	}
	return $return;
}

//Prints out various hub information to the user .. only reads database
function hubExamine($sql,$hubName){
	$result = $sql->query("SELECT * FROM hubs WHERE name = '$hubName'");
	if(mysqli_num_rows($result) == 0) return 0;
	$hub = mysqli_fetch_array($result);
	if($result2 = $sql->query("SELECT user,status FROM hubUsers WHERE hub = '$hubName' ORDER BY status DESC")){
		$usercount = mysqli_num_rows($result2);
	}
	if($hub['availability'] == 'private') return 0;
	echo $hub['name'] . "<br><br>";
	echo "Availability: " . $hub['availability'] . "<br>";
	echo "Users: " . $usercount . "<br><br>";
	if($usercount > 0){
		while($row = $result2->fetch_array()){
			if($row['status'] == 255) echo "Administrator: ";
			if($row['status'] == 100) echo "Moderator: ";
			if($row['status'] == 0) echo "Pending/Banned: ";
			echo $row['user'] . "<br>";
		}
	}
	echo "[<a class='ajax' href='appHub.php?join=" . $hub['name'] . "'> Join </a>]"; //Display this only if you're not already a member
}

//returns array with all info about a hub or 0 if hub doesn't exist
function hubQuery($sql, $hubName){
	$hubName = $sql->real_escape_string($hubName); // Just incase
	$result = $sql->query("SELECT * from hubs WHERE name = '$hubName'");
	if(mysqli_num_rows($result) == 0) return 0;
	$data = mysqli_fetch_array($result);
	return $data;
}

//Cleans data and submits an application to sql or joins the user
// 1. Escape html_entity / escape string
// 2. Check if hub exists
// 3. Check permission of hub
// 4. Determine if a password is requried, or join the hub or submit application
function hubJoin($sql,$username,$hubName,$userInfo){
	if($userInfo != ""){
		$userInfo = htmlentities($userInfo);
		$userInfo = $sql->real_escape_string($userInfo);
	}
	$hubName = $sql->real_escape_string($hubName);
	$hubName = htmlentities($hubName);
	if($row = hubQuery($sql, $hubName)){
		if($row['availability'] == 'public'){
			$sql->query("INSERT INTO hubUsers (hub, user, status) VALUES ('$hubName', '$username', 1)");
			if($sql->errno == 1062) return "You are already a member of $hubName.";
			if($sql->errno){
				$error = $sql->error;
				logThis($sql, "HubJoin", $username, $error);
				return "error!";
			}
			return "You are a member of $hubName";
		}
		if($row['availability'] == 'moderated'){
			$sql->query("INSERT INTO hubApplications (hub, user, message) VALUES ('$hubName', '$username', '$userInfo')");
			if($sql->errno == 1062) return "You have already attempted to join $hubName";
			return "Once your application has been moderated a note will be sent to you";
		}
		if($row['availability'] == 'private'){
			if($row['password'] == $userInfo){
				$sql->query("INSERT INTO hubUsers (hub, user, status) VALUES ('$hubName', '$username', 1)");
				if($sql->errno == 1062) return "You are already a member of $hubName.";
				return "You've opened the door to $hubName!";
			}else return "Wrong password for $hubName";
		}
	}else return "Hub does not exist";
}

function hubLeave($sql, $username, $hubName){
	$hubName = $sql->real_escape_string($hubName);
	$sql->query("DELETE FROM hubUsers WHERE user = '$username' AND hub = '$hubName'");
	if($sql->error){
		$error = $sql->error;
		logThis($sql, "hubLeave", $username, $error);
	}
	return "$username leaves $hubName behind...";
}

//Returns an array, if any, of each application for a given hub
function hubApplicants($sql, $hubName){
	$hubName = $sql->real_escape_string($hubName);
	if($result = $sql->query("SELECT user,message FROM hubApplications WHERE hub = '$hubName'")){
		$i = mysqli_num_rows($result);
		if($i == 0) return 0;
		$applicants = array();
		for($i; $row = $result->fetch_array(MYSQLI_ASSOC);$i--){
			$applicants[$i] = $row;
		}
		return $applicants;
	}
	return false;
}

//$control = 1 for approve or 0 for deny
function hubApplicantControl($sql, $hubName, $username, $applicant, $control){
	$hubName = $sql->real_escape_string($hubName);
	$applicant = $sql->real_escape_string($applicant);
	$control = $sql->real_escape_string($control);
	if(!checkStatus($sql, $username, $hubName)) return "You have insufficient privileges";
	if($control == 0){
		$sql->query("DELETE FROM hubApplications WHERE hub = '$hubName' AND user = '$applicant'");
		if($sql->error){
			$error = $sql->error;
			logThis($sql, "hubApplicantControl($hubName, $applicant, 1)",$username,$error);
		}
		$sql->query("INSERT INTO notes (name, note, createdby, createdfor, seen) 
		VALUES ('Application for $hubName', 'Unfortunately, your application has been denied' , 'System' , '$applicant', 0)");
		return "$applicant has been denied";
	}
	if($control == 1){
		$error = false;
		$errormsg = "Uh oh! Something broke! Details have been logged. Let an administrator know about this if you are unsure of what to do";
		$sql->query("INSERT INTO hubUsers (hub,user,status) VALUES ('$hubName', '$applicant', 1)");
		if($sql->error){ 
			$error = $sql->error;
			if($sql->errno == 1062) $errormsg = "$applicant is already a member of $hubName";
		}
		$sql->query("DELETE FROM hubApplications WHERE hub = '$hubName' AND user = '$applicant'");
		if($sql->error) $error = $sql->error;
		if($error){
			logThis($sql, "hubApplicantControl($hubName, $applicant, 0)", $username, $error);
			return $errormsg;
		}
		$sql->query("INSERT INTO notes (name, note, createdby, createdfor, seen) 
		VALUES ('Application for $hubName', 'You have been given access to $hubName by $username' , 'System' , '$applicant', 0)");
		return "$applicant has been given access to $hubName";
	}
	
}

// Returns 0 or a numerical array of the forums available
function hubForums($sql, $hubName){
	$hubName = $sql->real_escape_string($hubName);
	$hubName = hubQuery($sql, $hubName); // Expand name into an array with id as well
	$id = $hubName['id'];
	if($result = $sql->query("SELECT * FROM forumCategory WHERE class=3 AND parentid=$id")){
		$i = mysqli_num_rows($result);
		if($i == 0) return 0;
		$forums = array();
		for($i; $row = $result->fetch_array(MYSQLI_ASSOC);$i--){
			$forums[$i] = $row;
		}
		return $forums;
	}
	return false;
}

//Should have error logging etc
function createForum($sql, $hubName, $username, $category){
	$category = htmlentities($category);
	$info = hubQuery($sql, $hubName);
	$id = $info['id'];
	$hubName = $sql->real_escape_string($hubName);
	$category = $sql->real_escape_string($category);
	if(!checkStatus($sql,$username,$hubName)) return "You have insufficient power!!!";
	$result = $sql->query("INSERT INTO forumCategory (name, class, parentid, status) VALUES ('$category', 3, $id, 1)");
}

// querys only log, broken, do not use
function deleteForum($sql, $hubName, $username, $category){
	$category = htmlentities($category);
	$hubName = $sql->real_escape_string($hubName);
	$category = $sql->real_escape_string($category);
	if(!checkStatus($sql,$username,$hubName)) return "You have insufficient power!!!";
	// delete replies
	$sql->query("DELETE * FROM forumReplies JOIN forumTopics ON forumReplies.topic = forumTopics.id JOIN forumCategory ON forumTopics.category = forumCategory.id WHERE forumCategory.name = '$category'") or logThis($sql, "Delete replies", $username, mysql_error());
	// delete topics
	$sql->query("DELETE * FROM forumTopics JOIN forumCategory ON forumCategory.id = forumTopics.category WHERE forumTopics.hub = '$hubName' AND forumCategory.name = '$category'") or logThis($sql, "Delete topics", $username, mysql_error());
	// delete category
	$sql->query("DELETE * FROM forumCategory where forumCategory.name = '$category'") or logThis($sql, "Delete category", $username, mysql_error());
	return "$category begins shrinking, pulling all of its topics and replies with it";
}
?>