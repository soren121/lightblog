<?php

/***********************************************

	LightBlog 0.9
	SQLite blogging platform

	Sources/Admin.php

	©2009-2010 The LightBlog Team. All 
	rights reserved. Released under the 
	GNU General Public License 3. For 
	all licensing information, please 
	see the LICENSE.txt document 
	included in this distribution.

***********************************************/

// Check for session status
if(userFetch('username', 'r') == false && permissions(1) == false) {
	header('Location: login.php');
}

?>