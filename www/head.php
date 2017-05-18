<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/userSession.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../settings.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../randomFunctions.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../mysqlConnect.php');

if($_SESSION['theme'] < 50){
	$color1 = $backgroundcolor[$_SESSION['theme']];
	$color2 = $popoutcolor[$_SESSION['theme']];
}
?>
<!DOCTYPE html>
<head>
<meta charset="UTF-8">
<meta name="description" content="Presenting you finest the internet has to offer in a NO-BS zone" />
<style>
a{ text-decoration: none }
a:link{ color: <?php echo $color2; // settings.php?> } 
a:visited{ color: <?php echo $color2; ?> }
A:hover {   text-decoration: none; color: #ffffff}
.Stil1 {font-size: Kein}
.Stil2 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 14px;
	font-weight: bold;
}
body{
	color:white;
	background-color: <?php echo $color1; ?>; 
	font-family: Courier;
	margin-top:0px;
}
.centerText{
	text-align: center;
}
#navlinks{
	font-size: 10px;
	font-family: Tahoma;
	margin-top: -27px;
}
#footer{
	font-size: 10px;
	font-family:sans-serif;
}
#footerText{
	z-index: 10;
	position: absolute;
	margin-left: auto;
	margin-right: auto;
	margin-top: -29px;
	left: 0;
	right: 0;
}
textarea{
	background-color: <?php echo $color1; ?>;
	font-family: Courier;
	border: 1px solid white;
	color: white;
}
input{
	background-color: <?php echo $color1; ?>;
	border: 1px solid <?php echo $color2; ?>;
	font-family: Courier;
	color: white;
	text-align:center;
}
input:focus{
	border: 1px solid white;
}
button{
	background-color: <?php echo $color1; ?>;
	border: 1px solid <?php echo $color2; ?>;
	font-family: Courier;
	color: white;
	text-align:center;
}

table{
	border: 1px solid white;
	margin-bottom: 4px;
	margin-top: 3px;
}
pre{
	white-space: pre-wrap;       /* css-3 */
	white-space: -moz-pre-wrap;  /* Mozilla, since 1999 */
	white-space: -pre-wrap;      /* Opera 4-6 */
	white-space: -o-pre-wrap;    /* Opera 7 */
	word-wrap: break-word;
}
#container{
	width: 71%;
	word-wrap: break-word;
}
.icon{
  font-family: monospace;
  display: inline-block;
  width: 110px;
  height: 70px;
  font-size: 12px;
}
.iconWide{
  font-family: monospace;
  display: inline-block;
  width: 150px;
  height: 70px;
  font-size: 12px;
}
</style>
<script src="jquery.js"></script>
<script type="text/javascript">
// ajax class binds to all hyperlinks within the website
$(document).ready(function(){
    $(document).on("click",".ajax", function(){
		var href2 = $(this).text(); // Very clean browser URL but no way to .load(location.href)
		var href = $(this).attr("href");
		history.pushState({}, '',href);
		//$("#container").html("<img src='styling/ajax-loader.gif'>");
		$("#footer").hide(); // this was done after the pushstate and later in the function because when made earlier there was some problems with the footer not showing up correctly in internet explorer
		$("#container").hide().load($(this).attr("href"),{},function(){$(this).hide().fadeIn("fast"); $("#footer").fadeIn("fast");});
		return false;
	});
});

// User presses the back button (or forward button)
$(window).on("popstate", function(e) {
  if (e.originalEvent.state !== null) {
    $("#container").load(location.href).hide().fadeIn('slow');
  }
});

//Generic ajax for form submission. Most files can be converted from an internal jquery code to just using the 'ajaxForm' class on the submit button. This also is good because it ensures that the target is set properly on the html so if the user has JS disabled the standard synchronous code still functions. More stability. Nice. 

//The limitation with using this quick class is that the result is always written to #container. Although this is useful most of the time, it may be handy to have certain things only ajax into an alternative div. 
// There is also a strange known bug here where you pull a page up with ajax, and then .ajaxForm no longer works on appFile.php and forumPostAdd(possibly others). The solution is to include the javascript right on that page
$(document).ready(function(){
	$(".ajaxForm").click(function(e){
		e.preventDefault();
		$.ajax({type: "POST",
            url: $(this).parent().attr('action'),
            data: $(this).parent().serialize(),
            success:function(result){
				$("#footer").hide();
				$("#container").html(result).hide().fadeIn("fast");
				$("#footer").fadeIn("fast");
			}
		});
  });
});
</script>
</head>
<body><center>
<img src='styling/title.php'>
<div valign="top" id="navlinks"><center>
          [\ <a href="http://op2rules.net"> Home </a> /]  
		  [ <a class ="ajax" href='apps.php'>Apps</a> ] 
		  [ <a class="ajax" href="/files.php"> Files </a> ] 
		  [ <a class="ajax" href="appHub.php"> Hub </a> ]
		  [ <a class="ajax" href="forumFront.php"> Forum </a>]
<?php if($_SESSION['logged']) { ?> 
		  [ <a class="ajax" href="userSettings.php"> Settings </a>]
		  [ <a href='userLogout.php'>Logout</a> ] 
<? $newItems = userNotifications($mysqli,$_SESSION['name']); 
	if($newItems > 0){
		echo "<span id='notificationLink'>[<a class='ajax' id='notificationCount' href='/userNotifications.php'> $newItems </a>] </span>"; }  
	}?>
<?php if(!$_SESSION['logged']){ ?>   
          [ <a class="ajax" href="/userLogin.php"> Login </a> ] 
		  [ <a class="ajax" href="userRegister.php"> Register </a> ]
<?php }  echo $_SESSION['name']; ?>
		  <!--<hr color=#ffffff width=52% alight=center size=1>-->
</center></div><br>
<div id="container" >