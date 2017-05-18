<?php 
require_once($_SERVER['DOCUMENT_ROOT'] . '../../mysqlConnect.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../settings.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../randomFunctions.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/userSession.php');

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){}else{
	require_once("head.php");
}
if($_SESSION['logged'] && !empty($_GET['id']) && is_numeric($_GET['id'])){
	$_GET['id'] = $mysqli->real_escape_string($_GET['id']);
	$result = $mysqli->query("SELECT * FROM forumTopics WHERE id = " . $_GET['id']);
	$count = $result->num_rows;
	if($count == 0) {
		echo "Bad reference";
	}else{
		$row = mysqli_fetch_array($result);
		if($row['hub'] != NULL){
			if(isset($_SESSION['hub'])){
				if(!in_array($row['hub'], $_SESSION['hub'])){
					echo "Login to this hub";
					if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){}else{
						require_once("foot.php");
					}
					exit();
				}
			}else{
				echo "Login to this hub";
				if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){}else{
					require_once("foot.php");
				}
				exit();
			}
		}
		if($row['status'] <= $_SESSION['status']){
			$created = differenceTime($row['created']);
			echo "<pre>" . forumTrace($mysqli,$row['category']) . " -> [ <a class='ajax' id='fuckme' href='forumView.php?id=" . $_GET['id'] . "'>" . $row['name'] . " </a>] $created </pre>"; 
			// Linkify
			$row['content'] = linkify($row['content']);
			echo $row['content'] . "<br>";
			echo "-" . $row['createdby'] . "<br>";
			if($_SESSION['name'] == $row['createdby'] or $_SESSION['status'] > 99){ // Did the viewer create the post? Add a delete button to remove it
				echo "[ <a class ='ajax' id='del' href='forumPosts.php?del=" . $_GET['id'] . "'>Delete Post </a>]";
			}
			?>
			<form method="post" action="forumReply.php">
	<textarea rows="4" cols="40" name="body" id="body" value="<?php if(isset($_POST['body'])) echo $_POST['body']; ?>" placeholder="Write a response"></textarea>
	<input type="hidden" name="id" id="id" value="<?php echo $_GET['id']; ?>"><br>
    <input type="submit" id="submitReply" name="commit" value="Reply/Refresh">
</form><?php
			showResponse();
			if(!isset($_GET['pg'])) $pg = 0;
			else { $pg = $_GET['pg']; }
			$pg2 = $pg + 10;
			$query2 = $mysqli->query("SELECT * FROM forumReplies WHERE topic = ". $_GET['id'] . " ORDER BY id DESC LIMIT 10 OFFSET $pg");
			while ($row2 = mysqli_fetch_array($query2)) {
				$row2['content'] = linkify($row2['content']);
				$created2 = differenceTime($row2['created']);
				echo "<table width='650px' style='table-layout: fixed;'><tr><td width='190px' class='centerText'>[ " . $row2['createdby'] . " ]<br>$created2</td><td width='500px'>" . $row2['content'] . "</td></tr></table>"; 
			}
			if($pg != 0) { 
				$pgBack = $pg - 10;
				echo "[ <a class='ajax' id='fuckme' href='forumView.php?id=" . $_GET['id'] . "&pg=$pgBack'> < </a>] ";
			}
			if(mysqli_num_rows($mysqli->query("SELECT * FROM forumReplies WHERE topic = ". $_GET['id'])) > $pg2){
				echo "[ <a class='ajax' id='fuckme' href='forumView.php?id=" . $_GET['id'] . "&pg=$pg2'> > </a>] ";
			}
			 ?> 
			<script>
$(document).ready(function(){
  $("#submitReply").click(function(e){
      e.preventDefault();
    $.ajax({type: "POST",
            url: "/forumReply.php",
            data: { body: $("#body").val(), id: $("#id").val() },
            success:function(result){
      //$("#container").html(result);
	  $("#container").load(location.href);
    }});
  });
});
/*!
	Autosize 1.18.17
	license: MIT
	http://www.jacklmoore.com/autosize
*/
!function(e){var t,o={className:"autosizejs",id:"autosizejs",append:"\n",callback:!1,resizeDelay:10,placeholder:!0},i='<textarea tabindex="-1" style="position:absolute; top:-999px; left:0; right:auto; bottom:auto; border:0; padding: 0; -moz-box-sizing:content-box; -webkit-box-sizing:content-box; box-sizing:content-box; word-wrap:break-word; height:0 !important; min-height:0 !important; overflow:hidden; transition:none; -webkit-transition:none; -moz-transition:none;"/>',a=["fontFamily","fontSize","fontWeight","fontStyle","letterSpacing","textTransform","wordSpacing","textIndent","whiteSpace"],n=e(i).data("autosize",!0)[0];n.style.lineHeight="99px","99px"===e(n).css("lineHeight")&&a.push("lineHeight"),n.style.lineHeight="",e.fn.autosize=function(i){return this.length?(i=e.extend({},o,i||{}),n.parentNode!==document.body&&e(document.body).append(n),this.each(function(){function o(){var t,o=window.getComputedStyle?window.getComputedStyle(u,null):null;o?(t=parseFloat(o.width),("border-box"===o.boxSizing||"border-box"===o.webkitBoxSizing||"border-box"===o.mozBoxSizing)&&e.each(["paddingLeft","paddingRight","borderLeftWidth","borderRightWidth"],function(e,i){t-=parseFloat(o[i])})):t=p.width(),n.style.width=Math.max(t,0)+"px"}function s(){var s={};if(t=u,n.className=i.className,n.id=i.id,d=parseFloat(p.css("maxHeight")),e.each(a,function(e,t){s[t]=p.css(t)}),e(n).css(s).attr("wrap",p.attr("wrap")),o(),window.chrome){var r=u.style.width;u.style.width="0px";{u.offsetWidth}u.style.width=r}}function r(){var e,a;t!==u?s():o(),n.value=!u.value&&i.placeholder?p.attr("placeholder")||"":u.value,n.value+=i.append||"",n.style.overflowY=u.style.overflowY,a=parseFloat(u.style.height)||0,n.scrollTop=0,n.scrollTop=9e4,e=n.scrollTop,d&&e>d?(u.style.overflowY="scroll",e=d):(u.style.overflowY="hidden",c>e&&(e=c)),e+=z,Math.abs(a-e)>.01&&(u.style.height=e+"px",n.className=n.className,w&&i.callback.call(u,u),p.trigger("autosize.resized"))}function l(){clearTimeout(h),h=setTimeout(function(){var e=p.width();e!==b&&(b=e,r())},parseInt(i.resizeDelay,10))}var d,c,h,u=this,p=e(u),z=0,w=e.isFunction(i.callback),f={height:u.style.height,overflow:u.style.overflow,overflowY:u.style.overflowY,wordWrap:u.style.wordWrap,resize:u.style.resize},b=p.width(),g=p.css("resize");p.data("autosize")||(p.data("autosize",!0),("border-box"===p.css("box-sizing")||"border-box"===p.css("-moz-box-sizing")||"border-box"===p.css("-webkit-box-sizing"))&&(z=p.outerHeight()-p.height()),c=Math.max(parseFloat(p.css("minHeight"))-z||0,p.height()),p.css({overflow:"hidden",overflowY:"hidden",wordWrap:"break-word"}),"vertical"===g?p.css("resize","none"):"both"===g&&p.css("resize","horizontal"),"onpropertychange"in u?"oninput"in u?p.on("input.autosize keyup.autosize",r):p.on("propertychange.autosize",function(){"value"===event.propertyName&&r()}):p.on("input.autosize",r),i.resizeDelay!==!1&&e(window).on("resize.autosize",l),p.on("autosize.resize",r),p.on("autosize.resizeIncludeStyle",function(){t=null,r()}),p.on("autosize.destroy",function(){t=null,clearTimeout(h),e(window).off("resize",l),p.off("autosize").off(".autosize").css(f).removeData("autosize")}),r())})):this}}(jQuery||$);
$('textarea').autosize();    

</script>
<?php 
		}else{ echo "Status error"; }
	}
}else{
	echo "<pre>I tried to come up with something. Nothing came up</pre>";
}
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){}else{
	require_once("foot.php");
}
?>