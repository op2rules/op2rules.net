<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '../../mysqlConnect.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../settings.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../randomFunctions.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/userSession.php');

/* Settings like these need to be set.  The configuration option will keep this new value during the script's execution, and will be restored at the script's ending. 
	... Unfortunately this has been changed and need to use .user.ini instead since mode is PHP_INI_PERDIR
	ini_set('upload_max_filesize', '20M');
	ini_set('post_max_size', '20M');
	ini_set('max_input_time', 300);
	ini_set('max_execution_time', 300);
*/

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){}else{
	require_once("head.php");
}

if ($_SESSION['logged'])
{
?>
<script type="text/javascript">// Including this on the page fixes the bug where an ajax loaded version of appFile.php does not have ajax enabled o.0
$(document).ready(function(){
	$(".ajaxForm").click(function(e){
		e.preventDefault();
		$.ajax({type: "POST",
            url: $(this).parent().attr('action'),
            data: $(this).parent().serialize(),
            success:function(result){
				$("#container").html(result)
			}
		});
  });
});
</script>
<pre>
File Host
<form target="_self" method="post" name="form" action="appFile.php" enctype="multipart/form-data">
<input type="file" name="file[]" title="lol" multiple="multiple"/> <input type="submit" name="submit" value="Upload" />
<input type="hidden" name="MAX_FILE_SIZE" value="16777215" /> </form>
<?php
if($_SESSION['status'] = 1) $userLimit = 104857600; // 100 MB
if($_SESSION['status'] = 255) $userLimit = 209715200; // 200 MB
	
// Delete a File (must be done before total volume is calculated)
if(isset($_POST['fileDel'])){
	$fileDel = $mysqli->real_escape_string($_POST['fileDel']);
	$mysqli->query("DELETE FROM userFiles WHERE uploader = '" . $_SESSION['id'] . "' AND id = $fileDel");
	echo "The file rapidly dissipates into particles of unobservable size<br><br>";
}
	
// Calculate total upload volume
$totalSize = 0;
$sizes = $mysqli->query("SELECT size FROM userFiles WHERE uploader = '" . $_SESSION['id'] . "'");
if(mysqli_num_rows($sizes)){ // User has uploaded files
	foreach($sizes as $item){
		$totalSize += $item['size'];
	}
}

function reArrayFiles(&$file_post) { // http://php.net/manual/en/features.file-upload.multiple.php
	$file_ary = array();
	$file_count = count($file_post['name']);
	$file_keys = array_keys($file_post);

	for ($i=0; $i<$file_count; $i++) {
		foreach ($file_keys as $key) {
			$file_ary[$i][$key] = $file_post[$key][$i];
		}
	}
	return $file_ary;
	}
	
function validateUpload($test){
	if ($test == UPLOAD_ERR_OK) return 1337;
	switch ($test){		
		case UPLOAD_ERR_PARTIAL:
			$message = 'File was only partially uploaded :o';
			break;
		
		case UPLOAD_ERR_NO_FILE:
			$message = 'Nothing was uploaded 0.o';
			break;
		
		case UPLOAD_ERR_NO_TMP_DIR:
			$message = 'Could not find temporary upload folder :P';
			break;
		
		case UPLOAD_ERR_CANT_WRITE:
			$message = 'Could not write file :|';
			break;
			
		default:
			$message = 'Unknown error, please notify an admin about this, which you can do on Webirc :)';
	}
	return $message . "\n";
}

// Uploaded file
if(isset($_FILES['file'])){
	$files = reArrayFiles($_FILES['file']);
	for($i=0;isset($files[$i]);$i++){ // Validate each upload
		$validating = validateUpload($files[$i]['error']);
		if($validating != 1337){
			echo $validating;
			require_once("foot.php");
			exit();
		}
	}
	
	// Sanitize, check size, and upload
	foreach($files as $file){
		$name = $file['name'];
		$size = $file['size'];
		$type = $file['type'];
		$uploader = $_SESSION['id'];
		$tmpName = $file['tmp_name'];
		$fp = fopen($tmpName, 'r');
		$content = fread($fp, $size);
		
		if($size > 16777215){// 16777215 
			$sizeMB = number_format($size / 1048576,2);
			echo "$name is over 16 MB (it's $sizeMB MB) and was not uploaded";
			continue;
		}
		
		$totalSize += $size;
		if($size > $userLimit){
			echo "Your online storage with op2rules.net has been filled up";
			require_once("foot.php");
			exit();
		}
		
		$name = $mysqli->real_escape_string($name);
		$size = $mysqli->real_escape_string($size);
		$type = $mysqli->real_escape_string($type);
		$content = $mysqli->real_escape_string($content);
		
		$fileQuery = $mysqli->query("INSERT INTO userFiles (name, size, type, uploader, content) VALUES ('$name', '$size', '$type', '$uploader', '$content')");
		
		logThis($mysqli,"File",$_SESSION['name'],$name . " @ " . $size); 
		echo $name . " was uploaded!<br>";
	}
}

// List user's files
$userData = $mysqli->query("SELECT * FROM userFiles WHERE uploader = '" . $_SESSION['id'] ."' ORDER BY created DESC");
if(mysqli_num_rows($userData)){
	echo "Your Files";
	echo "<table style='table-layout: fixed;'>
		<tr>
			<td width='80'></td>
			<td class='centerText'>Name</td>
			<td class='centerText'>Type</td>
			<td width='70' class='centerText'>Size</td>
			<td width='80' class='centerText'>Created On</td>
			<td width='70'></td>
		</tr>";
	while($uploadedFile = mysqli_fetch_array($userData)){
		if($uploadedFile['size'] > 1048576) $uploadedFileSize = number_format($uploadedFile['size']/1048576, 2) . " MB";
		elseif($uploadedFile['size'] > 1024) $uploadedFileSize = number_format($uploadedFile['size']/1024, 2) . " KB";
		else $uploadedFileSize = $uploadedFile['size'] . " Bytes";
		
		echo "<tr class='removable'>
					<td class='centerText'><form action='appFileDownload.php' method='post'><input type='hidden' name='id' value='".$uploadedFile['id']."'><input type='submit' value='Download'></form></td>
					<td class='centerText'>" . $uploadedFile['name'] . "</td>
					<td class='centerText'><font size='1'>" . $uploadedFile['type'] . "</font></td>
					<td class='centerText'>" . $uploadedFileSize . "</td>
					<td class='centerText'>" . substr($uploadedFile['created'], 0, 10) . "</td>
					<td class='centerText'><form action='appFile.php' method='post'><input type='hidden' name='fileDel' value='".$uploadedFile['id']."'><input type='submit' class='ajaxForm' value='Delete'></form></td>
				</tr>";
	}
	echo "</table>";
}

$fractionFull = $totalSize/$userLimit;
$percentFull = number_format($fractionFull * 100,1);
?>
<style>
.meter { 
	height: 15px;  
	position: relative;
	background: #555;
	width: 75%;
}
.meter > span {
  display: block;
  height: 100%;
	margin-top: -15px;
	margin-right: 100%;
  background-color: <?php if($_SESSION['theme'] < 50){
	$color2 = $popoutcolor[$_SESSION['theme']];
} echo $color2; ?>;

  position: relative;
  overflow: hidden;
}
</style>
Limits
16 MB / File
<div class="meter"><?php echo $percentFull . "%";?>
  <span style="width: <?php echo $percentFull; ?>%"></span>
</div></pre>
<?php
}
else
{
?>
You need to be logged in to store files.
<?php
}
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){}else{
	require_once("foot.php");
}
?>