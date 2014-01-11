<?php
/*********************************************

	LightBlog 0.9
	SQLite blogging platform

	Sources/Processors/Processor.AddUser.php

	©2008-2012 The LightBlog Team. All
	rights reserved. Released under the
	GNU General Public License 3. For
	all licensing information, please
	see the LICENSE.txt document
	included in this distribution.

*********************************************/

class AddUser
{
	private $dbh;

	public function __construct()
	{
		$this->dbh = $GLOBALS['dbh'];
	}

	public function processor($data)
	{
		// Later on, we will send this as JSON to the browser.
		$response = array(
			'result' => 'error',
			'response' => null
		);
	
		// They need to have permission too.
		if(permissions('AddUsers'))
		{
			$options = array();
	
			// Make sure they gave us a user name.
			if(empty($data['username']) || utf_strlen(trim($data['username'])) == 0)
			{
				$response['response'] = 'Please enter a user name.';
			}
			// Make sure that the user name isn't in use.
			elseif(!user_name_allowed($data['username']))
			{
				// Uh oh! That's no good.
				$response['response'] = 'That user name is already in use.';
			}
			else
			{
				$options['username'] = utf_htmlspecialchars(trim($data['username']));
			}
	
			// How about their password?
			if($response['response'] === null && (empty($data['password']) || utf_strlen($data['password']) < 6))
			{
				$response['response'] = 'The password must be at least 6 characters.';
			}
			elseif($response['response'] === null)
			{
				$options['password'] = $data['password'];
			}
	
			// Make sure they verified the password (no typo!).
			if($response['response'] === null && isset($options['password']) && (empty($data['vpassword']) || $data['vpassword'] != $options['password']))
			{
				$response['response'] = 'The passwords do not match.';
			}
	
			// Now, the email address!
			if($response['response'] === null && empty($data['email']))
			{
				$response['response'] = 'Please enter an email address.';
			}
			//elseif($response['response'] === null && !user_email_allowed($data['email']))
			//{
			//	$response['response'] = 'That email address is already in use.';
			//}
			elseif($response['response'] === null)
			{
				$options['email'] = utf_htmlspecialchars($data['email']);
			}
	
			// Now for their display name... That is, if it's set (if it's not, we
			// will use their username.
			$data['displayname'] = !empty($data['displayname']) && utf_strlen(trim($data['displayname'])) > 0 ? trim($data['displayname']) : (isset($data['username']) ? trim($data['username']) : '');
			if($response['response'] === null && utf_strlen($data['displayname']) == 0)
			{
				$response['response'] = 'Please enter a display name.';
			}
			elseif($response['response'] === null && !user_name_allowed($data['displayname']))
			{
				$response['response'] = 'That display name is already in use.';
			}
			elseif($response['response'] === null)
			{
				$options['displayname'] = utf_htmlspecialchars($data['displayname']);
			}
	
			// Then their role.
			if($response['response'] === null && (empty($data['role']) || !in_array((int)$data['role'], array(1, 2, 3), true)))
			{
				$response['response'] = 'Please select a valid role.';
			}
			elseif($response['response'] === null)
			{
				$options['role'] = (int)$data['role'];
			}
	
			// Is everything okay? May we create the user now?
			if($response['response'] === null)
			{
				// We need to create a salt.
				$options['salt'] = randomString(9);
	
				// Now hash their password with the salt.
				$options['password'] = sha1($options['salt']. $options['password']);
	
				// Then their IP address.
				$options['ip'] = user()->ip();
	
				// Then sanitize everything.
				foreach($options as $key => $value)
				{
					$options[$key] = sqlite_escape_string($value);
				}
	
				// Now insert the user.
				$sql_adduser = $this->dbh->exec("
					INSERT INTO 
						users
							(user_name, 
							user_pass, 
							user_email, 
							display_name, 
							user_role, 
							user_ip, 
							user_salt, 
							user_activated, 
							user_created) 
					VALUES(
						'{$options['username']}', 
						'{$options['password']}', 
						'{$options['email']}', 
						'{$options['displayname']}', 
						'{$options['role']}', 
						'{$options['ip']}', 
						'{$options['salt']}, 
						1, 
						'".time()."'
					)
				");
	
				// Did we create it?
				if($sql_adduser > 0)
				{
					// Yes!
					$response['result'] = 'success';
					$response['response'] = 'User '. utf_htmlspecialchars(trim($data['username'])). ' created.';
				}
				else
				{
					$response['response'] = sqlite_error_string($this->dbh->lastError());
				}
			}
		}
		else
		{
			$response['response'] = 'You&#039;re not allowed to add users.';
		}
	
		return $response;
	}
}

?>