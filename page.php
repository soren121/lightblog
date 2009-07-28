<?php session_start();

/*********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	page.php
	
	�2009 soren121. All rights reserved.
	Released under the GNU General
	Public License. For all licensing
	information, please see the
	LICENSE.txt document included in this
	distribution.

*********************************************/

// Require config file
require('config.php');
require(ABSPATH .'/Sources/Template.php');

$pid = (int)$_GET['id'];

// Include theme files 
$themeName = bloginfo('theme', 'r');
include('themes/'.$themeName.'/page.php');

?>
