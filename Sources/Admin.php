<?php

/***********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	Sources/Admin.php
	
	©2009 soren121. All rights reserved.
	Released under the GNU General
	Public License. For all licensing
	information, please see the
	LICENSE.txt document included in this
	distribution.

***********************************************/

// Check if user is authentic
if($_SESSION['securestring'] !== $_COOKIE[bloginfo('title','r').'securestring']) {
	die('Session is invalid. Return to the <a href="'.bloginfo('url','r').'">homepage</a>.');
}

?>