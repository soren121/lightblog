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
require(ABSPATH .'/Sources/Core.php');

# Functions to find the start for a query based on the page number
function findStart($input) { return ($input - 1) * 6; }

// Request posts and pages from database
if($_GET['page'] > 1) {
	$result01 = $dbh->query("SELECT * FROM posts ORDER BY id desc LIMIT ".findStart($_GET['page']).",6") or die(sqlite_error_string($dbh->lastError));
}
else {
	$result01 = $dbh->query("SELECT * FROM posts ORDER BY id desc LIMIT 0,6") or die(sqlite_error_string($dbh->lastError));
}
$result10 = $dbh->query("SELECT * FROM pages ORDER BY id desc") or die(sqlite_error_string($dbh->lastError));

// Include theme files
$themeName = bloginfo('theme', 'r');
include('themes/'.$themeName.'/main.php');

?>