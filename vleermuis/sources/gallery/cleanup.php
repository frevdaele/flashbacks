<font size=+2>
Gallery Cleanup Script
</font>
<p>
Because Gallery runs as part of the webserver, any files it creates
are owned by the webserver process.  If you want to modify those
files yourself, you need to get the webserver to change the permissions
on them so that you have access.  That's what this script is for.
Simply enter the path to your albums directory below and this script
will make every file in that path (that it can access) writable
by everybody.  Then, you can do whatever you want to the files.
</font>

<p>

Path (on your filesystem) to a file or directory to fix?
<br>
<font size=-1>(examples: /home/~you/public_html/albums, /usr/www/htdocs/albums)</font>
<br>
<form>
<input name=dir>
<input type=submit value="Go!">
</form>

<?

if ($dir) {
	echo "<hr>";
	fix($dir);
}

function fix($obj) {
	if (is_dir($obj)) {
		status("Directory: ", $obj);
		if ($fd = opendir($obj)) {
			while (($child = readdir($fd)) != false) {
				if (!strcmp($child, ".")) {
					continue;
				}

				if (!strcmp($child, "..")) {
					continue;
				}

				$fullpath = "$obj/$child";
				fix($fullpath);
			}
			chmod($obj, 0777);
		} else {
			error("Error reading dir", $obj);
		}
	} else if (is_file($obj)) {
		print "File: <b>$obj</b><br>";
		chmod($obj, 0666);
	}
}

function status($msg, $obj) {
	print "$msg: <b>$obj</b><br>";
}

function error($msg, $obj) {
	print "<font color=red>$msg: <b>$obj</b></font><br>";
}

?>

