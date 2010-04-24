<?php session_start();

/*********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	index.php
	
	�2009-2010 The LightBlog Team. All 
	rights reserved. Released under the 
	GNU General Public License 3. For 
	all licensing information, please 
	see the LICENSE.txt document 
	included in this distribution.

*********************************************/

// Check if LightBlog is installed
if(!file_exists('config.php')){ 
	// It isn't, so head to the installer
	header('Location: install.php');
}

if(isset($_GET['install']) && $_GET['install'] === 'true' && file_exists('install.php')) {
	unlink('install.php');
	if(file_exists('install.sql')) {
		unlink('install.sql'); 
	}
}

// Require config file
require('config.php');
require(ABSPATH .'/Sources/Template.php');

// Include theme files
$themeName = bloginfo('theme', 'r');

// If it isn't a post or page we're showing...
if(!isset($_GET['post']) && !isset($_GET['page'])) {
	// Pagination variables
	$file = basename($_SERVER['SCRIPT_FILENAME']);
	if(isset($_GET['p'])) {
		$page = (int)$_GET['p'];
	}
	else {
		$page = 1;
	}
	
	// Display the right post view
	if(isset($_GET['archive'])) {
		$GLOBALS['postquery']['type'] = 'archive';
		$GLOBALS['postquery']['date'] = (int)$_GET['archive'];
	}

	elseif(isset($_GET['category'])) {
		$GLOBALS['postquery']['type'] = 'category';
		$GLOBALS['postquery']['catid'] = (int)$_GET['category'];
	}

	else {
		$GLOBALS['postquery']['type'] = 'latest';
	}

	// Include main theme file
	include('themes/'.$themeName.'/main.php');
}

// Looks like it is a post or page
else {	
	if(isset($_GET['post'])) {
		// Get post ID	
		$pid = (int)$_GET['post'];
		// Display appropriate theme file
		include('themes/'.$themeName.'/post.php');
	}
	
	elseif(isset($_GET['page'])) {
		// Get page ID
		$pid = (int)$_GET['page'];
		// Display appropriate theme file
		include('themes/'.$themeName.'/page.php');
	}
}

?>