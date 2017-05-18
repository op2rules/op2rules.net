<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '../../mysqlConnect.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../randomFunctions.php');
echo "this is test php file<br>";

?>
<style type="text/css">
#navlink{
	background: url(styling/navdivider.png) left no-repeat, url(styling/navbg.png) repeat;
	height: 33px;
	display: inline-table;
	padding-left: 15px;
	padding-right: 15px;
	padding-top: 16px;
	margin-right: -11px;
	font-family: Tahoma;
}
#navlink:hover{
	background: url(styling/navdivider.png) left no-repeat, url(styling/navbghover.png) repeat;
}
#navlink:active{
		background: url(styling/navdivider.png) left repeat, url(styling/navbg.png) repeat;
}
</style>
<style>
a{ text-decoration: none }
a:link{ color: <?php echo $color2; // settings.php?> } 
a:visited{ color: <?php echo $color2; ?> }
A:hover {   text-decoration: none; color: #ffffff}
body{
	color:white;
	background-color: <?php echo $color1; ?>; 
	font-family: Courier;
}
#navlinks{
	font-size: 10px;
	font-family: Tahoma;
}
#footer{
	font-size: 11px;
	font-family:sans-serif;
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

table{
	border: 1px solid white;
	margin-bottom: 4px;
	margin-top: 3px;
}
#container{
	width: 71%;
}

</style>

<div id="navlink">
	  [ <a href="http://op2rules.net"> Home </a> ]  
</div>
<div id="navlink">
 [ <a class ="ajax" href='apps.php'>Apps</a> ] 
 </div>
<div id="navlink">
		   <a class="ajax" href="/files.php"> Files </a> 
</div>
<div id="navlink">
		   <a class="ajax" href="/stats.php"> Stats </a>
</div>
<div id="navlink">
		  [ <a class="ajax" href="forumFront.php"> Forum </a>]
		  </div>
		  
		  <br>
		  


<div valign="top" id="navlinks" charset="UTF-8"><center>
          <hr Color=#FFFFFF width=72% align=center size=2>
          [\ <a href="http://op2rules.net">  Home  </a> /]
		   *-.__/ <a class ="ajax" href='apps.php'>Apps</a> \___.-*.
		  (x] <a class="ajax" href="/files.php"> Files </a>[x)\-._
		  v^ <a class="ajax" href="/stats.php"> Stats </a> ^v.__
		  _._:_|<a class="ajax" href="forumFront.php"> Forum </a> |_:_._
		  <hr color=#ffffff width=75% alight=center size=1>
</center></div>
     
<?php

echo $_SERVER['PATH_INFO'];
echo $_SERVER['REMOTE_USER'];
