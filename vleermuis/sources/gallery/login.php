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
// Security check.
$uname = removeTags($uname);
?>

<html>
<head>
  <title>Login to <?php echo $gallery->app->galleryTitle?></title>
  <?php echo getStyleSheetLink() ?>
</head>
<body>

<center>
<span class="popuphead">None shall pass!</span>
<br>
<br>
<?php
if ($submit) {
	if ($uname && $gallerypassword) {
		$tmpUser = $gallery->userDB->getUserByUsername($uname);
		if ($tmpUser && $tmpUser->isCorrectPassword($gallerypassword)) {
			$gallery->session->username = $uname;
			dismissAndReload();
		} else {
			$invalid = 1;
			$gallerypassword = null;
		}
	} else {
		$error = 1;
	}
}
?>

<?php echo makeFormIntro("login.php", array("name" => "login_form", "method" => "POST")); ?>
Als je zelf een hele hoop leuke foto's hebt van de Vleermuis, kan je met een geldige loginnaam zelf foto's toevoegen, onderschriften aanpassen en albums aanmaken. 
Stuur een mailtje naar <b><a href='http://www.scene24.net/words/email.php' target='_blank'>Gaai</a></b> als je wilt registreren.<br><br>
<p>

<table>
<?php if ($invalid) { ?>
 <tr>
  <td colspan=2>
   <?php echo gallery_error("Ongeldige gebruikersnaam of paswoord!"); ?>
  </td>
 </tr>
<?php } ?>

 <tr>
  <td>
   Naam
  </td>
  <td>
   <input type=text name="uname" value=<?php echo $uname?>>
  </td>
 </tr>

<?php if ($error && !$uname) { ?>
 <tr>
  <td colspan=2 align=center>
   <?php echo gallery_error("U moet een gebruikersnaam opgeven"); ?>
  </td>
 </tr>
<?php } ?>

 <tr>
  <td>
   Paswoord
  </td>
  <td>
   <input type=password name="gallerypassword">
  </td>
 </tr>

<?php if ($error && !$gallerypassword) { ?>
 <tr>
  <td colspan=2 align=center>
   <?php echo gallery_error("U moet een paswoord opgeven"); ?>
  </td>
 </tr>
<?php } ?>

</table>
<p>
<input type=submit name="submit" value="Aanmelden">
<input type=submit name="submit" value="Annuleren" onclick='parent.close()'>
</form>

<script language="javascript1.2">
<!--
// position cursor in top form field
document.login_form.uname.focus();
//--> 
</script>

</body>
</html>
