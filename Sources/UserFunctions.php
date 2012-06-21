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