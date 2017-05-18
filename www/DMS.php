<?php
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){}else{
	require_once("head.php");
}?>
<form method='post' action='/DMS/index.php'>
	<input type="text" name="url" id="url" style='width: 400px;' value="" placeholder="http://">
	<input type="submit" id="submit" name="commit" value="Blastoff!">
</form>
<script type="text/javascript">
	document.getElementsByName("url")[0].focus();
</script>
<?php
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'){}else{
	require_once("foot.php");
}
?>