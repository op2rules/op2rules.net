<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '../../mysqlConnect.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../settings.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../randomFunctions.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/userSession.php');

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){}else{
	require_once("head.php");
}

if(isset($_POST['password']) && isset($_POST['name']) && $_POST['password'] != "" && isset($_POST['captcha']) && md5($_POST['captcha']) == $_SESSION['key']){
	$_SESSION['response'] = makeUser($mysqli);
}
?>
<div class="login">
<pre>
__________               __          __                
\______   \ ____   ____ |__| _______/  |_  ___________ 
 |       _// __ \ / ___\|  |/  ___/\   __\/ __ \_  __ \
 |    |   \  ___// /_/  >  |\___ \  |  | \  ___/|  | \/
 |____|_  /\___  >___  /|__/____  > |__|  \___  >__|   
        \/     \/_____/         \/            \/       
</pre>
<script>
$(document).ready(function(){
  $("#submit").click(function(e){
      e.preventDefault();
    $.ajax({type: "POST",
            url: "/userRegister.php",
            data: { name: $("#name").val(), password: $("#pass").val(), email: $("#email").val(), captcha: $("#captcha").val()},
            success:function(result){
      $("#container").html(result);
    }});
  });
});
</script>
      <form method="post" action="userRegister.php">
        <p><input type="text" autofocus name="name" maxlength="20" id="name" value="<?php if(isset($_POST['name'])) echo $_POST['name']; ?>" placeholder="Account Name"></p>
        <p><input type="password"  name="password" id="pass" value="<?php if(isset($_POST['password'])) echo $_POST['password']; ?>" placeholder="Password"></p>
        <p><input type="text" name="email"  id="email" value="<?php if(isset($_POST['email'])) echo $_POST['email']; ?>" placeholder="Email"></p>
		<img src="captcha/captcha.php"> 
		<p><input type="captcha" id="captcha" name="captcha" placeholder="Macro Check"</p>
        <p><input type="submit" id="submit" name="commit" value="Register"></p>
      </form>
    </div>

	
<?php 
if($_SESSION['response']){
	echo $_SESSION['response'];
	$_SESSION['response'] = 0;
}

//Highlight empty forms blue
if(isset($_POST['name']) && $_POST['name'] == "") { ?> <script>
$("#name").css({ "background-color": '<?php echo $color2 ?>', "border": '<?php echo $color2 ?> 2px solid'})</script>
<?php }

if(isset($_POST['password']) && $_POST['password'] == "") { ?> <script>
$("#pass").css({ "background-color": '<?php echo $color2 ?>', "border": '<?php echo $color2 ?> 2px solid'})</script>
<?php }

if(isset($_POST['email']) && $_POST['email'] == "") { ?> <script>
$("#email").css({ "background-color": '<?php echo $color2 ?>', "border": '<?php echo $color2 ?> 2px solid'})</script>
<?php }

if(isset($_POST['captcha']) && md5($_POST['captcha']) != $_SESSION['key']) echo "<pre>Wrong captcha</pre>"; 

function makeUser($sql){
	global $salt;
	
	//Parse
	$_POST['name'] = (strlen($_POST['name']) > 20) ? substr($_POST['name'], 0, 20) : $_POST['name'];
	$_POST['name'] = $sql->real_escape_string($_POST['name']);
	$_POST['password'] = $sql->real_escape_string(sha1($_POST['password'] . $salt));
	if(isset($_POST['email'])){
		$_POST['email'] = $sql->real_escape_string($_POST['email']);
	}
	else{
		$_POST['email'] = null;
	}
	
	//Check if user already exists in database
		$checkName = $sql->query(" SELECT * FROM users WHERE name = '" . $_POST['name'] . "'");
		if(mysqli_num_rows($checkName) == 1){
			return "Unfortunately that username has already been taken";
		}elseif(!alphanumericUnderscore($_POST['name'])){
			return "Your username may only contain letters, numbers, and underscore";
		}
		else{	
//Load the data into the database
			$registerQuery = $sql->query("INSERT INTO users (name, pass, email) VALUES ('" . $_POST['name'] . "', '" . $_POST['password'] . "', '" . $_POST['email'] . "')");
			if($registerQuery){
				logThis($sql, "register", $_POST['name']);
				return "Your entries have been put in the database. You may now login";
			}
			else{
				$error = mysqli_error();
				logThis($sql,"register error", $_POST['name'], $error);
				return "Something went horribly wrong while creating your account!";
			}
		}		
}

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){}else{
	require_once("foot.php");
}




