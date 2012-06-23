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
		reload - Reload the settings again, forcibly.

	Returns:

		The value of the given row.
*/
function get_bloginfo($var, $reload = false)
{
	// Global the database handle
	global $dbh;

	if(!isset($GLOBALS['bloginfo_data']))
	{
		$GLOBALS['bloginfo_data'] = null;
	}

	// If this is the first time bloginfo's been called...
	if($GLOBALS['bloginfo_data'] === null || !empty($reload))
	{
		$result = $dbh->query('
			SELECT
				variable, value
			FROM settings') or die(sqlite_error_string($dbh->lastError));

		// Let's make an array!
		$GLOBALS['bloginfo_data'] = array();

		// For each row, set a key with the value
		while($row = $result->fetchObject())
		{
			$GLOBALS['bloginfo_data'][$row->variable] = $row->value;
		}

		if(!isset($GLOBALS['bloginfo_data']['themeurl']))
		{
			// Set the theme URL
			$GLOBALS['bloginfo_data']['themeurl'] = $GLOBALS['bloginfo_data']['url'].'themes/'.$GLOBALS['bloginfo_data']['theme'];
		}
	}

	return array_key_exists($var, $GLOBALS['bloginfo_data']) ? $GLOBALS['bloginfo_data'][$var] : false;
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
		$result = $dbh->query('
			SELECT
				role_id, role_name
			FROM roles') or die(sqlite_error_string($dbh->lastError));

		$roles = array();
		while($row = $result->fetchObject())
		{
			$roles[$row->role_id] = $row->role_name;
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