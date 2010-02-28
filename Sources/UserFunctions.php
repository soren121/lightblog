<?php

/***********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	Sources/UserFunctions.php
	
	©2009-2010 The LightBlog Team. All 
	rights reserved. Released under the 
	GNU General Public License 3. For 
	all licensing information, please 
	see the LICENSE.txt document 
	included in this distribution.

***********************************************/

// Shutdown Magic Quotes automatically
// Highly inefficient, but there isn't much we can do about it
if(get_magic_quotes_gpc()) {
	function stripslashes_gpc(&$value) {
		$value = stripslashes($value);
	}
	array_walk_recursive($_GET, 'stripslashes_gpc');
    array_walk_recursive($_POST, 'stripslashes_gpc');
    array_walk_recursive($_COOKIE, 'stripslashes_gpc');
    array_walk_recursive($_REQUEST, 'stripslashes_gpc');
}

/*
	Function: fetchGravatar
	
	Obtains the URL for a user's Gravatar <http://www.gravatar.com/> based on their email address.
	
	Parameters:
		
		email - The user's email address.
		size - The dimensions (in pixels) to output the Gravatar in.
		output - Specifies whether the version will be echoed or returned.
		
	Returns:
	
		The full URL for the user's Gravatar.
*/
function fetchGravatar($email, $size = 32, $output = 'e') {
	# Is the Gravatar being echoed?
	if($output == 'e') {
		# Yep, so echo the URL
		echo "http://www.gravatar.com/avatar.php?gravatar_id=".md5($email)."&amp;size=".(int)$size;
	}
	else {
		# It's not being echoed, so return the URL
		return "http://www.gravatar.com/avatar.php?gravatar_id=".md5($email)."&amp;size=".(int)$size;
	}
}

/*
	Function: userFetch
	
	Safely obtains a piece of information about the user currently logged in.
	
	Parameters:
	
		var - The name of the info we are getting.
		output - Specifies whether the version will be echoed or returned.
		
	Returns:
	
		The requested information about the user (e.g. their email address.)
*/
function userFetch($var, $output = 'e') {
	# Does that value exist?
	if(!isset($_SESSION[$var])) { 
		# Nope, so return nothing
		return null;
	}
	else {
		# It exists, so echo/return it
		if($output == 'e') {
			echo $_SESSION[$var];
		}
		else {
			return $_SESSION[$var];
		}
	}
}

/*
	Function: permissions
	
	Determines if the user can do something.
	
	Parameters:
	
		group - Minimum group, or role level, required to use feature.
		
	Returns:
	
		Boolean value based on the user's role level. (e.g. true/false)
*/
function permissions($group) {
	# Fetch the session info
	if(userFetch('role', 1) >= $group) {
		# Return true if they're allowed
		return true;
	}
	
	else {
		# Return false if they aren't
		return false;
	}
}

?>
