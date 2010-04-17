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

if(!isset($_GET['post']) && !isset($_GET['page'])) {
	// Pagination variables
	if(isset($_GET['page'])){if((int)$_GET['page']>1){$page=(int)$_GET['page'];}}else{$page=0;}
	$file = $_SERVER['SCRIPT_FILENAME'];

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

else {	
	if(isset($_GET['post'])) {		
		$pid = (int)$_GET['post'];
		include('themes/'.$themeName.'/post.php');
	}
	
	elseif(isset($_GET['page'])) {
		$pid = (int)$_GET['page'];
		include('themes/'.$themeName.'/page.php');
	}
}

?>