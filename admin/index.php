<?php session_start();

/***********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	admin/index.php
	
	©2009 soren121. All rights reserved.
	Released under the GNU General
	Public License. For all licensing
	information, please see the
	LICENSE.txt document included in this
	distribution.

***********************************************/

// Require config file
require('../config.php');
require(ABSPATH .'/Sources/Core.php');

# check user status
if(isset($_SESSION['securestring'])) {
	header('Location: dashboard.php');
}
else {
	header('Location: login.php');
}

?>