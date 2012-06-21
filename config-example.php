<?php

/*********************************************

	LightBlog 0.9
	SQLite blogging platform

	config.php

	©2008-2012 The LightBlog Team. All
	rights reserved. Released under the
	GNU General Public License 3. For
	all licensing information, please
	see the LICENSE.txt document
	included in this distribution.

*********************************************/

// Constant: DBH
// Defines the path to the SQLite database
define('DBH', 'absolute path to database here');

define('LBCOOKIE', 'name of login cookie');

/*-------DO NOT EDIT BELOW THIS LINE!---------*/

// Define absolute path of main directory here
if(!defined('ABSPATH')) {
	// Constant: ABSPATH
	// Defines the absolute path to LightBlog's main directory
	define('ABSPATH', dirname(__FILE__));
}

?>
