<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/userSession.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../settings.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../randomFunctions.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../mysqlConnect.php');

//INPUT HANDLING
if(isset($_POST['color'])){
	$color = $mysqli->real_escape_string($_POST['color']);
	$query = "UPDATE users SET theme='$color' WHERE name = '". $_SESSION['name'] . "'";
	$mysqli->query($query);
	$_SESSION['theme'] = $color;
}

if(isset($_POST['email'])){
	$email = $mysqli->real_escape_string($_POST['email']);
	$query = "UPDATE users SET email='$email' WHERE name = '". $_SESSION['name'] . "'";
	$mysqli->query($query);
	$_SESSION['email'] = $email;
	$_SESSION['response'] = "Email was saved as $email";
}

if(isset($_POST['hotSettings'])){
	$hotSettings = $mysqli->real_escape_string($_POST['hotSettings']);
	if($hotSettings != 0) $hotSettings = 1;
	$query = "UPDATE users SET hotSettings = '". $hotSettings . "' WHERE name = '" . $_SESSION['name'] . "'";
	$mysqli->query($query);
	if($hotSettings == 0) { // Reset irc confirmation when you disable settings niggaaa
		$query = "UPDATE users SET ircConfirmation = '0' WHERE name = '" . $_SESSION['name'] ."'";
		$mysqli->query($query);
	}
	$_SESSION['hotSettings'] = $hotSettings;
	if($hotSettings == 1) $hotSettings = "Enabled, type !identnick on an IRC channel with Hot to confirm";
	else $hotSettings = "Disabled";
	$_SESSION['response'] = "Hot-Settings were $hotSettings";
}

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){}else{
	require_once("head.php");
}
?><pre>
<script>
$(document).ready(function(){
	$("#submit1").click(function(e){
		e.preventDefault();
		$.ajax({type: "POST",
            url: "/userSettings.php",
            data: { email: $("#email").val() },
            success:function(result){
				$("#container").html(result);
			}
		});
  });
});
$(document).ready(function(){
	$("#submit2").click(function(e){
		e.preventDefault();
		$.ajax({type: "POST",
            url: "/userSettings.php",
            data: { hotSettings: $("#hotSettings").val() },
            success:function(result){
				$("#container").html(result);
			}
		});
  });
});
</script>
Color Theme
<form method="post" action="userSettings.php">
<?php
if($_SESSION['logged']){
		
		$numIndex = count($popoutcolor) - 1;
		while($numIndex >= 0){
			echo "<table style='display:table-cell'><tr><td width='50px' height='20px' bgcolor='" . $popoutcolor[$numIndex] . "'></td></tr><tr><td width='50px' height='20px' bgcolor='" . $backgroundcolor[$numIndex] . "'> </td></tr><tr><td><input type='radio' name='color' value='$numIndex' >$numIndex</td></tr></table>";
			$numIndex--;
		}	
?>
<table style='display:inline-block'><tr><td width='50px' height='20px'><center>?</center></td></tr><tr><td width='50px' height='20px' ><center>?</center></td></tr><tr><td><input type='radio' name='color' value='255' checked>Default</td></tr><tr><td><input type='submit'></td></tr></table>
</form>
<form method="post" action="userSettings.php">
	<input type="text" name="email" id="email" value="<?php echo $_SESSION['email']; ?>"><input type="submit" id="submit1" value="Update">
</form>
<?php 
if($_SESSION['hotSettings'] == 0){?>
<form method="post" action="userSettings.php">
	<input type="hidden" name="hotSettings" id="hotSettings" value="1"><input type="submit" id="submit2" value="Enable Hot-Settings"> 
</form>
<?php }else{ ?>
<form method="post" action="userSettings.php">
	<input type="hidden" name="hotSettings" id="hotSettings" value="0"><input type="submit" id="submit2" value="Disable Hot-Settings"> 
</form>
<?php
}
if($_SESSION['response'] != ""){
	echo "<br>" . $_SESSION['response'];
	$_SESSION['response'] = "";
}
		
}else echo "Settings? Easy there bessie, you're not even logged in";
?> </pre> <?php
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){}else{
	require_once("foot.php");
}
?>