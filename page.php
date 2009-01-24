<?php session_start();define("Light", true);require('config.php');require('admin/corefunctions.php');
$result05 = sqlite_query($handle, "SELECT * FROM pages WHERE id=".$_GET['id']." ORDER BY id desc") or die("SQLite query error: code 05<br>".sqlite_error_string(sqlite_last_error($handle)));
$result12 = sqlite_query($handle, "SELECT value FROM coreinfo WHERE variable='theme'") or die("SQLite query error: code 01<br>".sqlite_error_string(sqlite_last_error($handle)));
?>
<!--	LightBlog v0.9.0
		Copyright 2008 soren121. Some Rights Reserved.
		Licensed under the General Public License v3.
		For more info, see the LICENSE.txt file included.
-->

<?php 
@list($themeName) = sqlite_fetch_array($result12);
include('themes/'.$themeName.'/head.php');
include('themes/'.$themeName.'/sidebar.php');
include('themes/'.$themeName.'/page.php');
include('themes/'.$themeName.'/footer.php'); 
?>
