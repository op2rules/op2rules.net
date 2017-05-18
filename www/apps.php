<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/userSession.php');

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){}else{
	require_once("head.php");
}
if($_SESSION['logged']){
?>
<div class="icon"><img src="styling/URL.png"><br>
[<a class="ajax" id="one" href='appURL.php'> URL </a>]</div>
<div class="icon"><img src="styling/webIRC.png"><br>
[<a class="ajax" href='appWebirc.php'> webIRC </a>]</div>
<div class="icon"><img src="styling/Notes.png"><br>
[<a class="ajax" href='appNotes.php'> Notes </a>]</div>
<div class="icon"><img src="styling/Images.png"><br>
[<a class="ajax" href='appImage.php'> Image Host </a>]</div>
<div class="icon"><img src="styling/fileHost.png"><br>
[<a class="ajax" href='appFile.php'> File Host </a>]</div>
<div class="iconWide"><img src="styling/DMS.png"><br>
[<a class="ajax" href='DMS.php'> Median System </a>]</div>
<div class="icon"><img src="styling/users.png"><br>
[<a class="ajax" href='appUsers.php'> Users </a>]</div>
<div class="icon"><img src="styling/stats.png"><br>
[<a class="ajax" href='stats.php'> Stats </a>]</div><!--was tired of how it skipped a line...-->
<script type="text/javascript">
$("#one").focus();
</script>
<br><br>
<? 
}
else echo "NO APPS FOR YOU";
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){}else{
	require_once("foot.php");
}