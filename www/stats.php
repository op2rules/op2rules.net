<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/userSession.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../settings.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../randomFunctions.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../mysqlConnect.php');

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){}else{
	require_once("head.php");
}
?>
<pre>
<?php
if($_SESSION['logged']){

$result = $mysqli->query("SELECT * FROM users WHERE name='" . $_SESSION['name'] . "'");
$user = mysqli_fetch_array($result);

echo "[ User ]<br><br>";
echo "Last Login: " . substr($user['lastlogin'],0,10) . "<br>";
echo "Visits: " . $user['logins'] . "<br><br>";

echo "[ Website ]<br><br>";
$query1 = $mysqli->query("SELECT COUNT(*) FROM users");
$userCount = mysqli_fetch_row($query1);
echo "Users: " . $userCount[0] . "<br><br>";

$query2 = $mysqli->query("SELECT COUNT(*) FROM forumTopics");
$forumTopics = mysqli_fetch_row($query2);
echo "Forum Topics: " . $forumTopics[0] . "<br>";
$query3 = $mysqli->query("SELECT COUNT(*) FROM forumReplies");
$forumReplies = mysqli_fetch_row($query3);
echo "Forum Replies: " . $forumReplies[0] . "<br><br>";

$query4 = $mysqli->query("SELECT COUNT(*) FROM urls");
$urlCodes = mysqli_fetch_row($query4);
echo "URL shortcodes: " . $urlCodes[0] . "<br><br>";

$query5 = $mysqli->query("SELECT COUNT(*) FROM notes");
$IPutTheNotes = mysqli_fetch_row($query5);
echo "Notes: " . $IPutTheNotes[0] . "<br><br>";

echo "[ IRC ]<br><br>";

$query6 = "SELECT * FROM uptimeTable WHERE ID = '1'";
$query7 = "SELECT * FROM statsTable WHERE ID = '1'";
$str = " lines per minute <br>";
$result1 = $mysqli->query($query6);
$result2 = $mysqli->query($query7);

while ($row = mysqli_fetch_array($result1))
{
	echo "Hot's uptime: " . convertseconds($row['uptime']) . "<br>";
	echo "Hot's uptime record: " . convertseconds($row['uptimerecord']) . "<br>";
}
while ($row = mysqli_fetch_array($result2))	
{
	if ($row['LPM'] == 1){$str = " line per minute <br>";}
	echo "Past hour average activity: " . $row['LPM'] . $str;
}
$result1->close();
$result2->close();
echo "<br>";

$df = disk_free_space("/");
$df = $df / 1048576;
	
$dt = disk_total_space("/");
$dt = $dt / 1048576;

$du = $dt - $df;

echo "[ Server ]<br><br>";
	
echo (int)$du . "MB used";
echo "<br>" . (int)$dt . "MB total";
echo "<br>" . (int)$df . "MB free";

echo "<br><br>" . sayQuote();
}
?> </pre> <?php
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){}else{
	require_once("foot.php");
}
?>
