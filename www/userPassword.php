<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/userSession.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../settings.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../randomFunctions.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../mysqlConnect.php');

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){}else{
	require_once("head.php");
}
?><pre><center><?php	
if($_SESSION['logged']){
		echo "No need to reset your password. You're already logged in!";
}else{?>
<script>
$(document).ready(function(){
  $("#submit").click(function(e){
      e.preventDefault();
    $.ajax({type: "POST",
            url: "/userPassword.php",
            data: { email: $("#email").val()},
            success:function(result){
      $("#container").html(result);
    }});
  });
});
</script>
Warning: For some reason Gmail is still not working. If you registered with a gmail account you can either contact Patman1/op2rules, or register a new name. Sorry for the inconvenience!
<form method="post" action="userPassword.php">
<input type="text" style='width: 400px;' name="email" autofocus id="email" value="" placeholder="Email">
<input type="submit" id="submit" name="commit" value="Send Reset Form">
</form>
<?php	
if(isset($_POST['email'])){//Check Email
	$response = resetPassword($mysqli, $_POST['email']);
	echo $response;
}

} ?><?php
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){}else{
	require_once("foot.php");
}
?>