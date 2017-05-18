<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '../../mysqlConnect.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../settings.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../../randomFunctions.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/userSession.php');

if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){}else{
	require_once("head.php");
}

if ($_SESSION['logged'])
{
	if(isset($_POST['image_id'])){
		$_POST['image_id'] = $mysqli->real_escape_string($_POST['image_id']);
		$mysqli->query("DELETE FROM user_images WHERE username = '" . $_SESSION['name'] . "' AND image_id = '" . $_POST['image_id'] ."'");
	}
?>
<script>
$(document).ready(function(){
	$(".submit44").click(function(e){
		var row = $(this);
		e.preventDefault();
		$.ajax({type: "POST",
            url: "/appImage.php",
            data: { image_id: $(this).val() },
            success:function(result){ //This will trigger even if the mysql query failed
				$(row).closest("tr").remove();
				$(".response").html("The image dissolves into the void...").hide().fadeIn('slow');
			}
		});
	});
});

$(document).ready(function(){
	$(".image").click(function(e){
		e.preventDefault();
		var link = $(this).attr('href');
		$(".imageSpot").prepend("<br><img style='max-width: 800px' src='"+ link +"'>");
	});
});
</script><pre>
<p>Image Host</p>
Post Your pretty pictures here!
Current limits: 8mb/image image type: jpg/png max: 20 images/user (soon more!)

<form target="_blank" method="post" name="form" action="appImageUpload.php" enctype="multipart/form-data">
Image: <input type="file" name="image" title="lol"/> <input type="submit" name="submit" value="Upload" /></form>
<div class="response" id="response"></div>
<?php


$username = $_SESSION['name'];
	$result = $mysqli->query("SELECT * FROM user_images WHERE username = '$username'");
	if (mysqli_num_rows($result))
	{
		echo "Your images:";
		echo "<table cellspacing=\"5\"><tr><td>Image Name</td><td>Size</td><td>Link</td><td>Uploaded on</td></tr>";
		while ($row = mysqli_fetch_array($result))
		{
			$creation = substr($row['creation'], 0,10);
			$size = $row['file_size'] / 1024;
			echo "<tr class='removable'><td class='name'>" . $row['filename'] . "</td><td>" . (int)$size . " KB</td><td><a class='image' target='_blank' href='appImageView.php?id=" . $row['image_id'] . "'> Image ID: " . $row['image_id'] . "</a></td><td>" . $creation . "</td><td><form action=\"appImage.php\" method=\"post\" name=\"image_id\"><button type=\"submit\" class='submit44' name=\"image_id\" value=\"".$row['image_id']."\">Delete</button></form></td></tr>";
		}
	}
	echo "</table>";
?>
<div class='imageSpot'></div></pre>
<?php
}
else
{
?>
You need to be logged in to use the imagehost.
<?php
}
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){}else{
	require_once("foot.php");
}
?>