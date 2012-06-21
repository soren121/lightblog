<?php
/***********************************************

	LightBlog 0.9
	SQLite blogging platform

	Sources/User.php

	©2008-2012 The LightBlog Team. All
	rights reserved. Released under the
	GNU General Public License 3. For
	all licensing information, please
	see the LICENSE.txt document
	included in this distribution.

***********************************************/

/*
	Function: user_init

	If the current user is actually logged in, this function will load their
	information for use by their respective functions.

	Parameters:
		none

	Returns:
		void - Nothing is returned by this function.
*/
function user_init()
{
	// Perhaps they are logged in?
	// In which case, we can let the User class do the work for us :-).
	user();
}

/*
	Function: user_login

	Processes the log in request and sets the proper cookies and session
	information to keep them logged in if it is a success.

	Parameters:
		array $options

	Returns:
		array
*/
function user_login($options)
{
	global $dbh;

	loadLanguage('login');

	$messages = array();

	// We need their user name.
	if(empty($options['username']))
	{
		$messages[] = l('Please enter a user name.');
	}
	// Now we need to see if the user exists.
	else
	{
		$request = $dbh->query("
			SELECT
				id, password, ip, salt
			FROM users
			WHERE LOWER(username) = '". sqlite_escape_string(utf_strtolower(utf_htmlspecialchars($options['username']))). "'
			LIMIT 1");

		if($request->numRows() == 0)
		{
			// We didn't find their user name, but we won't tell them whether it
			// was due to the password being wrong or their user name :P.
			$messages[] = l('Incorrect user name or password.');
		}
		else
		{
			list($user_id, $user_pass, $user_ip, $user_salt) = $request->fetch(SQLITE_NUM);
		}
	}

	// Make sure we don't have any messages yet.
	if(count($messages) == 0)
	{
		// We need a password.
		if(empty($options['password']))
		{
			$messages[] = l('Please enter a password.');
		}
		// On top of that, it needs to be right!
		elseif(sha1($user_salt. $options['password']) != $user_pass)
		{
			$messages[] = l('Incorrect user name or password.');
		}
		else
		{
			// They have successfully proved they are who they say they are
			// (unless they have a sucky password, of course ;-)).
			// Now update their salt and then update their IP, perhaps.
			$new_salt = sqlite_escape_string(randomString(9));
			$new_password = sqlite_escape_string(sha1($new_salt. $options['password']));
			$new_ip = sqlite_escape_string(user()->ip());

			$dbh->query("
				UPDATE users
				SET salt = '$new_salt', password = '$new_password'". (user()->ip() != $user_ip ? ', ip = \''. $new_ip. '\'' : ''). "
				WHERE id = $user_id");

			// Now, set that cookie!
			setcookie(LBCOOKIE, implode('|', array($user_id, $new_password)), (!empty($options['remember_me']) ? time() + 2592000 : 0));

			// Along with some basic session information.
			$_SESSION['user_id'] = $user_id;
			$_SESSION['user_pass'] = $new_password;

			// Alright, now it is time to take them, somewhere...
			if(!empty($options['redir_to']) && utf_substr($options['redir_to'], 0, 1) !== '/' && utf_strpos($options['redir_to'], '://') === false)
			{
				redirect(get_bloginfo('url'). $options['redir_to']);
			}
			else
			{
				redirect(get_bloginfo('url'). 'admin/dashboard.php');
			}

			$messages[] = l('An unknown error occurred.');
		}
	}

	return $messages;
}

/*
	Class: User

	Contains all the information about the currently logged in user (or guest).
*/
class User
{
	// Variable: id
	// The user's ID.
	private $id;

	// Variable: name
	// The user's name.
	private $name;

	// Variable: password
	// The user's hashed password.
	private $password;

	// Variable: email
	// The user's email address.
	private $email;

	// Variable: displayName
	// The user's display name.
	private $displayName;

	// Variable: role
	// The user's role (an integer).
	private $role;

	/*
		Constructor: __construct

		Checks to see if the current user is logged in and loads their
		information if they are.

		Parameters:
			none
	*/
	public function __construct()
	{
		global $dbh;

		// Set everything to 0 or null.
		$this->id = 0;
		$this->name = null;
		$this->password = null;
		$this->email = null;
		$this->displayName = null;
		$this->role = 0;

		// Maybe they're logged in?
		if(isset($_COOKIE[LBCOOKIE]) && utf_strpos($_COOKIE[LBCOOKIE], '|') !== false)
		{
			// We need to get their user ID and password.
			list($user_id, $user_pass) = explode('|', $_COOKIE[LBCOOKIE], 2);

			// Make sure their session data is theirs.
			if((!empty($_SESSION['user_id']) && $_SESSION['user_id'] != $user_id) || (!empty($_SESSION['user_pass']) && $_SESSION['user_pass'] != $user_pass))
			{
				// This isn't yours! You cannot have it :-P.
				$_SESSION = array();
			}

			// Make sure that their user ID and password have the possibility of
			// being valid.
			if((int)$user_id > 0 && utf_strlen($user_pass) == 40)
			{
				// Now we need to see if they can log in.
				$request = $dbh->query("
					SELECT
						id, username, password, email, displayname, role
					FROM users
					WHERE id = ". ((int)$user_id). " AND password = '". sqlite_escape_string($user_pass). "'
					LIMIT 1");

				// Did we find anything?
				if($request->numRows() > 0)
				{
					// We sure did!
					$row = $request->fetch(SQLITE_ASSOC);

					// Now set their information.
					$this->id = (int)$row['id'];
					$this->name = $row['username'];
					$this->password = $row['password'];
					$this->email = $row['email'];
					$this->displayName = $row['displayname'];
					$this->role = (int)$row['role'];

					$_SESSION['user_id'] = $this->id();
					$_SESSION['user_pass'] = $this->password();
				}
			}
		}

		// They showed signs of activity just now, they're not dead!
		$_SESSION['last_activity'] = time();
	}

	/*
		Function: id

		Returns the user's ID.

		Parameters:
			none

		Returns:
			int - Returns the user's ID (if they are a guest, it will be 0).
	*/
	public function id()
	{
		return $this->id;
	}

	/*
		Function: name

		Returns the user's name (the one used to log into their account).

		Parameters:
			none

		Returns:
			string - Returns their user name if they're logged in, but 'Guest' if
							 they are, take a guess, a guest.
	*/
	public function name()
	{
		return $this->is_logged() ? $this->name : l('Guest');
	}

	/*
		Function: password

		Returns the user's hashed password.

		Parameters:
			none

		Returns:
			string - Returns their hashed password if they're logged in, but null
							 if not.
	*/
	public function password()
	{
		return $this->is_logged() ? $this->password : null;
	}

	/*
		Function: email

		Returns the user's email address.

		Parameters:
			none

		Returns:
			string - Returns their email if they're logged in, but null if not.
	*/
	public function email()
	{
		return $this->is_logged() ? $this->email : null;
	}

	/*
		Function: displayName

		Returns the user's display name.

		Parameters:
			none

		Returns:
			string - Returns their display name if they're logged in, but 'Guest'
							 if they're a guest.
	*/
	public function displayName()
	{
		return $this->is_logged() ? $this->displayName : l('Guest');
	}

	/*
		Function: role

		Returns the user's role.

		Parameters:
			none

		Returns:
			int - Returns the user's role ID (0 if they're a guest).
	*/
	public function role()
	{
		return $this->role;
	}

	/*
		Function: is_logged

		Returns whether the user is logged in.

		Parameters:
			none

		Returns:
			bool - Returns true if they're logged in, false if not.
	*/
	public function is_logged()
	{
		return $this->id() > 0;
	}

	/*
		Function: is_guest

		Returns whether the user is a guest.

		Parameters:
			none

		Returns:
			bool - Returns true if they're a guest, false if not.
	*/
	public function is_guest()
	{
		return !$this->is_logged();
	}

	/*
		Function: ip

		Returns the user's IP.

		Parameters:
			none

		Returns:
			string - Returns their IP.
	*/
	public function ip()
	{
		return isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
	}

	/*
		Function: csrf_token

		Returns the user's current CSRF (Cross-Site Request Forgery) token.

		Parameters:
			bool $urlencode - Whether to URL encode the CSRF token. Defaults to
												false.

		Returns:
			string
	*/
	public function csrf_token($urlencode = false)
	{
		// Do they not have a token yet? Do we need to regenerate it?
		if(empty($_SESSION['csrf_token']) || empty($_SESSION['last_activity']) || ($_SESSION['last_activity'] + 86400) < time())
		{
			$_SESSION['csrf_token'] = randomString(mt_rand(40, 60));
		}

		return !empty($urlencode) ? urlencode($_SESSION['csrf_token']) : $_SESSION['csrf_token'];
	}
}

/*
	Function: user

	This function will return the current <User> object.

	Parameters:
		none

	Returns:
		object
*/
function user()
{
	if(!isset($GLOBALS['user']) || !is_object($GLOBALS['user']))
	{
		$GLOBALS['user'] = new User();
	}

	return $GLOBALS['user'];
}
?>