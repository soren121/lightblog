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
		int $group - Minimum group, or role level, required to use feature.

	Returns:
		bool - Boolean value based on the user's role level. (e.g. true/false)
*/
function permissions($permission)
{
	// Grab that database handle
	global $dbh;

	// Check if we've already fetched the permissions from the DB
	if(!isset($GLOBALS['permissions_data']))
	{
		$result = $dbh->query('
		SELECT *
		FROM role_permissions');

		// Super fun array time!
		$GLOBALS['permissions_data'] = array();

		// Toss those permissions into a multi-dimensional array
		while($row = $result->fetchObject())
		{
			if(!isset($GLOBALS['permissions_data'][$row->permission]))
			{
				$GLOBALS['permissions_data'][$row->permission] = array();
			}
			$GLOBALS['permissions_data'][$row->permission][$row->role_id] = $row->status;
		}
	}

	if(isset($GLOBALS['permissions_data'][$permission][user()->role()]) && $GLOBALS['permissions_data'][$permission][user()->role()] == 1)
	{
		return true;
	}
	else
	{
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

/*
	Function: users_load

	Loads the specified users' information.

	Parameters:
		mixed $users - Either an array of users to load or an integer specifying
									 the user to load.

	Returns:
		void - Nothing is returned by this function.
*/
function users_load($users)
{
	global $dbh;

	// We will turn it into an array if it isn't already.
	if(!is_array($users))
	{
		$users = array($users);
	}

	// The loaded_users array contains just what one might think :-P.
	if(!isset($GLOBALS['loaded_users']))
	{
		$GLOBALS['loaded_users'] = array();
	}

	// We don't want to load the same user multiple times.
	$users = array_unique($users);
	foreach($users as $key => $user_id)
	{
		$user_id = (int)$user_id;

		if($user_id <= 0 || isset($GLOBALS['loaded_users'][$user_id]))
		{
			// No need to load this one.
			unset($users[$key]);

			continue;
		}

		$users[$key] = (int)$user_id;
	}

	// Alright, do we have anything to load?
	if(count($users) > 0)
	{
		$request = $dbh->query("
			SELECT
				user_id, user_name, user_pass, user_email, display_name, user_role,
				user_ip, user_salt, user_activated, user_created
			FROM users
			WHERE user_id IN(". implode(', ', $users). ")");

		while($row = $request->fetch(SQLITE_ASSOC))
		{
			$GLOBALS['loaded_users'][$row['user_id']] = array(
																										'id' => $row['user_id'],
																										'user_name' => $row['user_name'],
																										'password' => $row['user_pass'],
																										'email' => $row['user_email'],
																										'display_name' => $row['display_name'],
																										'name' => !empty($row['display_name']) ? $row['display_name'] : $row['user_name'],
																										'role' => array(
																																'id' => $row['user_role'],
																																'name' => null,
																															),
																										'ip' => $row['user_ip'],
																										'salt' => $row['user_salt'],
																										'activated' => !empty($row['user_activated']),
																										'created' => $row['user_created'],
																									);
		}
	}
}

/*
	Function: users_get

	Retrieves the loaded users information.

	Parameters:
		mixed $users - An array containing the users' ID to retrieve or a single
									 integer.

	Returns:
		array - An array containing the loaded users' information. If $users was
						an array, then the array's keys are the users ID whereas if
						$users is a single integer just that user's information is
						returned. However, if the user does not exist, the value will be
						false.

	Note:
		The users information must have been loaded by the <users_load>
		function.
*/
function users_get($users)
{
	if(is_array($users))
	{
		$loaded = array();
		foreach($users as $user_id)
		{
			$loaded[$user_id] = users_get((int)$user_id);
		}

		return $loaded;
	}
	else
	{
		return isset($GLOBALS['loaded_users'][(int)$users]) ? $GLOBALS['loaded_users'][(int)$users] : false;
	}
}

/*
	Function: commenter_name

	Attempts to fetch, and then output, the commenter's user name saved when
	they commented.

	Parameters:
		none

	Returns:
		void - Nothing is returned by this function.
*/
function commenter_name()
{
	if(isset($_COOKIE[LBCOOKIE. '_cname']))
	{
		echo utf_htmlspecialchars($_COOKIE[LBCOOKIE. '_cname']);
	}
}

/*
	Function: commenter_email

	Attempts to fetch, and then output, the commenter's email that may have
	been saved when they commented.

	Parameters:
		none

	Returns:
		void
*/
function commenter_email()
{
	if(isset($_COOKIE[LBCOOKIE. '_cemail']))
	{
		echo utf_htmlspecialchars($_COOKIE[LBCOOKIE. '_cemail']);
	}
}

/*
	Function: commenter_website

	Attempts to fetch, and then output, the commenter's website that may have
	been saved when they commented.

	Parameters:
		none

	Returns:
		void
*/
function commenter_website()
{
	if(isset($_COOKIE[LBCOOKIE. '_curl']) && is_url($_COOKIE[LBCOOKIE. '_curl']))
	{
		echo utf_htmlspecialchars($_COOKIE[LBCOOKIE. '_curl']);
	}
}
?>