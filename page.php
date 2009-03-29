<?php session_start();

/*********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	page.php
	
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

// Request page from database
$result05 = $dbh->query("SELECT * FROM pages WHERE id=".$_GET['id']." ORDER BY id desc") or die(sqlite_error_string($dbh->lastError));
$result10 = $dbh->query("SELECT * FROM pages ORDER BY id desc") or die(sqlite_error_string($dbh->lastError));

// Include theme files 
$themeName = bloginfo('theme');
include('themes/'.$themeName.'/head.php');
include('themes/'.$themeName.'/sidebar.php');
include('themes/'.$themeName.'/page.php');
include('themes/'.$themeName.'/footer.php'); 

?>
