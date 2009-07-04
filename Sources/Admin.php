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
	unset($current_ip[3]);
	$current_ip = implode('.', $current_ip);

	// Get first 3 octets of session IP
	$session_ip = explode('.', $_SESSION['ip']);
	unset($session_ip[3]);
	$session_ip = implode('.', $session_ip);

	if($current_ip !== $session_ip) {
		session_destroy();
		die('Session is invalid. Return to the <a href="'.bloginfo('url','r').'">homepage</a>.');
	}

?>