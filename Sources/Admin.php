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

// Require Core.php if it isn't already loaded
require_once('Core.php');

// Authentication check
if($_SESSION['securestring'] !== $_COOKIE[strtolower(bloginfo('title','r')).'securestring']) {
	die('Session is invalid. Return to the <a href="'.bloginfo('url','r').'">homepage</a>.');
}

// Similar IP lock
	// Get first 3 octets of current IP
	$current_ip = explode('.', get_ip());
	$current_ip = $current_ip[0] . '.' . $current_ip[1] . '.' . $current_ip[2];

	// Get first 3 octets of session IP
	$session_ip = explode('.', $_SESSION['ip']);
	$session_ip = $session_ip[0] . '.' . $session_ip[1] . '.' . $session_ip[2];

	if($current_ip !== $session_ip) {
		session_destroy();
		die('Session is invalid. Return to the <a href="'.bloginfo('url','r').'">homepage</a>.');
	}

?>