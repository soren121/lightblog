<?php
/*********************************************

	LightBlog 0.9
	SQLite blogging platform

	index.php

	©2008-2012 The LightBlog Team. All
	rights reserved. Released under the
	GNU General Public License 3. For
	all licensing information, please
	see the LICENSE.txt document
	included in this distribution.

*********************************************/

// Require config file
require('Sources/Core.php');

// Include theme files
$themeName = get_bloginfo('theme');

// If it isn't a post or page we're showing...
if(!isset($_GET['post']) && !isset($_GET['page'])) {
	// Require the proper loop class
	require(ABSPATH .'/Sources/Class.PostLoop.php');

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
		// Require the proper loop class
		require(ABSPATH .'/Sources/Class.PostLoop.php');
		require(ABSPATH .'/Sources/Class.CommentLoop.php');
		// Get post ID
		$GLOBALS['pid'] = (int)$_GET['post'];
		$GLOBALS['postquery']['type'] = 'post';
		// Display appropriate theme file
		include('themes/'.$themeName.'/post.php');
	}

	elseif(isset($_GET['page'])) {
		// Require the proper loop class
		require(ABSPATH .'/Sources/Class.PageLoop.php');
		// Get page ID
		$GLOBALS['pid'] = (int)$_GET['page'];
		$GLOBALS['postquery']['type'] = 'page';
		// Display appropriate theme file
		include('themes/'.$themeName.'/page.php');
	}
}

?>