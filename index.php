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

if($_GET['install'] === 'true' && file_exists('install.php')) {
	unlink('install.php');
	if(file_exists('install.sql')) {
		unlink('install.sql'); 
	}
}

// Require config file
require('config.php');
require(ABSPATH .'/Sources/Template.php');

// Pagination variables
if(isset($_GET['page'])){if((int)$_GET['page']>1){$page=(int)$_GET['page'];}}else{$page=0;}
$file = $_SERVER['SCRIPT_FILENAME'];

// Include theme files
$themeName = bloginfo('theme', 'r');
include('themes/'.$themeName.'/main.php');

?>