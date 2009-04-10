<?php session_start();

/*********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	post.php
	
	©2009 soren121. All rights reserved.
	Released under the GNU General
	Public License. For all licensing
	information, please see the
	LICENSE.txt document included in this
	distribution.

*********************************************/

// Require config file
require('config.php');
require(ABSPATH .'/Sources/Core.php');

// Request post and comments from database
$result03 = $dbh->query("SELECT * FROM posts WHERE id=".(int)$_GET['id']." ORDER BY id desc") or die(sqlite_error_string($dbh->lastError));
$result04 = $dbh->query("SELECT * FROM comments WHERE post_id=".(int)$_GET['id']." ORDER BY id asc") or die(sqlite_error_string($dbh->lastError));
$result10 = $dbh->query("SELECT * FROM pages ORDER BY id desc") or die(sqlite_error_string($dbh->lastError));

// Include theme files
$themeName = bloginfo('theme', 'r');
include('themes/'.$themeName.'/head.php');
include('themes/'.$themeName.'/sidebar.php');
include('themes/'.$themeName.'/post.php');
include('themes/'.$themeName.'/footer.php');

?>