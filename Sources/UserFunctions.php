<?php

/***********************************************

	LightBlog 0.9
	SQLite blogging platform

	Sources/UserFunctions.php

	©2008-2012 The LightBlog Team. All
	rights reserved. Released under the
	GNU General Public License 3. For
	all licensing information, please
	see the LICENSE.txt document
	included in this distribution.

***********************************************/

/*
	Function: get_userinfo

	Safely obtains a piece of information about the user currently logged in.

	Parameters:

		var - The name of the info we are getting.

	Returns:

		The requested information about the user (e.g. their email address.)
*/
function get_userinfo($var)
{
	// Does that value exist?
	if(!isset($_SESSION[$var]))
	{
		// Nope, so return nothing
		return null;
	}
	else {
		return $_SESSION[$var];
	}
}

function userinfo($var)
{
	echo get_userinfo($var);
}

/*
	Function: permissions

	Determines if the user can do something.

	Parameters:

		group - Minimum group, or role level, required to use feature.

	Returns:

		Boolean value based on the user's role level. (e.g. true/false)
*/
function permissions($group)
{
	// Fetch the session info
	if(user()->role() >= $group)
	{
		// Return true if they're allowed
		return true;
	}
	else
	{
		// Return false if they aren't
		return false;
	}
}

function gravatar($email = null, $size = 80, $default = 'mm', $rating = 'pg')
{
	if($email == null)
	{
		$email = user()->email();
	}

	return 'http://www.gravatar.com/avatar/'. md5(strtolower(trim($email))). '?s='. $size. '&amp;d='. $default. '&amp;rating='. $rating;
}
?>