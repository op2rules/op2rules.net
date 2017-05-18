<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/userSession.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../mysqlConnect.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../randomFunctions.php');

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){}else{
	require_once("head.php");
}

if($_SESSION['logged']){
?>
<pre style="font: 8px/3px monospace;">                             ,@;                         
                             + @                         
                            ,..@                         
                            , @'                         
                            +##                          
                           ``@                           
                           +:#                           
                          ::#`                           
                          ',+                            
                         ;:#                             
                      .  ;;'                             
                     ,@@@:@                              
                    `@@#:'@@@:                           
                    @# +;@ +@@@@+.                       
                   @# `.#@+. @@ .+@@'`                   
                  @' '@'@      @:   .#@@:`               
                 #:  #.@:       ,@      ,#@#,            
              ` +    @+@`+        @:        :#@+`        
               :    ,.#@@@         :#          `'@@;     
              `@ +@@@+@@@#          .@,            .#@#` 
              @# ;@@#+###`            @@#+#######@@@@@@@@
             @## @'                     .,,..``          
            @,#'`@    `                                  
           @, #, @`  @` ,:.                              
          @.  @  #+ '@@@@@@@                             
         @`   @   : @@@@@@@@@                            
        @     @  :'@#@@@@@@@@@                           
       @`     ##' @:.@ @@#@@@@@                          
    ``@       .'+@,    @ .@#@#@@:     .;@#.              
    `@          @+,    # ;; `#@@@@@@@@@@'.               
   .@`         `@ ,;: `  @     `'#@@+`'@@@@@@'.          
  ,@           :   ;'@@.`+                ;@@@@@#@;.     
 ,@                    .,                     :#@@@@@@@` 
,@                                              ` ;@@@@  
`:'+#@@@@@@@+':`                                  #+  .  
          ``:+#@@@@#;`                            @`     
                  `.+@@@+`                       +@      
                       `'@@@;                    @`      
                           .+@@#.  :#,          :@       
                               ;@@@: `'@'       @.       
                                  ,#@@'  '@'`  '@        
                                     `+@@+``;:.@.        
                                         '@@+'@#         
                                            ;@;          
</pre><pre>
[ <a class='ajax' id='one' href='appNotes.php?id=mynotes'>My Notes </a>] [ <a class='ajax' href='appNotes.php?id=newnote'>New Note </a>]
<?php 
// View your notes, using GET
if(isset($_GET['id']) && $_GET['id'] == "mynotes"){
	echo "Your Notes<br><table><tr><td valign=top>";
	echo "Notes By Me<br>";
	$_GET['id'] = $mysqli->real_escape_string($_GET['id']);
	$query = $mysqli->query("SELECT * FROM notes WHERE createdby = '" . $_SESSION['name'] . "' ORDER BY created DESC LIMIT 0, 30");
	while($row = mysqli_fetch_array($query)){
		echo "[<a class='ajax' href='appNotes.php?note=" . $row['id'] . "'> " . $row['name'] . " </a>]<br>";
	}
		echo "</td><td valign=top>Notes For Me<br>";
	$query = $mysqli->query("SELECT * FROM notes WHERE createdfor = '" . $_SESSION['name'] . "' ORDER BY created DESC LIMIT 0, 30");
	while($row = mysqli_fetch_array($query)){
		echo "[<a class='ajax' href='appNotes.php?note=" . $row['id'] . "'> " . $row['name'] . " </a>] from " . $row['createdby'] . "<br>";
	}
		echo "</td></tr></table>";
}

// Write a new note, form pulled up using GET
if(isset($_GET['id']) && $_GET['id'] == "newnote"){
	echo "New Note<br>";
?>
<form method="post" action="appNotes.php">
<input type="text" id="name" name="name" placeholder="Title"></input><br>
<textarea style='width: 580px;' id="note" name="note" placeholder="Your Note"></textarea>
<input type="text" name="to" value="<?php if(isset($_GET['share'])) echo $_GET['share'];?>" placeholder="Share with?"></input>(Blank for self) *Shared notes can be deleted by either member
<input type="submit" class='ajaxForm' id="submit" name="commit" value="Write">
</form> 
<?php
}

// Writing note to SQL
if(isset($_POST['note']) && $_POST['note'] != ""){
	// Parse
	$note = $mysqli->real_escape_string(htmlspecialchars($_POST['note'])); 
	$name = $mysqli->real_escape_string(nl2br(htmlspecialchars($_POST['name']))); 
	$seen = 1;
	if(!isset($_POST['to'])) $_POST['to'] = "";
	if(isset($_POST['to']) && $_POST['to'] != "") $seen = 0;
	$to = $mysqli->real_escape_string(nl2br(htmlspecialchars($_POST['to']))); 
	
	$query = $mysqli->query("INSERT INTO notes (name, note, createdby, createdfor, seen) VALUES ('" . $name . "', '" . $note . "' , '" .  $_SESSION['name'] . "' , '" . $to . "', $seen)") or die(mysqli_error());
	logThis($mysqli, "New Note",$_SESSION['name'],$_POST['name']); 
	echo "Your note has been saved";
}

// Displaying notes
if(isset($_GET['note'])){
	$_GET['note'] = $mysqli->real_escape_string($_GET['note']);
	$query = $mysqli->query("SELECT * FROM notes WHERE id = '" . $_GET['note'] . "'");
	$row = mysqli_fetch_array($query);
	if(!$row) { echo "Note wasn't found in database"; }
	if($row['createdby'] != $_SESSION['name'] && $row['createdfor'] != $_SESSION['name']) { echo "You do not have access to this note"; }
	else{
		echo "<form method='post' action='appNotes.php?save=" . $_GET['note'] . "'><textarea name='noteChanged' style='width: 580px;'>" . $row['note'] . "</textarea><br><input type='submit' class='ajaxForm' value='Save Changes'></form>";
		echo "Created " . differenceTime($row['created']);
		if($row['updated'] != null) echo "<br>Updated " . differenceTime($row['updated']);
		echo "<br>[ <a class='ajax' id='del' href='appNotes.php?del=" . $_GET['note'] . "'>Delete Note </a>]";
		if($row['seen'] == 0 && $row['createdfor'] == null or $row['createdfor'] == $_SESSION['name']){
			$mysqli->query("UPDATE notes SET seen=1 WHERE id = ". $_GET['note']);
			?> <script>$("#notificationCount").html(function(i,val) { if(parseInt(val) > 1){ val = parseInt(val); val -= 1; return " " + val + " "}else{ $("#notificationLink").remove()}});</script><?php //This will either decrement the notification box by one, or remove it entirely
		}
	}
}

// Updating notes
if(isset($_GET['save'])){
	$noteNum = $mysqli->real_escape_string($_GET['save']);
	$note = $mysqli->real_escape_string($_POST['noteChanged']);
	$query = $mysqli->query("SELECT * FROM notes WHERE id = $noteNum");
	$row = mysqli_fetch_array($query);
	if(!$row) echo "Note wasn't found in database";
	if($row['createdby'] != $_SESSION['name']) echo "You can only edit notes you've created";
	else{
		$query = $mysqli->query("UPDATE notes SET note='$note', updated=CURRENT_TIMESTAMP WHERE id=$noteNum");
		// Eventually convert this all to OOP and allow for some error checking
		echo "Your note has been saved";
	}
}

// Deleting notes
if(isset($_GET['del'])){
	$_GET['del'] = $mysqli->real_escape_string($_GET['del']);
	$query = $mysqli->query("SELECT * FROM notes WHERE id = '" . $_GET['del'] . "'");
	$row = mysqli_fetch_array($query);
	if(!$row) { echo "Note wasn't found in database"; }
	if($row['createdby'] != $_SESSION['name'] && $row['createdfor'] != $_SESSION['name']) { echo "You do not have access to this note"; }
	else{
		$query = $mysqli->query("DELETE FROM notes WHERE id = '" .  $_GET['del'] . "'");
		echo "Your note vanishes into the void";
	}
}

// jQuery plugin to autosize the textarea
?> </pre>
<script type="text/javascript">
$("#one").focus();
/*!
	Autosize 1.18.17
	license: MIT
	http://www.jacklmoore.com/autosize
*/
!function(e){var t,o={className:"autosizejs",id:"autosizejs",append:"\n",callback:!1,resizeDelay:10,placeholder:!0},i='<textarea tabindex="-1" style="position:absolute; top:-999px; left:0; right:auto; bottom:auto; border:0; padding: 0; -moz-box-sizing:content-box; -webkit-box-sizing:content-box; box-sizing:content-box; word-wrap:break-word; height:0 !important; min-height:0 !important; overflow:hidden; transition:none; -webkit-transition:none; -moz-transition:none;"/>',a=["fontFamily","fontSize","fontWeight","fontStyle","letterSpacing","textTransform","wordSpacing","textIndent","whiteSpace"],n=e(i).data("autosize",!0)[0];n.style.lineHeight="99px","99px"===e(n).css("lineHeight")&&a.push("lineHeight"),n.style.lineHeight="",e.fn.autosize=function(i){return this.length?(i=e.extend({},o,i||{}),n.parentNode!==document.body&&e(document.body).append(n),this.each(function(){function o(){var t,o=window.getComputedStyle?window.getComputedStyle(u,null):null;o?(t=parseFloat(o.width),("border-box"===o.boxSizing||"border-box"===o.webkitBoxSizing||"border-box"===o.mozBoxSizing)&&e.each(["paddingLeft","paddingRight","borderLeftWidth","borderRightWidth"],function(e,i){t-=parseFloat(o[i])})):t=p.width(),n.style.width=Math.max(t,0)+"px"}function s(){var s={};if(t=u,n.className=i.className,n.id=i.id,d=parseFloat(p.css("maxHeight")),e.each(a,function(e,t){s[t]=p.css(t)}),e(n).css(s).attr("wrap",p.attr("wrap")),o(),window.chrome){var r=u.style.width;u.style.width="0px";{u.offsetWidth}u.style.width=r}}function r(){var e,a;t!==u?s():o(),n.value=!u.value&&i.placeholder?p.attr("placeholder")||"":u.value,n.value+=i.append||"",n.style.overflowY=u.style.overflowY,a=parseFloat(u.style.height)||0,n.scrollTop=0,n.scrollTop=9e4,e=n.scrollTop,d&&e>d?(u.style.overflowY="scroll",e=d):(u.style.overflowY="hidden",c>e&&(e=c)),e+=z,Math.abs(a-e)>.01&&(u.style.height=e+"px",n.className=n.className,w&&i.callback.call(u,u),p.trigger("autosize.resized"))}function l(){clearTimeout(h),h=setTimeout(function(){var e=p.width();e!==b&&(b=e,r())},parseInt(i.resizeDelay,10))}var d,c,h,u=this,p=e(u),z=0,w=e.isFunction(i.callback),f={height:u.style.height,overflow:u.style.overflow,overflowY:u.style.overflowY,wordWrap:u.style.wordWrap,resize:u.style.resize},b=p.width(),g=p.css("resize");p.data("autosize")||(p.data("autosize",!0),("border-box"===p.css("box-sizing")||"border-box"===p.css("-moz-box-sizing")||"border-box"===p.css("-webkit-box-sizing"))&&(z=p.outerHeight()-p.height()),c=Math.max(parseFloat(p.css("minHeight"))-z||0,p.height()),p.css({overflow:"hidden",overflowY:"hidden",wordWrap:"break-word"}),"vertical"===g?p.css("resize","none"):"both"===g&&p.css("resize","horizontal"),"onpropertychange"in u?"oninput"in u?p.on("input.autosize keyup.autosize",r):p.on("propertychange.autosize",function(){"value"===event.propertyName&&r()}):p.on("input.autosize",r),i.resizeDelay!==!1&&e(window).on("resize.autosize",l),p.on("autosize.resize",r),p.on("autosize.resizeIncludeStyle",function(){t=null,r()}),p.on("autosize.destroy",function(){t=null,clearTimeout(h),e(window).off("resize",l),p.off("autosize").off(".autosize").css(f).removeData("autosize")}),r())})):this}}(jQuery||$);
$('textarea').autosize();    
</script>
 <?php
} // if($_SESSION['logged']){
else echo "Login to use Notes";

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){}else{
	require_once("foot.php");
}