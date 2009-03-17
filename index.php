<?php session_start();

/*********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	index.php
	
	©2009 soren121. All rights reserved.
	Released under the GNU General
	Public License. For all licensing
	information, please see the
	LICENSE.txt document included in this
	distribution.

*********************************************/

// Check if LightBlog is installed
if(!file_exists('config.php')){ 
	// It isn't, so head to the installer
	header('Location: install.php');
}

// Require config file
require('config.php');

// Open database if not open
$dbh = sqlite_popen( DBH );

// Request posts from database
$result01 = sqlite_query($dbh, "SELECT * FROM posts ORDER BY id desc") or die("SQLite query error: code 01<br>".sqlite_error_string(sqlite_last_error($dbh)));

// Include theme files
$themeName = bloginfo('theme');
include('themes/'.$themeName.'/head.php');
include('themes/'.$themeName.'/sidebar.php');
include('themes/'.$themeName.'/main.php');
include('themes/'.$themeName.'/footer.php');

// Queries done, close database
sqlite_close($dbh);

?>