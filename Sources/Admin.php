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
		die('Either your session has expired or it is invalid. You can return to the <a href="'.bloginfo('url','r').'">homepage</a> now.');
	}
	
// Function to list themes in a drop-down box
function list_themes() {
	// List directories
	$dir = dirlist(ABSPATH .'/themes'); 
	foreach($dir as $k => $v) {
		if(bloginfo('theme','r') == $k) {
			echo '<option selected="selected" value="'.$k.'">'.$v.'</option>';
		}
		else {
			echo '<option value="'.$k.'">'.$v.'</option>';
		}		
	}
}

?>