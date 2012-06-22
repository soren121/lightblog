<?php
/*********************************************

	LightBlog 0.9
	SQLite blogging platform

	Sources/ProcessAJAX.php

	©2008-2012 The LightBlog Team. All
	rights reserved. Released under the
	GNU General Public License 3. For
	all licensing information, please
	see the LICENSE.txt document
	included in this distribution.

*********************************************/

// Require config file
require('Core.php');

header('Content-Type: text/json; charset=utf-8');

// Process post/page/category creation
if(isset($_POST['create']))
{
	if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== user()->csrf_token())
	{
		die(json_encode(array("result" => "error", "response" => "CSRF token incorrect or missing.")));
	}
	else
	{
		$type = $_POST['type'];
		if($type != 'category' && permissions(1) || $type == 'category' && permissions(2))
		{
			// Has anything been submitted?
			if(empty($_POST['title']) || empty($_POST['text']))
			{
				die(json_encode(array("result" => "error", "response" => "you must give your ".$type." a title and content.")));
			}

			// Require the HTML filter class
			require('Class.htmLawed.php');

			// Grab the data from form and clean things up
			$title = utf_htmlspecialchars($_POST['title']);
			$text = htmLawed::hl($_POST['text'], array('safe' => 1, 'make_tag_strict' => 1, 'balance' => 1, 'keep_bad' => 3));

			// If you're not a category, you must be...
			if($type !== 'category')
			{
				$date = time();
				$author = user()->displayName();
				if($type == 'post')
				{
					$category = (int)$_POST['category'];
				}

				// Check published checkbox
				if(isset($_POST['published']) && $_POST['published'] == 1)
				{
					$published = 1;
				}
				else
				{
					$published = 0;
				}

				// Check comments checkbox
				if(isset($_POST['comments']) && $_POST['comments'] == 1)
				{
					$comments = 1;
				}
				else
				{
					$comments = 0;
				}

				// Insert post/page into database
				if($type == 'post')
				{
					@$dbh->query("INSERT INTO posts (title,post,date,author,published,category,comments) VALUES('".sqlite_escape_string($title)."','".sqlite_escape_string($text)."',$date,'".sqlite_escape_string($author)."',$published,$category,$comments)") or die(json_encode(array("result" => "error", "response" => sqlite_error_string($dbh->lastError()))));
				}
				else
				{
					@$dbh->query("INSERT INTO pages (title,page,date,author,published) VALUES('".sqlite_escape_string($title)."','".sqlite_escape_string($text)."',$date,'".sqlite_escape_string($author)."',$published)") or die(json_encode(array("result" => "error", "response" => sqlite_error_string($dbh->lastError()))));
				}

				// Show 'view' link
				$showlink = true;
			}
			// ...a category.
			else
			{
				$shortname = utf_substr(preg_replace("/[^a-zA-Z0-9\s]/", "", utf_strtolower($title)), 0, 15);
				@$dbh->query("INSERT INTO categories (shortname,fullname,info) VALUES('".sqlite_escape_string($shortname)."','".sqlite_escape_string($title)."','".sqlite_escape_string($text)."')") or die(json_encode(array("result" => "error", "response" => sqlite_error_string($dbh->lastError()))));

				// Do not show 'view' link
				$showlink = false;
			}

			// Fetch post ID from database
			$id = $dbh->lastInsertRowid();

			// Create URL to return to jQuery
			$url = get_bloginfo('url')."?".$type."=".$id;

			// Return JSON-encoded response
			die(json_encode(array("result" => "success", "response" => "$url", "showlink" => "$showlink")));
		}
	}
}

# Process post/page editing
if(isset($_POST['edit']))
{
	if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== user()->csrf_token())
	{
		die(json_encode(array("result" => "error", "response" => "CSRF token incorrect or missing.")));
	}
	else
	{
		$id = (int)$_POST['id'];
		$type = sqlite_escape_string($_POST['type']);
		$query = @$dbh->query("SELECT author FROM posts WHERE id=".$id) or die(json_encode(array("result" => "error", "response" => sqlite_error_string($dbh->lastError()))));

		if($type !== 'category' &&  permissions(2) || $type !== 'category' &&  permissions(1) && $query->fetchSingle() === user()->displayName() || $type === 'category' && permissions(2))
		{
			// Require the HTML filter class
			require('Class.htmLawed.php');

			// Grab the data from form and escape the text
			$title = utf_htmlspecialchars($_POST['title']);
			$text = htmLawed::hl($_POST['text'], array('safe' => 1, 'make_tag_strict' => 1, 'balance' => 1, 'keep_bad' => 3));

			// Check published checkbox
			if(isset($_POST['published']) && $_POST['published'] == 1)
			{
				$published = 1;
			}
			else
			{
				$published = 0;
			}

			// For posts only
			if($type == 'post')
			{
				// Check comments checkbox
				if(isset($_POST['comments']) && $_POST['comments'] == 1)
				{
					$comments = 1;
				}
				else
				{
					$comments = 0;
				}

				// Check category
				$category = (int)$_POST['category'];
			}
			elseif($type == 'category')
			{
				$shortname = utf_substr(preg_replace("/[^a-zA-Z0-9]/", "", strtolower($title)), 0, 15);
			}
			// Query for previous data
			$result = @$dbh->query("SELECT * FROM ".($type == 'category' ? 'categorie' : $type)."s WHERE id=".$id) or die(json_encode(array("result" => "error", "response" => sqlite_error_string($dbh->lastError()))));

			// Fetch previous data
			while($past = $result->fetchObject())
			{
				if($type == 'post')
				{
					$ptitle = $past->title;
					$ppublished = $past->published;
					$ptext = $past->post;
					$pcategory = $past->category;
					$pcomments = $past->comments;
				}
				elseif($type == 'page')
				{
					$ptitle = $past->title;
					$ppublished = $past->published;
					$ptext = $past->page;
				}
				elseif($type == 'category')
				{
					$ptitle = $past->fullname;
					$ptext = $past->info;
					$pshortname = $past->shortname;
				}
			}

			// Set a base query to modify
			$base = "UPDATE ".($type == 'category' ? 'categorie' : $type)."s SET ";

			// Create URL to return to jQuery
			$url = get_bloginfo('url')."?".$type."=".$id;

			// Run through scenarios
			if($type != 'category')
			{
				if($ptitle !== $title)
				{
					$base .= "title='".sqlite_escape_string($title)."', ";
				}

				if($ptext !== $text)
				{
					$base .= $type."='".sqlite_escape_string($text)."', ";
				}

				if((int)$ppublished !== $published)
				{
					$base .= "published='".(int)$published."', ";
				}

				if($type == 'post')
				{
					if((int)$pcategory !== $category)
					{
						$base .= "category='".(int)$category."', ";
					}

					if((int)$pcomments !== $comments)
					{
						$base .= "comments='".(int)$comments."', ";
					}
				}

				// Show 'view' link
				$showlink = true;
			}
			else
			{
				if($ptitle !== $title)
				{
					$base .= "fullname='".sqlite_escape_string($title)."', ";
				}

				if($pshortname !== $shortname)
				{
					$base .= "shortname='".sqlite_escape_string($shortname)."', ";
				}

				if($ptext !== $text)
				{
					$base .= "info='".sqlite_escape_string($text)."', ";
				}

				// Don't show 'view' link
				$showlink = false;
			}

			// If nothing's changed, then we don't need to do anything
			if($base == "UPDATE ".($type == 'category' ? 'categorie' : $type)."s SET ")
			{
				echo json_encode(array("result" => "success", "response" => "$url", "showlink" => "$showlink"));
				die();
			}

			// Remove last comma & space
			$base = substr($base, 0, -2);
			$base .= " WHERE id=".(int)$id;

			// Execute modified query
			@$dbh->query($base) or die(json_encode(array("result" => "error", "response" => $type.sqlite_error_string($dbh->lastError()))));

			// Return JSON-encoded response
			die(json_encode(array("result" => "success", "response" => "$url", "showlink" => "$showlink")));
		}
	}
}

// Process post/page/category deletion
if(isset($_POST['delete']) && $_POST['delete'] == 'true')
{
	if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== user()->csrf_token())
	{
		die(json_encode(array("result" => "error", "response" => "CSRF token incorrect or missing.")));
	}
	else
	{
		// Execute query to delete post/page/category
		@$dbh->query("DELETE FROM ".sqlite_escape_string(strip_tags($_POST['type']))." WHERE id=".(int)$_POST['id']) or die(json_encode(array("result" => "error", "response" => sqlite_error_string($dbh->lastError()))));

		if($_POST['type'] == 'posts')
		{
			// Delete comments associated with this post
			@$dbh->query("DELETE FROM comments WHERE pid=".(int)$_POST['id']) or die(json_encode(array("result" => "error", "response" => sqlite_error_string($dbh->lastError()))));
		}

		die(json_encode(array("result" => "success")));
	}
}

if(isset($_POST['bulk']))
{
	if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== user()->csrf_token())
	{
		die(json_encode(array("result" => "error", "response" => "CSRF token incorrect or missing.")));
	}
	elseif($_POST['checked'] != '' && permissions(2))
	{
		if($_POST['action'] == 'delete')
		{
			@$dbh->query("DELETE FROM ".sqlite_escape_string(strip_tags($_POST['type']))." WHERE id IN (".sqlite_escape_string(implode(',', $_POST['checked'])).")") or die(json_encode(array("result" => "error", "response" => sqlite_error_string($dbh->lastError()))));
		}
		elseif($_POST['action'] == 'publish')
		{
			@$dbh->query("UPDATE ".sqlite_escape_string(strip_tags($_POST['type']))." SET published=1 WHERE id IN (".sqlite_escape_string(implode(',', $_POST['checked'])).")") or die(json_encode(array("result" => "error", "response" => sqlite_error_string($dbh->lastError()))));
		}
		elseif($_POST['action'] == 'unpublish')
		{
			@$dbh->query("UPDATE ".sqlite_escape_string(strip_tags($_POST['type']))." SET published=0 WHERE id IN (".sqlite_escape_string(implode(',', $_POST['checked'])).")") or die(json_encode(array("result" => "error", "response" => sqlite_error_string($dbh->lastError()))));
		}
		die(json_encode(array("result" => "success")));
	}
}

// Process theme change
if(isset($_POST['themesubmit']))
{
	if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== user()->csrf_token())
	{
		die(json_encode(array("result" => "error", "response" => "CSRF token incorrect or missing.")));
	}
	else
	{
		if(permissions(3))
		{
			// Execute query to change theme
			@$dbh->query("UPDATE core SET value='".sqlite_escape_string(strip_tags($_POST['changetheme']))."' WHERE variable='theme'") or die(json_encode(array("result" => "error", "response" => sqlite_error_string($dbh->lastError()))));

			// Return result
			die(json_encode(array("result" => "success")));
		}
	}
}

// User creation
if(isset($_POST['addusersubmit']))
{
	// Later on, we will send this as JSON to the browser.
	$response = array(
		'result' => 'error',
		'response' => null
	);

	// We should verify that the user is actually submitting the request
	// themselves, or at least to our best abilities.
	if(empty($_POST['csrf_token']) || $_POST['csrf_token'] != user()->csrf_token())
	{
		$response['response'] = 'CSRF token incorrect or missing';
	}
	// They need to have permission too.
	elseif(permissions(3))
	{
		$options = array();

		// Make sure they gave us a user name.
		if(empty($_POST['username']) || utf_strlen(trim($_POST['username'])) == 0)
		{
			$response['response'] = 'Please enter a user name.';
		}
		// Make sure that the user name isn't in use.
		elseif(!user_name_allowed($_POST['username']))
		{
			// Uh oh! That's no good.
			$response['response'] = 'That user name is already in use.';
		}
		else
		{
			$options['username'] = utf_htmlspecialchars(trim($_POST['username']));
		}

		// How about their password?
		if($response['response'] === null && (empty($_POST['password']) || utf_strlen($_POST['password']) < 6))
		{
			$response['response'] = 'The password must be at least 6 characters.';
		}
		elseif($response['response'] === null)
		{
			$options['password'] = $_POST['password'];
		}

		// Make sure they verified the password (no typo!).
		if($response['response'] === null && isset($options['password']) && (empty($_POST['vpassword']) || $_POST['vpassword'] != $options['password']))
		{
			$response['response'] = 'The passwords do not match.';
		}

		// Now, the email address!
		if($response['response'] === null && empty($_POST['email']))
		{
			$response['response'] = 'Please enter an email address.';
		}
		elseif($response['response'] === null && !user_email_allowed($_POST['email']))
		{
			$response['response'] = 'That email address is already in use.';
		}
		elseif($response['response'] === null)
		{
			$options['email'] = utf_htmlspecialchars($_POST['email']);
		}

		// Now for their display name... That is, if it's set (if it's not, we
		// will use their username.
		$_POST['displayname'] = !empty($_POST['displayname']) && utf_strlen(trim($_POST['displayname'])) > 0 ? trim($_POST['displayname']) : (isset($_POST['username']) ? trim($_POST['username']) : '');
		if($response['response'] === null && utf_strlen($_POST['displayname']) == 0)
		{
			$response['response'] = 'Please enter a display name.';
		}
		elseif($response['response'] === null && !user_name_allowed($_POST['displayname']))
		{
			$response['response'] = 'That display name is already in use.';
		}
		elseif($response['response'] === null)
		{
			$options['displayname'] = utf_htmlspecialchars($_POST['displayname']);
		}

		// Then their role.
		if($response['response'] === null && (empty($_POST['role']) || !in_array((int)$_POST['role'], array(1, 2, 3), true)))
		{
			$response['response'] = 'Please select a valid role.';
		}
		elseif($response['response'] === null)
		{
			$options['role'] = (int)$_POST['role'];
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
			$dbh->query("
				INSERT INTO users
				(username, password, email, displayname, role, ip, salt)
				VALUES('{$options['username']}', '{$options['password']}', '{$options['email']}', '{$options['displayname']}', '{$options['role']}', '{$options['ip']}', '{$options['salt']}')");

			// Did we create it?
			if($dbh->changes() > 0)
			{
				// Yes!
				$response['result'] = 'success';
				$response['response'] = 'User '. utf_htmlspecialchars(trim($_POST['username'])). ' created.';
			}
			else
			{
				$response['response'] = sqlite_error_string($dbh->lastError());
			}
		}
	}
	else
	{
		$response['response'] = 'You&#039;re not allowed to add users.';
	}

	echo json_encode($response);
	exit;
}

if(isset($_POST['editprofile']))
{
	// Later on, we will send this as JSON to the browser
	$response = array(
		'result' => 'error',
		'response' => null
	);

	if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== user()->csrf_token())
	{
		$response['response'] = "CSRF token incorrect or missing.";
	}
	else
	{
		// Can the user do this?
		if(permissions(3) || (int)$_POST['uid'] == user()->id())
		{
			// Get current user data from the database
			$user_query = @$dbh->query("SELECT password,email,displayname,role,salt FROM users WHERE id=".(int)$_POST['uid']) or die(json_encode(array("result" => "error", "response" => "couldn't read from the database.")));
			while($row = $user_query->fetchObject())
			{
				// Make variables from the database query
				$cpassword_db = $row->password;
				$email = $row->email;
				$displayname = $row->displayname;
				$role = $row->role;
				$csalt = $row->salt;
			}
			// Check if the current password given in the form matches the actual current password
			if(sha1($csalt . $_POST['cpassword']) == $cpassword_db)
			{
				// Make an array for the queries
				$query = array();
				// Are we changing a password? Make sure both fields are filled so we don't accidentally change it!
				if($_POST['password'] != '' && $_POST['vpassword'] != '')
				{
					// Do both password fields match?
					if($_POST['password'] === $_POST['vpassword'])
					{
						// Make a new salt
						$salt = randomString(9);
						// Make a new password hash
						$password = sha1($salt . $_POST['password']);
						// Add it to the query
						array_push($query, "SET password='".sqlite_escape_string($password)."'", "SET salt='".sqlite_escape_string($salt)."'");
					}
					else
					{
						$response['response'] = "new passwords don't match.";
					}
				}
				// Are we changing an email address?
				if($_POST['email'] != $email)
				{
					// Yup. Add it to the query
					array_push($query, "SET email='".sqlite_escape_string($_POST['email'])."'");
				}
				// Are we changing a display name?
				if($_POST['displayname'] != $displayname)
				{
					// Yup. Add it to the query
					array_push($query, "SET displayname='".sqlite_escape_string($_POST['displayname'])."'");
				}
				// Are we changing a role? And is the logged-in user an admin? Because you can't change roles otherwise.
				if($_POST['role'] != $role && permissions(3))
				{
					// Yup. Add it to the query
					array_push($query, "SET role='".sqlite_escape_string($_POST['role'])."'");
				}
				// Go, query, go!
				@$dbh->query("UPDATE users ".implode(',', $query)." WHERE id=".(int)$_POST['uid']);
				// Did it work?
				if($dbh->changes() > 0)
				{
					// Yes!
					$response['result'] = "success";
					$response['response'] = "profile edited.";
				}
				else
				{
					$response['response'] = "couldn't save data to the database.";
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
	}
	// Return the news to jQuery, whether it's good or bad
	die(json_encode($response));
}

if(isset($_POST['deleteusersubmit']))
{
	if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== user()->csrf_token())
	{
		die(json_encode(array("result" => "error", "response" => "CSRF token incorrect or missing.")));
	}
	else
	{
		// Can the user do this?
		if(permissions(2))
		{
			// Check the user level of the user being deleted
			$query = @$dbh->query("SELECT role FROM users WHERE id=".(int)$_POST['id']) or die(json_encode(array("result" => "error", "response" => "could not get the role of the user being deleted.")));

			if(user()->role() >= $query->fetchSingle())
			{
				// Execute query to delete user
				@$dbh->query("DELETE FROM users WHERE id=".(int)$_POST['id']) or die(json_encode(array("result" => "error", "response" => "user deletion command failed.")));

				die(json_encode(array("result" => "success")));
			}
		}
	}
}

// Process comment approval
if(isset($_POST['approvecomment']))
{
	if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== user()->csrf_token())
	{
		die(json_encode(array("result" => "error", "response" => "CSRF token incorrect or missing.")));
	}
	else
	{
		if(permissions(2))
		{
			// Execute query to approve comment
			@$dbh->query("UPDATE comments SET published='1' WHERE id=".(int)$_POST['id']) or die(json_encode(array("result" => "error", "response" => "comment approval command failed.")));

			die(json_encode(array("result" => "success")));
		}
	}
}
?>