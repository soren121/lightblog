<?php

/***********************************************

	LightBlog 0.9
	SQLite blogging platform

	Sources/Admin.php

	©2008-2012 The LightBlog Team. All
	rights reserved. Released under the
	GNU General Public License 3. For
	all licensing information, please
	see the LICENSE.txt document
	included in this distribution.

***********************************************/

// Check for session status
if(get_userinfo('username') == false && permissions(1) == false)
{
	header('Location: login.php?return_to='.urlencode($_SERVER['REQUEST_URI']));
}
?>