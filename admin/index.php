<?php session_start();

/***********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	admin/index.php
	
	©2009-2010 The LightBlog Team. All 
	rights reserved. Released under the 
	GNU General Public License 3. For 
	all licensing information, please 
	see the LICENSE.txt document 
	included in this distribution.

***********************************************/

// Require config file
require('../config.php');
require(ABSPATH .'/Sources/UserFunctions.php');

# check user status
if(userFetch('username', 'r') != false && permissions(1)) {
	header('Location: dashboard.php');
}
else {
	header('Location: login.php');
}

?>
