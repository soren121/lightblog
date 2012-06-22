<?php

/***********************************************

	LightBlog 0.9
	SQLite blogging platform

	Sources/DatabaseFunctions.php

	©2008-2012 The LightBlog Team. All
	rights reserved. Released under the
	GNU General Public License 3. For
	all licensing information, please
	see the LICENSE.txt document
	included in this distribution.

***********************************************/

/*
	Function: get_bloginfo

	Returns the value of a given row.

	Parameters:

		var - Row to obtain value from.

	Returns:

		The value of the given row.
*/
function get_bloginfo($var)
{
	// Global the database handle
	global $dbh;

	// Make PHP remember $bloginfo next time
	static $bloginfo = null;

	// If this is the first time bloginfo's been called...
	if($bloginfo === null)
	{
		$result = $dbh->query('SELECT * FROM core') or die(sqlite_error_string($dbh->lastError));

		// Let's make an array!
		$bloginfo = array();

		// For each row, set a key with the value
		while($row = $result->fetchObject())
		{
			$bloginfo[$row->variable] = $row->value;
		}

		if(!isset($bloginfo['themeurl']))
		{
			// Set the theme URL
			$bloginfo['themeurl'] = $bloginfo['url'].'themes/'.$bloginfo['theme'];
		}
	}

	// Are we echoing or returning?
	return $bloginfo[$var];
}

function bloginfo($var)
{
	echo get_bloginfo($var);
}

function get_roles($role = null)
{
	global $dbh;
	static $rolequery = null;
	if($rolequery === null)
	{
		$result = $dbh->query("SELECT id,role FROM roles") or die(sqlite_error_string($dbh->lastError));
		$roles = array();
		while($row = $result->fetchObject())
		{
			$roles[$row->id] = $row->role;
		}
	}
	if(!is_null($role))
	{
		return $roles[$role];
	}
	else
	{
		return $roles;
	}
}

?>