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

// Request post and comments from database
$result03 = sqlite_query( DBH , "SELECT * FROM posts WHERE id=".$_GET['id']." ORDER BY id desc") or die("SQLite query error: code 03<br>".sqlite_error_string(sqlite_last_error( DBH )));
$result04 = sqlite_query( DBH , "SELECT * FROM comments WHERE post_id=".$_GET['id']." ORDER BY id desc") or die("SQLite query error: code 04<br>".sqlite_error_string(sqlite_last_error( DBH )));
$result12 = sqlite_query( DBH , "SELECT value FROM coreinfo WHERE variable='theme'") or die("SQLite query error: code 01<br>".sqlite_error_string(sqlite_last_error( DBH )));

// Include theme files
$themeName = bloginfo('theme');
include('themes/'.$themeName.'/head.php');
include('themes/'.$themeName.'/sidebar.php');
include('themes/'.$themeName.'/post.php');
include('themes/'.$themeName.'/footer.php');

?>