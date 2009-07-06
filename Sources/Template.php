<?php

/***********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	Sources/Template.php
	
	©2009 soren121. All rights reserved.
	Released under the GNU General
	Public License. For all licensing
	information, please see the
	LICENSE.txt document included in this
	distribution.

***********************************************/

// Open database
$dbh = new SQLiteDatabase( DBH );

// Function to check for posts
function have_posts() {
	global $dbh;
	$result = $dbh->query("SELECT * FROM posts");
	if($result->numRows > 0) {
		return true;
	}
	else {
		return false;
	}
}

// Function to loop through posts
function postLoop() {
	
}

?>