<?php

// let's stop them hackers =)
if(!defined("Light")) {
	die("DIE!");
}

$stselect = sqlite_query($handle, "SELECT * FROM coreinfo") or die("SQLite query error: code 02<br>".sqlite_error_string(sqlite_last_error($handle)));
	while($row = sqlite_fetch_array($stselect)) {
		$cmsinfo[$row['variable']] = stripslashes(stripslashes($row['value']));
	}
	
function clean($var) {
  $replace = array(
    '&' => '&amp;',
    '"' => '&quot;',
    "'" => '&#39;',
    '<' => '&lt;',
    '>' => '&gt;'
  );
  $var = str_replace(array_keys($replace), array_values($replace), $var);
  return $var;
}
	
$grav_default=$site_url."admin/style/gravatar.gif";
$gravatar = "http://www.gravatar.com/avatar.php?gravatar_id=".md5($_SESSION['email'])."&amp;default=".urlencode($grav_default)."&amp;size=60";
?>
