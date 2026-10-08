<?php
// Hack prevention.
if (!empty($HTTP_GET_VARS["GALLERY_BASEDIR"]) ||
		!empty($HTTP_POST_VARS["GALLERY_BASEDIR"]) ||
		!empty($HTTP_COOKIE_VARS["GALLERY_BASEDIR"])) {
	print "Security violation\n";
	exit;
}
?>
<?php require($GALLERY_BASEDIR . "init.php"); ?>
<?php
// Hack check
if (!$gallery->user->canAddToAlbum($gallery->album)) {
	exit;
}
	
if (!$boxes) {
	$boxes = 1;
}

?>

<html>
<head>
  <title>Add Photos</title>
  <?php echo getStyleSheetLink() ?>

<script language="Javascript">
<!--
	function reloadPage() {
		document.count_form.submit();
		return false;
	}
// -->
</script>
</head>
<body>
<span class="title">MOGELIJKHEID 1: Upload een ZIP-bestand of een enkele foto</span>

<?php echo makeFormIntro("add_photos.php",
			array("name" => "count_form",
				"method" => "POST")); ?>
Hoeveel bestanden wil je uploaden?
<select name="boxes" onChange='reloadPage()'>
<?php for ($i = 1; $i <= 10;  $i++) {
	echo "<option ";
        if ($i == $boxes) {
		echo "selected ";
	}
	echo "value=\"$i\">$i\n";

} ?>
</select>
<br>
</form>

<?php echo makeFormIntro("save_photos.php",
			array("name" => "upload_form",
				"enctype" => "multipart/form-data",
				"method" => "POST")); ?>
<input type="hidden" name="max_file_size" value="10000000">
<table>
<?php for ($i = 0; $i < $boxes;  $i++) { ?>
<tr><td>
Bestand</td>
<td><input name="userfile[]" type="file" size=40></td></tr>
<td>Onderschrift</td><td> <input name="usercaption[]" type="text" size=40></td></tr>
<tr><td></td></tr> 
<?php } ?>
</table>
<input type=checkbox name=setCaption value="1">Gebruik de bestandsnamen als onderschrift.
<br><br>
<center>
<input type="button" value="Plaatsen" onClick='opener.showProgress(); document.upload_form.submit()'>
<input type=submit value="Annuleren" onclick='parent.close()'>
</center>
</form>

<?php echo makeFormIntro("save_photos.php",
			array("name" => "uploadurl_form",
				"method" => "POST")); ?>

<br>
<hr>
<br>
<span class="title">MOGELIJKHEID 2: Haal de foto's van jouw webspace (FTP)</span>
<br><br>
<input type="text" name="urls[]" size=40 value="http://webspace.met.de.fotos">
<br><br>
<input type=checkbox name=setCaption value="1">Gebruik de bestandsnamen als onderschrift.<br>
<br>
<center>
<input type="button" value="Plaatsen" onClick='opener.showProgress(); document.uploadurl_form.submit()'>
<input type=submit value="Annuleren" onclick='parent.close()'>
</center>
</form>
</body>
</html>
