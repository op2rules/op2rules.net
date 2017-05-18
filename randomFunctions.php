<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '../../mysqlConnect.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../settings.php');
// For all those functions that are otherwise known as ... 'sneaky' business...
 
// Protips quotes
function sayQuote($a = 0){
	$quotes = array("Logging into an account is handy", 
				"IE works like shit on op2rules.net, reminding you to get a better browser", 
				"op2rules is probably programming on op2rules.net right now",
				"Everything while logged out is a hoax",
				"We're always looking for developers in #dev",
				"AJAX is why you're loving this place",
				"God doesn't play dice with the world",
				"Protip: use [tab] to access the nav links",
				"New content is added when you least expect it",
				"Every login is recorded");

	$key = array_rand($quotes);
	if($a==1) echo "<pre>" . $quotes[$key] . "</pre>";
	return $quotes[$key];
}
 
function userIP(){ 
    if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
    $TheIp=$_SERVER['HTTP_X_FORWARDED_FOR'];
    else $TheIp=$_SERVER['REMOTE_ADDR'];
 
    return trim($TheIp);
}
 

function logThis($sql,$action,$name,$note=null){
	$note = $sql->real_escape_string($note);
	$ip = userIP();
	$sql->query("INSERT INTO logs (action, name, ip, note) VALUES('$action', '$name', '$ip', '$note')");
}

function compareTime($init){
	$now = new DateTime("now");
	$interval = date_diff($init, $now);
	return $interval;
}

// Input: mySQL Timestamp Output: Time since
function differenceTime($timestamp){
	$now = time();
	$timestamp = strtotime($timestamp);
	$diff = $now - $timestamp;
	// unix to legit output 
	if(floor($diff/108000)){ return round($diff/86400, 1) . " Days ago"; }
	elseif(floor($diff/7200)) { return round($diff/3600, 1) . " Hours ago";}
	elseif(floor($diff/120)) return floor($diff/60) . " Minutes ago";
	else return $diff . " Seconds ago";
}

//Validate alphanumeric/underscore Returns a 0 if $str has illegal chars not in the match
function alphanumericUnderscore($str){
	return preg_match('/^[A-Za-z][A-Za-z0-9]*(?:_[A-Za-z0-9]+)*$/',$str);
}

function lastReply($sql,$topicID){
	if(!is_numeric($topicID)) return "Niggaaa";
	$query = $sql->query("SELECT * FROM forumReplies WHERE topic = '$topicID' ORDER BY id DESC");
	$numRows = mysqli_num_rows($query);
	if($numRows <= 0) return "No Replies";
	else{
		if($numRows == 1) $returner = "1 Reply from ";
		else $returner = $numRows . " Replies last by ";
		$row = mysqli_fetch_array($query);
		$returner .= $row['createdby'] . " ";
		$returner .= differenceTime($row['created']);
		return $returner;
	}
}

//Input the category id and output links back to each respective part
function forumTrace($sql,$categoryID){
		if($result = $sql->query("SELECT * FROM forumCategory WHERE id = " . $categoryID)){
			$count = $result->num_rows;
		}else $count = 0;
		if($count != 0){
			$return = "[ <a class='ajax' href='forumFront.php'>Forum</a> ] -> ";
			$category2 = mysqli_fetch_array($sql->query("SELECT * FROM forumCategory WHERE id = ". $categoryID));
			if($category2['class'] == 3){
				$row = mysqli_fetch_array($sql->query("SELECT forumCategory.id, hubs.name AS hubName, forumCategory.name AS forumName FROM forumCategory JOIN hubs ON forumCategory.parentid = hubs.id WHERE forumCategory.id = " . $categoryID));
				$return .= "[ " . $row['hubName'] . " ] -> [ <a class='ajax' href='forumPosts.php?id=" . $row['id'] . "' >" . $row['forumName'] . " </a>]";
				return $return;
			}
			$category1 =mysqli_fetch_array($sql->query("SELECT * from forumCategory WHERE id = ". $category2['parentid'] .""));
			$return .= "[ " . $category1['name'] . " ] -> ";
			$return .= "[ <a class='ajax' href='forumPosts.php?id=" . $category2['id'] . "'>" . $category2['name'] . " </a>]";
			return $return;
		}else{
			return "Bad ID";
		}
}

function linkify($text)
{
    //$text = preg_replace('|([\w\d]*)\s?(https?://([\d\w\.-]+\.[\w\.]{2,6})[^\s\]\[\<\>]*/?)|i', '$1 <a target="_blank" href="$2">$3</a>', $text);
    //return($text);
    $url_pattern = '/# Rev:20100913_0900 github.com\/jmrware\/LinkifyURL
    # Match http & ftp URL that is not already linkified.
      # Alternative 1: URL delimited by (parentheses).
      (\()                     # $1  "(" start delimiter.
      ((?:ht|f)tps?:\/\/[a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]+)  # $2: URL.
      (\))                     # $3: ")" end delimiter.
    | # Alternative 2: URL delimited by [square brackets].
      (\[)                     # $4: "[" start delimiter.
      ((?:ht|f)tps?:\/\/[a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]+)  # $5: URL.
      (\])                     # $6: "]" end delimiter.
    | # Alternative 3: URL delimited by {curly braces}.
      (\{)                     # $7: "{" start delimiter.
      ((?:ht|f)tps?:\/\/[a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]+)  # $8: URL.
      (\})                     # $9: "}" end delimiter.
    | # Alternative 4: URL delimited by <angle brackets>.
      (<|&(?:lt|\#60|\#x3c);)  # $10: "<" start delimiter (or HTML entity).
      ((?:ht|f)tps?:\/\/[a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]+)  # $11: URL.
      (>|&(?:gt|\#62|\#x3e);)  # $12: ">" end delimiter (or HTML entity).
    | # Alternative 5: URL not delimited by (), [], {} or <>.
      (                        # $13: Prefix proving URL not already linked.
        (?: ^                  # Can be a beginning of line or string, or
        | [^=\s\'"\]]          # a non-"=", non-quote, non-"]", followed by
        ) \s*[\'"]?            # optional whitespace and optional quote;
      | [^=\s]\s+              # or... a non-equals sign followed by whitespace.
      )                        # End $13. Non-prelinkified-proof prefix.
      ( \b                     # $14: Other non-delimited URL.
        (?:ht|f)tps?:\/\/      # Required literal http, https, ftp or ftps prefix.
        [a-z0-9\-._~!$\'()*+,;=:\/?#[\]@%]+ # All URI chars except "&" (normal*).
        (?:                    # Either on a "&" or at the end of URI.
          (?!                  # Allow a "&" char only if not start of an...
            &(?:gt|\#0*62|\#x0*3e);                  # HTML ">" entity, or
          | &(?:amp|apos|quot|\#0*3[49]|\#x0*2[27]); # a [&\'"] entity if
            [.!&\',:?;]?        # followed by optional punctuation then
            (?:[^a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]|$)  # a non-URI char or EOS.
          ) &                  # If neg-assertion true, match "&" (special).
          [a-z0-9\-._~!$\'()*+,;=:\/?#[\]@%]* # More non-& URI chars (normal*).
        )*                     # Unroll-the-loop (special normal*)*.
        [a-z0-9\-_~$()*+=\/#[\]@%]  # Last char can\'t be [.!&\',;:?]
      )                        # End $14. Other non-delimited URL.
    /imx';
    $url_replace = '$1$4$7$10$13<a href="$2$5$8$11$14" class="ajax">$2$5$8$11$14</a>$3$6$9$12';
    return preg_replace($url_pattern, $url_replace, $text);
}

function showResponse(){
	if(isset($_SESSION['response']) && $_SESSION['response']){
		echo $_SESSION['response'];
		$_SESSION['response'] = 0;
	}
}

function convertseconds($originalseconds)
	{
		$output = "";
		$days = intval(($originalseconds / (3600 * 24)));
		$hours = intval(( ($originalseconds % (3600 * 24)) / 3600));
		$min = intval(($originalseconds % (3600 * 24) % 3600) / 60);
		$sec = intval(($originalseconds % (3600 * 24) % 3600) % 60);
		$output = "$days" . "d $hours" . "h:$min" . "m:$sec" . "s";
		return $output;
	}
	
function resetPassword($sql,$email){ //Sends out the email with recovery code and returns a form to fill the shit out
	if($email == "") return "Enter an email address";
	$email = $sql->real_escape_string($email);
	$query = "SELECT name FROM users WHERE email = '$email'";
	$username = $sql->query($query);
	if($username->num_rows == 0) return "Invalid Email";
	else {
		$newPass1 = array ("stupid", "silly", "dull", "dumb", "foolish", "futile", "ludicrous",
		"naive", "senseless", "shortsighted", "simple", "dummy", "deficient", "mindless", "idiotic");
		$newPass2 = array ("User", "Member", "Person", "Human", "Brain", "Somebody", "Dodo", "Dingo");
		$newPass = $newPass1[array_rand($newPass1)] . $newPass2[array_rand($newPass2)] . rand(1,90000); 
		$headers = "From: <no-reply@op2rules.net>\r\n";
		$headers .= "MIME-Version: 1.0\r\n"; 
		$headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 
		mail($email, "Reset Password on op2rules.net", "Your recovery code is: $newPass",$headers);
		$usernameString = $username->fetch_row();
		$usernameString = $usernameString[0];
		$sql->query("DELETE FROM user_tempData WHERE name = '$usernameString'"); // Clear any old tempdata
		$query = "INSERT INTO user_tempData (name, temp) VALUES ('$usernameString', '$newPass')";
		$sql->query($query);
		logThis($sql, "Password Recovery",$usernameString);
	}
	return <<<HTML
Check your spam folder
<script>
$(document).ready(function(){
  $("#submit2").click(function(e){
      e.preventDefault();
    $.ajax({type: "POST",
            url: "/userPasswordReset.php",
            data: { recovery: $("#recovery").val(), password: $("#password").val(), username: $("#username").val()},
            success:function(result){
      $("#response").html(result);
    }});
  });
});
</script>
      <form method="post" action="userPassword.php">
        <input type="text" style='width: 200px;' name="recovery" autofocus id="recovery" value="" placeholder="Recovery Code">
		<input type="text" name="username" id="username" value="" placeholder="Username">
		<input type="text" style='width: 400px;' name="password" id="password" value="" placeholder="New Password">
        <p><input type="submit" id="submit2" name="commit" value="Update Password"></p>
      </form>
	  <div id="response"></div>
HTML;
}

function resetPassword2($sql,$username, $password, $recovery){ // Takes input from the previous form and works the magic
	global $salt;
	$username = $sql->real_escape_string($username);
	$recovery = $sql->real_escape_string($recovery);
	if($password == "") return "Enter a password";
	$password = $sql->real_escape_string(sha1($password . $salt));
	if($recovery == "") return "Enter a recovery code";
	if($username == "") return "Enter a username";
	$query = "SELECT * FROM user_tempData WHERE name = '$username' AND temp = '$recovery'";
	$tempData = $sql->query($query);
	if($tempData->num_rows){
		$sql->query("DELETE FROM user_tempData WHERE name = '$username'");
		$sql->query("UPDATE users SET pass = '$password' WHERE name = '$username'");
		logThis($sql,"Password Change", $username);
		return "You may now login with that new password. This recovery code will only work once";
	}	
	else return "No match found for given recovery and username combination";
}

// Prints the whole $_POST variable... useful when needed
//Needs to be inserted on the page right then and there so a function call won't work since $_POST is different elsewhere
/*
foreach ($_POST as $key => $value) {
	echo '$_POST' . "['" . $key . "'] -> $value<br>";
	}
}
*/

function userNotifications($sql, $username){
	$username = $sql->real_escape_string($username);
	$new = $sql->query("SELECT * FROM notes WHERE createdfor = '$username' AND seen = 0");
	$newCount = mysqli_num_rows($new);
	return $newCount;
}

// Returns an array of logs from the database or 0 if something went wrong
function getIRCLogs($sql, $chan, $numLogs = 5000){
	if($query = $sql->query("SELECT * FROM ircLogs WHERE chan = '$chan' ORDER BY id DESC LIMIT $numLogs")){
		$i = mysqli_num_rows($query);
		if($i == 0) return 0;
		$logs = array();
		for($i;$log = $query->fetch_array(MYSQLI_ASSOC); $i--){
				$logs[$i] = $log;
		}
		return $logs;
	}
	return 0;
}
?>