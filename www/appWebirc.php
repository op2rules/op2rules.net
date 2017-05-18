<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/userSession.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../mysqlConnect.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../settings.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../randomFunctions.php');

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){}else{
	require_once("head.php");
}

if($_SESSION['logged']){
?>
<script>
$(document).ready(function(){
	$("#logs").hide(); // Hide the initial logs
	
	$("#hide").click(function(){
		$("#logs").hide();
	});
	
	$("#show").click(function(){ 
		$("#logs").show(); 
	});

});
</script>
<iframe id="webirc" src="http://185.59.204.160:9090/?nick=<?php echo $_SESSION['name']; echo "^"; ?>&channels=op2rules&uio=MTE9MTQ05a" width="647" height="400"></iframe>
<?
$logs = getIRCLogs($mysqli, "#op2rules");
/*echo "<br>[<a href='#' onclick='return false;' id='show'> Show Previous Messages in #op2rules </a>] [<a href='#' onclick='return false;' id='hide'> Hide Logs </a>]";
echo "<table id='logs'><tr><td><center>User</center></td><td><center>Message</center></td><td><center>Time</center></td></tr>";
foreach($logs as $log){
	$timeSince = differenceTime($log['time']);
	echo "<tr><td>" . $log['user'] . "></td><td> " . $log['message'] . "</td><td>$timeSince</td></tr>";
}
echo "</table>";*/
///version 2
echo "<textarea rows='6' cols='100'>";
foreach($logs as $log){
	$timeSince = differenceTime($log['time']);
	echo "\n[$timeSince] " . $log['user'] . "> " . $log['message'];
}
echo "</textarea>";
}
else echo "Login to use webIRC";
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){}else{
	require_once("foot.php");
}