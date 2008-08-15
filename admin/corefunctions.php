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
	'/\[u\](.*?)\[\/u\]/is',
	'/\[s\](.*?)\[\/s\]/is',
	'/\[img\](.*?)\[\/img\]/is',
	'/\[quote=\"(.*?)\"\](.*?)\[\/quote\]/is',
	'/\[quote\](.*?)\[\/quote\]/is',
	'/\[url=(.*?)\](.*?)\[\/url\]/is',
	'/\[url\](.*?)\[\/url\]/is',
	'/\[break\](.*?)\[\/break\]/is',
	'/\[youtube\](.*?)\[\/youtube\]/is');

	// Translate BBCode to XHTML
	$simple_replace = array(
	'<strong>$1</strong>',
	'<em>$1</em>',
	'<span style="text-decoration: underline;">$1</span>',
	'<del>$1</del>',
	'<img src=\"$1\" alt=\"\" />',
	'$1 said: \"$2\"',
	'"$1"',
	'<a href=\"$1\">$2</a>',
	'<a href=\"$1\">$1</a>',
	'<br />',
	'<object type=\"application/x-shockwave-flash\" width=\"425\" height=\"350\" data=\"$1\"><param name=\"movie\" value=\"$1\"/></object>');

    // Output HTML translations
    $str = preg_replace ($simple_search, $simple_replace, $str);
    return $str;
}

// Gravatar retrieval	
$grav_default=$site_url."admin/style/gravatar.gif";
$gravatar = "http://www.gravatar.com/avatar.php?gravatar_id=".md5($_SESSION['email'])."&amp;default=".urlencode($grav_default)."&amp;size=60";
?>
