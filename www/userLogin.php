<?php
//The most important feature to have when logging in is to allow the website to remember you but only if you didn't log out
//Bundled with functional email features --a "lost password" feature is also mandatory
//"sticky forms" is an important concept where all the data exchanged between logins is ajax so that no new pages are loaded and no fields are erased
require_once($_SERVER['DOCUMENT_ROOT'] . '../../mysqlConnect.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../settings.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../randomFunctions.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/userSession.php');

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){}else{
	require_once("head.php");
}

if(isset($_POST['password']) && isset($_POST['name']) && $_POST['password'] != ""){
	$_SESSION['response'] = userlogin($mysqli);
}
if($_SESSION['response']){
	echo $_SESSION['response'];
	$_SESSION['response'] = 0;
}
else{
	 ?> <div class="login">
      <pre>
____                  __        
|    |    ____   ____ |__| ____  
|    |   /  _ \ / ___\|  |/    \ 
|    |__(  <_> ) /_/  >  |   |  \
|_______ \____/\___  /|__|___|  /
        \/    /_____/         \/ 
		</pre>
<script>
$(document).ready(function(){
  $("#submit").click(function(e){
      e.preventDefault();
    $.ajax({type: "POST",
            url: "/userLogin.php",
            data: { name: $("#name").val(), password: $("#pass").val() },
            success:function(result){
      $("#container").html(result);
    }});
  });
});
</script>
      <form method="post" action="userLogin.php">
        <p><input type="text" name="name" autofocus id="name" value="<?php if(isset($_POST['name'])) echo $_POST['name']; ?>" placeholder="Account Name"></p>
        <p><input type="password" name="password" id="pass" value="" placeholder="Password"></p>
        <p><input type="submit" id="submit" name="commit" value="Login"></p>
      </form>
    </div>
	<a class='ajax' href='userPassword.php'>Reset Password</a><?php 
}
if(isset($_POST['password']) && $_POST['password'] == "") echo "Well that password won't get you far";
//Check posted data
function userLogin($sql){
	global $salt;
	if(!$_POST['name']){
		$error = "A name must be entered";
		return $error;
	}
	if(!$_POST['password']){
		$error = "A password must be given";
		return $error;
	}
	else{
		//Prepare for queries
		$_POST['name'] = $sql->real_escape_string($_POST['name']);
		$_POST['password'] = $sql->real_escape_string(sha1($_POST['password'] . $salt));
		/*if(isset($_POST['hub'])){ // Hub implementation
			$_POST['hub'] = $mysqli->real_escape_string($_POST['hub']);
		}
		else{
			$_POST['hub'] = null;
		}*/
		//Check if user is in database
		$checkName = $sql->query(" SELECT * FROM users WHERE name = '" . $_POST['name'] . "'");
		if(mysqli_num_rows($checkName) == 0){
			$error = "The username you've typed in doesn't exist in the op2rules database";
			return $error;
		}
		else{
			//Compare username and password
			$loginQuery = $sql->query("SELECT * FROM users WHERE name='" . $_POST['name'] . "' AND pass='" . $_POST['password'] . "'");
			$count = mysqli_num_rows($loginQuery);
			if($count >= 1){
				$sql->query("UPDATE users SET logins=logins+1, lastlogin=now() WHERE name='" . $_POST['name'] . "'"); 
				$user = mysqli_fetch_array($loginQuery);
				$_SESSION['name'] = $user['name'];
				$_SESSION['id'] = $user['id'];
				$_SESSION['logged'] = 1;
				$_SESSION['status'] = $user['status'];
				$_SESSION['theme'] = $user['theme'];
				$_SESSION['email'] = $user['email'];
				$_SESSION['hotSettings'] = $user['hotSettings'];
				// login to every hub
				$hubQuery = $sql->query("SELECT hub, status FROM hubUsers WHERE user = '" . $user['name'] . "' AND status > 0");
				if(mysqli_num_rows($hubQuery)){
					$i = 0;
					while($hubInfo = mysqli_fetch_array($hubQuery, MYSQLI_ASSOC)){
							$_SESSION['hub'][$i] = $hubInfo['hub'];
							$_SESSION['hubStatus'][$i] = $hubInfo['status'];
							$i++;
					}
				}
				logThis($sql, "login", $user['name']);
				return "Logging in....<br><script>setTimeout(function(){window.location.assign('/')})</script>"; 
			}
			else{ //Couldn't find entry in table
				return "The wrong password key has been entered to gain access to " . $_POST['name'];  
			}
		}
	}
}
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){}else{
	require_once("foot.php");
}