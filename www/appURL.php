<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '../../mysqlConnect.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../settings.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../randomFunctions.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/userSession.php');

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){}else{
	require_once("head.php");
}

if($_SESSION['logged']){
?>
<pre>
___   ___         ___      ____    ____ 
\  \ /  /         \  \     \   \  /   / 
 \  V  /      _____\  \     \   \/   /  
  >   <      |______>  >     \_    _/   
 /  .  \           /  /        |  |     
/__/ \__\         /__/         |__|     
                                        
<form method="post" action="appURL.php">
<input type="text" name="name" autofocus id="name" value="<?php if(isset($_POST['name'])) echo $_POST['name']; ?>" placeholder="Shortcode">

<input type="text" name="url" id="url" style='width: 75%;' value="<?php if(isset($_POST['url'])) echo $_POST['url']; ?>" placeholder="Address">
<input type="submit" class='ajaxForm' name="commit" value="Add"></form>
<?php 
// ON POST INPUT
// We have a code
if(isset($_POST['name'])){ 
	echo makeURL($mysqli); 
}
// User deletes a URL
if(isset($_POST['urlDel'])){
		$urlDel = $mysqli->real_escape_string($_POST['urlDel']);
		$mysqli->query("DELETE FROM urls WHERE createdby = '" . $_SESSION['name'] . "' AND id = $urlDel");
		echo "The url rapidly dissipates into particles of unobservable size<br>";
}

// Display list of user's URLs
$username = $_SESSION['name'];
$result = $mysqli->query("SELECT * FROM urls WHERE createdby = '$username' ORDER BY hits DESC");
if (mysqli_num_rows($result))
{
	echo "<br>Your shortcodes: ";
	echo "<table cellspacing=\"5\"><tr><td>Hits</td><td>Link</td><td>Created on</td><td></td></tr>";
	while ($row = mysqli_fetch_array($result))
	{
		echo "<tr class='removable'><td class='hits'>" . $row['hits'] . "</td><td class='code'><a title='" . $row['url'] . "' class='link' target='_blank' href='/" . $row['code'] . "'>" . $row['code'] . "</a></td><td>" . substr($row['created'], 0, 10) . "</td><td><form action='appURL.php' method='post'><input type='hidden' name='urlDel' value='".$row['id']."'><input type='submit' class='ajaxForm' value='Delete'></form></td></tr>";
	}
	echo "</table></pre>";
}

} // if($_SESSION['logged']){ close

function makeURL($sql){
	$error = "You must enter a shortcode!";
	if(isset($_POST['name']) && $_POST['name'] == "") return $error; // Make sure a shortcode was sent
	else{ //Got a name from em
		$_POST['name'] = $sql->real_escape_string($_POST['name']); 
		$data = $sql->query("SELECT * FROM urls WHERE code = '" . $_POST['name'] . "'");
		if($data = mysqli_fetch_array($data)){ // Exists already
			$resolve = "<pre>Url found in mySQL<br> " . $data['code'] . " - > " . $data['url'] . "<br>Hits : " . $data['hits'] . "<br>[<a href='http://op2rules.net/".$data['code']."'>op2rules.net/".$data['code']."</a>]</pre>";
			return $resolve;
		}elseif(isset($_POST['url']) && $_POST['url'] != ""){ // Add url to database (maybe check for legit url?)
			$_POST['url'] = $sql->real_escape_string($_POST['url']);
			$urlQuery = $sql->query("INSERT INTO urls (url, code, createdby) VALUES ('" . $_POST['url'] . "', '" . $_POST['name'] . "' , '" . $_SESSION['name'] . "')");
			logThis($sql,"URL",$_SESSION['name'],$_POST['name']); 
			return "<pre>Successfully Added to mySQL Database<br>[<a href='http://op2rules.net/".$_POST['name']."'>op2rules.net/".$_POST['name']."</a>]</pre> ";
		}else $error = "Well that url won't take you far";
		return $error;
	}
}

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){}else{
	require_once("foot.php");
}