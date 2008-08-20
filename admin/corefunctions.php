<?php

// let's stop them hackers =)
if(!defined("Light")) {
	die("DIE!");
}

// Settings grabber
$stselect = sqlite_query($handle, "SELECT * FROM coreinfo") or die("SQLite query error: code 02<br>".sqlite_error_string(sqlite_last_error($handle)));
	while($row = sqlite_fetch_array($stselect)) {
		$cmsinfo[$row['variable']] = stripslashes(stripslashes($row['value']));
	}
	
// Site URL grabber
$site_url = $cmsinfo['site_url'];

// SQL queries
$result01 = sqlite_query($handle, "SELECT * FROM posts ORDER BY id desc") or die("SQLite query error: code 02<br>".sqlite_error_string(sqlite_last_error($handle)));
$result02 = sqlite_query($handle, "SELECT * FROM comments WHERE post_id=".$post->id."") or die("SQLite query error: code 02<br>".sqlite_error_string(sqlite_last_error($handle)));
$result03 = sqlite_query($handle, "SELECT * FROM posts WHERE id=".$_GET['id']." ORDER BY id desc") or die("SQLite query error: code 02<br>".sqlite_error_string(sqlite_last_error($handle)));
$result04 = sqlite_query($handle, "SELECT * FROM comments WHERE post_id=".$_GET['id']." ORDER BY id desc") or die("SQLite query error: code 02<br>".sqlite_error_string(sqlite_last_error($handle)));
$result05 = sqlite_query($handle, "SELECT * FROM pages WHERE id=".$_GET['id']." ORDER BY id desc") or die("SQLite query error: code 02<br>".sqlite_error_string(sqlite_last_error($handle)));
$result06 = sqlite_query($handle,"SELECT title, id, post, date FROM posts ORDER BY id desc LIMIT 15");
$result07 = sqlite_query($handle, "SELECT * FROM categories ORDER BY id DESC") or die("SQLite query error: code 02<br>".sqlite_error_string(sqlite_last_error($handle)));
$result08 = sqlite_query($handle, "SELECT * FROM ".$_GET['type']."s WHERE id=".$_GET['id']."") or die("SQLite query error: code 02<br>".sqlite_error_string(sqlite_last_error($handle)));
$result09 = sqlite_query($handle, "SELECT * FROM ".$_GET['type']."s ORDER BY id desc") or die("SQLite query error: code 02<br>".sqlite_error_string(sqlite_last_error($handle)));
$result10 = sqlite_query($handle, "SELECT * FROM users ORDER BY id desc") or die("SQLite query error: code 02<br>".sqlite_error_string(sqlite_last_error($handle)));

// Gravatar retrieval	
$grav_default=$site_url."admin/style/gravatar.gif";
$gravatar = "http://www.gravatar.com/avatar.php?gravatar_id=".md5($_SESSION['email'])."&amp;default=".urlencode($grav_default)."&amp;size=60";
?>
