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

// BBCode function	
function bbcode_format ($str) {
    // Strip HTML tags
	$str = htmlentities($str);

	// Find BBCode
    $simple_search = array(
    '/\[b\](.*?)\[\/b\]/is',                               
    '/\[i\](.*?)\[\/i\]/is',                               
    '/\[u\](.*?)\[\/u\]/is'
    );

	// Translate BBCode to HTML
    $simple_replace = array(
    '<strong>$1</strong>',
    '<em>$1</em>',
    '<u>$1</u>'
    );

    // Output HTML translations
    $str = preg_replace ($simple_search, $simple_replace, $str);
    return $str;
}

// Gravatar retrieval	
$grav_default=$site_url."admin/style/gravatar.gif";
$gravatar = "http://www.gravatar.com/avatar.php?gravatar_id=".md5($_SESSION['email'])."&amp;default=".urlencode($grav_default)."&amp;size=60";
?>
