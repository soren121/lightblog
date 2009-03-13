<?php

require_once('..\config.php');

// Bloginfo grabber
function bloginfo($var) {
	$result = sqlite_query($handle, "SELECT value FROM coreinfo WHERE variable='".$var."'") or die("SQLite query error: code 01<br>".sqlite_error_string(sqlite_last_error($handle)));
	return sqlite_fetch_array($result);
}

// Gravatar retrieval	
$grav_default=$site_url."admin/style/gravatar.gif";
$gravatar = "http://www.gravatar.com/avatar.php?gravatar_id=".md5($_SESSION['email'])."&amp;default=".urlencode($grav_default)."&amp;size=60";
?>