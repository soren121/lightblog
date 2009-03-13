<?php

/**************************************

	LightBlog 0.9
	SQLite blogging platform
	
	config.php
	
	©2009 soren121. All rights reserved.
	Released under the GNU General
	Public License. For all licensing
	information, please see the
	LICENSE.txt document included in this
	distribution.

**************************************/

// Set SQLite connection information
define('DBH', 'absolute path to database here');

/*-------DO NOT EDIT BELOW THIS LINE!---------*/

// Define absolute path of main directory here
if(!defined('ABSPATH')) {
	define('ABSPATH', dirname(__FILE__));
}

// Include Core functions and vars
require_once(ABSPATH .'/Sources/Core.php');

?>