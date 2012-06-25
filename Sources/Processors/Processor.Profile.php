<?php
/*********************************************

	LightBlog 0.9
	SQLite blogging platform

	Sources/Processors/Processor.Profile.php

	©2008-2012 The LightBlog Team. All
	rights reserved. Released under the
	GNU General Public License 3. For
	all licensing information, please
	see the LICENSE.txt document
	included in this distribution.

*********************************************/

class Profile
{
	private $dbh;

	public function __construct()
	{
		$this->dbh = $GLOBALS['dbh'];
	}

	public function processor($data)
	{
		// Later on, we will send this as JSON to the browser
		$response = array(
			'result' => 'error',
			'response' => null
		);
	
		// Can the user do this?
		if(permissions('EditOtherUsers') || (int)$data['uid'] == user()->id())
		{
			// Get current user data from the database
			$user_query = @$this->dbh->query("SELECT user_pass,user_email,display_name,user_role,user_salt FROM users WHERE user_id=".(int)$data['uid']);
			if(!$user_query)
			{
				$response['response'] = array("result" => "error", "response" => "couldn't read from the database.");
				return $response;
			}
			while($row = $user_query->fetchObject())
			{
				// Make variables from the database query
				$cpassword_db = $row->user_pass;
				$email = $row->user_email;
				$displayname = $row->display_name;
				$role = $row->user_role;
				$csalt = $row->user_salt;
			}
			// Check if the current password given in the form matches the actual current password
			if(sha1($csalt . $data['cpassword']) == $cpassword_db)
			{
				// Make an array for the queries
				$query = array();
				// Are we changing a password? Make sure both fields are filled so we don't accidentally change it!
				if($data['password'] != '' && $data['vpassword'] != '')
				{
					// Do both password fields match?
					if($data['password'] === $data['vpassword'])
					{
						// Make a new salt
						$salt = randomString(9);
						// Make a new password hash
						$password = sha1($salt . $data['password']);
						// Add it to the query
						array_push($query, "SET user_pass='".sqlite_escape_string($password)."'", "SET user_salt='".sqlite_escape_string($salt)."'");
					}
					else
					{
						$response['response'] = "new passwords don't match.";
					}
				}
				// Are we changing an email address?
				if($data['email'] != $email)
				{
					// Yup. Add it to the query
					array_push($query, "SET user_email='".sqlite_escape_string($data['email'])."'");
				}
				// Are we changing a display name?
				if($data['displayname'] != $displayname)
				{
					// Yup. Add it to the query
					array_push($query, "SET display_name='".sqlite_escape_string($data['displayname'])."'");
				}
				// Are we changing a role?
				if($data['role'] != $role && permissions('EditOtherUsers'))
				{
					// Yup. Add it to the query
					array_push($query, "SET user_role='".sqlite_escape_string($data['role'])."'");
				}
				if(!is_null($response['response']))
				{
					return $response;
				}
				// Go, query, go!
				$update_query = @$this->dbh->query("UPDATE users ".implode(',', $query)." WHERE user_id=".(int)$data['uid']);
				// Did it work?
				if(!$update_query)
				{
					$response['response'] = "couldn't save data to the database.";
				}
				else
				{
					// Yes!
					$response['result'] = "success";
					$response['response'] = "profile edited.";
				}
			}
			else
			{
				$response['response'] = "current password incorrect.";
			}
		}
		else
		{
			$response['response'] = "not allowed to edit this profile.";
		}
		// Return the news, whether it's good or bad
		return $response;
	}
}

?>