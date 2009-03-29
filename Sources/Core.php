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

function bloginfo($var, $output = 'e') {
	global $dbh;
	static $bloginfo = null;

	if($bloginfo == null) {
		$result = $dbh->query('SELECT * FROM core') or die(sqlite_error_string($dbh->lastError));
		$bloginfo = array();
		while($row = $result->fetchObject()) {
			$bloginfo[$row->variable] = $row->value;
		}
	}
	if($output == 'e') {
		return !empty($bloginfo[$var]) ? $bloginfo[$var] : false;
	}
	else {
		echo !empty($bloginfo[$var]) ? $bloginfo[$var] : false;
	}	
}

function fetchGravatar($email, $size = 30, $output = 'e') {
	if($output == 'e') {
		echo "http://www.gravatar.com/avatar.php?gravatar_id=".md5($email)."&amp;size=".$size;
	}
	else {
		return "http://www.gravatar.com/avatar.php?gravatar_id=".md5($email)."&amp;size=".$size;
	}
}

?>