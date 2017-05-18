<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '../../randomFunctions.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../mysqlConnect.php');
//require_once($_SERVER['DOCUMENT_ROOT'] . '../../settings.php');
//require_once($_SERVER['DOCUMENT_ROOT'] . '/userSession.php');
error_reporting(0);
$title = imagecreatefrompng("./title.png"); 
//$font = "./visitor2.ttf";
$font = "./Tahoma.ttf";

//use array_reverse() on the array when outputting as a list
$logs = getIRCLogs($mysqli, "#op2rules", 11);
$string = "";
// Should be able to display 11 lines of irc...

$max = 0;
for($i=11;$i>=$max;$i--){
		if(strlen($logs[$i]['message']) > 30){
		$start = substr($logs[$i]['message'],0,30);
		$cutoff = substr($logs[$i]['message'], 30);
		$cutoff = preg_replace("/\s/","\n               ", $cutoff, 1);
		if(strlen($cutoff)>50){
			$cutoff = substr($cutoff,0,50) . "...";
		}
		$logs[$i]['message'] = $start . $cutoff;
		$max++;
	}
}

for($i=0; $i<$max; $i++){
	unset($logs[$i]);
}

while($log = array_pop($logs)){
	$string .= $log['user'] . "> " . $log['message'] . "\n";
}

/*foreach($logs as $log){
	if(strlen($log['message']) > 23){
		$start = substr($log['message'],0,23);
		$cutoff = substr($log['message'], 23);
		$cutoff = preg_replace("/\s/","\n               ", $cutoff, 1);
		if(strlen($cutoff)>40){
			$cutoff = substr($cutoff,0,40) . "...";
		}
		$log['message'] = $start . $cutoff;
	}
	$time =  "[" . substr($log['time'],14,2) . "] ";
	$string .= $log['user'] . "> " . $log['message'] . "\n";
	//$string .= $log['user'] . "> " . $log['message'] . "       ";
}*/

/* Stuff for making it bg color
if($_SESSION['theme'] < 50){
	$color1 = $backgroundcolor[$_SESSION['theme']];
	$color2 = $popoutcolor[$_SESSION['theme']];
}

$r =  (int) hexdec(substr($color1,1,2));
$b = (int) hexdec(substr($color1,3,2));
$g = (int) hexdec(substr($color1,5,2));

*/
$white = imagecolorallocatealpha($title, 255,255,255,70);

imagettftext($title, 5, 0, 515, 7, $white, $font, $string);

imagealphablending($title, true);
imagesavealpha($title, true);

//header("Content-type: image/png");  
imagepng($title,null,9,PNG_ALL_FILTERS);
imagedestroy($title);

?>