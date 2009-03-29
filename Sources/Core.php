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
$dbh = new SQLiteDatabase( DBH );

function bloginfo($var) {
	$dbh = new SQLiteDatabase( DBH );
	$result = $dbh->query("SELECT value FROM core WHERE variable='".$var."'") or die(sqlite_error_string($dbh->lastError));
	return $result->fetchSingle();
}

function fetchGravatar($email, $size = 30) {
	echo "http://www.gravatar.com/avatar.php?gravatar_id=".md5($email)."&amp;size=".$size;
}

?>