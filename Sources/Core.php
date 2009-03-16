<?php

/***********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	Core.php
	
	©2009 soren121. All rights reserved.
	Released under the GNU General
	Public License. For all licensing
	information, please see the
	LICENSE.txt document included in this
	distribution.

***********************************************/

// Open database if not open
$dbh = sqlite_popen( DBH );

function bloginfo($var) {
	$result = sqlite_query($dbh, "SELECT value FROM coreinfo WHERE variable='".$var."'") or die("SQLite query error: code 01<br>".sqlite_error_string(sqlite_last_error($dbh)));
	return sqlite_fetch_array($result);
}

function fetchGravatar($email, $size) {
	return "http://www.gravatar.com/avatar.php?gravatar_id=".md5($email)."&amp;size=".$size;
}

?>