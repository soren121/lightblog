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

# check user status
if(userFetch('username', 'r') !== null) {
	header('Location: dashboard.php');
}
else {
	header('Location: login.php');
}

?>