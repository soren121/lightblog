<?php session_start();

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
require('../config.php');
require(ABSPATH .'/Sources/Core.php');
require(ABSPATH .'/Sources/FunctionReplacements.php');

header('Content-Type: text/json; charset=utf-8');

# Process post/page/category creation
if(isset($_POST['create'])) {
	if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
		die(json_encode(array("result" => "error", "response" => "CSRF token incorrect or missing.")));
	}
	else {
		$type = $_POST['type'];
		if(($type !== 'category' && permissions(1)) || ($type === 'category' && permissions(2))) {
			# Has anything been submitted?
			if(empty($_POST['title']) || empty($_POST['text'])) {
				die(json_encode(array("result" => "error", "response" => "you must give your ".$type." a title and content.")));
			}
			# Require the HTML filter class
			require('Class.htmLawed.php');
			# Grab the data from form and clean things up
			$title = strip_tags($_POST['title']);
			$text = htmLawed::hl($_POST['text'], array('safe' => 1, 'make_tag_strict' => 1, 'balance' => 1, 'keep_bad' => 3));
			# If you're not a category, you must be...
			if($type !== 'category') {
				$date = time();
				$author = userFetch('displayname', 'r');
				if($type == 'post') {
					$category = (int)$_POST['category'];
				}
				# Check published checkbox
				if(isset($_POST['published']) && $_POST['published'] == 1) { $published = 1; }
				else { $published = 0; }
				# Check comments checkbox
				if(isset($_POST['comments']) && $_POST['comments'] == 1) { $comments = 1; }
				else { $comments = 0; }
				# Insert post/page into database
				if($type == 'post') {
					@$dbh->query("INSERT INTO posts (title,post,date,author,published,category,comments) VALUES('".sqlite_escape_string($title)."','".sqlite_escape_string($text)."',$date,'".sqlite_escape_string($author)."',$published,$category,$comments)") or die(json_encode(array("result" => "error", "response" => sqlite_error_string($dbh->lastError()))));
				}
				else {
					@$dbh->query("INSERT INTO pages (title,page,date,author,published) VALUES('".sqlite_escape_string($title)."','".sqlite_escape_string($text)."',$date,'".sqlite_escape_string($author)."',$published)") or die(json_encode(array("result" => "error", "response" => sqlite_error_string($dbh->lastError()))));
				}
				# Show 'view' link
				$showlink = true;
			}
			# ...a category.
			else {
				$shortname = substr(preg_replace("/[^a-zA-Z0-9\s]/", "", strtolower($title)), 0, 15);
				@$dbh->query("INSERT INTO categories (shortname,fullname,info) VALUES('".sqlite_escape_string($shortname)."','".sqlite_escape_string($title)."','".sqlite_escape_string($text)."')") or die(json_encode(array("result" => "error", "response" => sqlite_error_string($dbh->lastError()))));
				# Do not show 'view' link
				$showlink = false;
			}
			# Fetch post ID from database
			$id = $dbh->lastInsertRowid();
			# Create URL to return to jQuery
			$url = get_bloginfo('url')."?".$type."=".$id;
			# Return JSON-encoded response
			echo json_encode(array("result" => "success", "response" => "$url", "showlink" => "$showlink"));
			# Prevent the rest of the page from loading
			die();
		}
	}
}

# Process post/page editing
if(isset($_POST['edit'])) {
	if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
		die(json_encode(array("result" => "error", "response" => "CSRF token incorrect or missing.")));
	}
	else {
		$id = (int)$_POST['id'];
		$type = sqlite_escape_string($_POST['type']);
		$query = @$dbh->query("SELECT author FROM posts WHERE id=".$id) or die(json_encode(array("result" => "error", "response" => sqlite_error_string($dbh->lastError()))));
		if($type !== 'category' &&  permissions(2) || $type !== 'category' &&  permissions(1) && $query->fetchSingle() === userFetch('displayname','r') || $type === 'category' && permissions(2)) {
			# Require the HTML filter class
			require('Class.htmLawed.php');
			# Grab the data from form and escape the text
			$title = strip_tags($_POST['title']);
			$text = htmLawed::hl($_POST['text'], array('safe' => 1, 'make_tag_strict' => 1, 'balance' => 1, 'keep_bad' => 3));
			# Check published checkbox
			if(isset($_POST['published']) && $_POST['published'] == 1) { $published = 1; }
			else { $published = 0; }
			# For posts only
			if($type == 'post') {
				# Check comments checkbox
				if(isset($_POST['comments']) && $_POST['comments'] == 1) { $comments = 1; }
				else { $comments = 0; }
				# Check category
				$category = (int)$_POST['category'];
			}
			elseif($type == 'category') {
				$shortname = substr(preg_replace("/[^a-zA-Z0-9]/", "", strtolower($title)), 0, 15);
			}
			# Query for previous data
			$result = @$dbh->query("SELECT * FROM ".($type == 'category' ? 'categorie' : $type)."s WHERE id=".$id) or die(json_encode(array("result" => "error", "response" => sqlite_error_string($dbh->lastError()))));
			# Fetch previous data
			while($past = $result->fetchObject()) {
				if($type == 'post') {
					$ptitle = $past->title;
					$ppublished = $past->published;
					$ptext = $past->post;
					$pcategory = $past->category;
					$pcomments = $past->comments;
				}
				elseif($type == 'page') {
					$ptitle = $past->title;
					$ppublished = $past->published;
					$ptext = $past->page;
				}
				elseif($type == 'category') {
					$ptitle = $past->fullname;
					$ptext = $past->info;
					$pshortname = $past->shortname;
				}
			}
			# Set a base query to modify
			$base = "UPDATE ".($type == 'category' ? 'categorie' : $type)."s SET ";
			# Create URL to return to jQuery
			$url = get_bloginfo('url')."?".$type."=".$id;
			# Run through scenarios
			if($type != 'category') {
				if(stripslashes($ptitle) !== $title) { $base .= "title='".sqlite_escape_string($title)."', "; }
				if(stripslashes($ptext) !== $text) { $base .= $type."='".sqlite_escape_string($text)."', "; }
				if((int)$ppublished !== $published) { $base .= "published='".(int)$published."', "; }
				if($type == 'post') {
					if((int)$pcategory !== $category) { $base .= "category='".(int)$category."', "; }
					if((int)$pcomments !== $comments) { $base .= "comments='".(int)$comments."', "; }
				}
				# Show 'view' link
				$showlink = true;
			}
			else {
				if(stripslashes($ptitle) !== $title) { $base .= "fullname='".sqlite_escape_string($title)."', "; }
				if(stripslashes($pshortname) !== $shortname) { $base .= "shortname='".sqlite_escape_string($shortname)."', "; }
				if(stripslashes($ptext) !== $text) { $base .= "info='".sqlite_escape_string($text)."', "; }
				# Don't show 'view' link
				$showlink = false;
			}
			# If nothing's changed, then we don't need to do anything
			if($base == "UPDATE ".($type == 'category' ? 'categorie' : $type)."s SET ") {
				echo json_encode(array("result" => "success", "response" => "$url", "showlink" => "$showlink"));
				die();
			}
			# Remove last comma & space
			$base = substr($base, 0, -2);
			$base .= " WHERE id=".(int)$id;
			# Execute modified query
			@$dbh->query($base) or die(json_encode(array("result" => "error", "response" => $type.sqlite_error_string($dbh->lastError()))));
			# Return JSON-encoded response
			echo json_encode(array("result" => "success", "response" => "$url", "showlink" => "$showlink"));
			# Prevent the rest of the page from loading
			die();
		}
	}
}

# Process post/page/category deletion
if(isset($_POST['delete']) && $_POST['delete'] == 'true') {
	if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
		die(json_encode(array("result" => "error", "response" => "CSRF token incorrect or missing.")));
	}
	else {
		# Execute query to delete post/page/category
		@$dbh->query("DELETE FROM ".sqlite_escape_string(strip_tags($_POST['type']))." WHERE id=".(int)$_POST['id']) or die(json_encode(array("result" => "error", "response" => sqlite_error_string($dbh->lastError()))));
		if($_POST['type'] == 'posts') {
			# Delete comments associated with this post
			@$dbh->query("DELETE FROM comments WHERE pid=".(int)$_POST['id']) or die(json_encode(array("result" => "error", "response" => sqlite_error_string($dbh->lastError()))));
		}
		die(json_encode(array("result" => "success")));
	}
}

# Process theme change
if(isset($_POST['themesubmit'])) {
	if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
		die(json_encode(array("result" => "error", "response" => "CSRF token incorrect or missing.")));
	}
	else {
		if(permissions(3)) {
			# Execute query to change theme
			@$dbh->query("UPDATE core SET value='".sqlite_escape_string(strip_tags($_POST['changetheme']))."' WHERE variable='theme'") or die(json_encode(array("result" => "error", "response" => sqlite_error_string($dbh->lastError()))));
			# Return result
			die(json_encode(array("result" => "success")));
		}
	}
}

if(isset($_POST['changesettings'])) {
	if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
		json_encode(array("result" => "error", "response" => "CSRF token incorrect or missing."));
	}
	else {
		if($_POST['changetitle'] != get_bloginfo('title')) {
			@$dbh->query("UPDATE core SET value='".sqlite_escape_string(strip_tags($_POST['changetitle']))."' WHERE variable='title'") or die(json_encode(array("result" => "error", "response" => "Failed to change blog title.")));
		}
		if($_POST['changeurl'] != get_bloginfo('url')) {
			@$dbh->query("UPDATE core SET value='".sqlite_escape_string(strip_tags($_POST['changeurl']))."' WHERE variable='url'") or die(json_encode(array("result" => "error", "response" => "Failed to change blog URL.")));
		}
		if($_POST['commentmoderation'] != bloginfo('comment_moderation', 'r')) {
			@$dbh->query("UPDATE core SET value='".sqlite_escape_string(strip_tags($_POST['commentmoderation']))."' WHERE variable='comment_moderation'") or die(json_encode(array("result" => "error", "response" => "Failed to change blog URL.")));
		}
		die(json_encode(array("result" => "success")));
	}
}

// User creation
if(isset($_POST['addusersubmit'])) {
	if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
		die(json_encode(array("result" => "error", "response" => "CSRF token incorrect or missing.")));
	}
	else {
		// Can the user do this?
		if(permissions(3)) {
			// Set
			if(!isset($_POST['username'])) { $fielderror = true; }
			else { $username = sqlite_escape_string($_POST['username']); }
			if(!isset($_POST['password'])) { $fielderror = true; }
			else { $password = $_POST['password']; }
			if(!isset($_POST['vpassword'])) { $fielderror = true; }
			else { $vpassword = $_POST['vpassword']; }
			if(!isset($_POST['email'])) { $fielderror = true; }
			else { $email = $_POST['email']; }
			if(!isset($_POST['displayname'])) { $fielderror = true; }
			else { $displayname = $_POST['displayname']; }
			if(!isset($_POST['role'])) { $fielderror = true; }
			else { $role = $_POST['role']; }
			// Output error if required
			if(isset($fielderror) && $fielderror == true) {
				die(json_encode(array("result" => "error", "response" => "All fields must be filled. Try again.")));
			}
			// Does that username exist already?
			$result = @$dbh->query("SELECT * FROM users WHERE username='$username'") or die(json_encode(array("result" => "error", "response" => sqlite_error_string($dbh->lastError()))));
			if($result->numRows() < 0) {
				die(json_encode(array("result" => "error", "response" => "Username is already taken. Try again.")));
			}
			unset($result);
			// I guess not, let's verify the password
			if($password !== $vpassword) {
				die(json_encode(array("result" => "error", "response" => "Passwords don't match. Try again.")));
			}
			// Let's verify the email
			if(!preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/',$email)) {
				die(json_encode(array("result" => "error", "response" => "Email address not valid. Try again.")));
			}
			// Well, everything looks good, let's go
			// Create a password hash
			$salt = substr(md5(uniqid(rand(), true)), 0, 9);
			$passhash = sha1($salt.$password);
			// Clean!
			$email = sqlite_escape_string($email);
			$displayname = sqlite_escape_string(strip_tags($displayname));
			$role = sqlite_escape_string($role);
			$ip = $_SERVER['REMOTE_ADDR'];
			// Create the user!
			@$dbh->query("INSERT INTO users (username,password,email,displayname,role,ip,salt) VALUES('$username','$passhash','$email','$displayname','$role', '$ip', '$salt');") or die(json_encode(array("result" => "error", "response" => sqlite_error_string($dbh->lastError()))));
			die(json_encode(array("result" => "success", "response" => "User $username created.")));
		}
		else {
			die(json_encode(array("result" => "error", "response" => "You're not allowed to add users.")));
		}
	}
}

if(isset($_POST['editprofilesubmit'])) {
	if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
		die(json_encode(array("result" => "error", "response" => "CSRF token incorrect or missing.")));
	}
	else {
		// Can the user do this?
		if(permissions(1)) {
			// Sanitize input fields
			$password = sqlite_escape_string($_POST['password']);
			$email = sqlite_escape_string($_POST['email']);
			$displayname = sqlite_escape_string($_POST['displayname']);
			$vpassword = sqlite_escape_string($_POST['vpassword']);
			$c_user = sqlite_escape_string(userFetch('username', 1));
			// Run database query to get current password hash
			$dbpasshash = @$dbh->query("SELECT password FROM users WHERE username='$c_user'") or die(json_encode(array("result" => "error", "response" => "username query failed.")));
			$dbsalt = @$dbh->query("SELECT salt FROM users WHERE username='$c_user'") or die(json_encode(array("result" => "error", "response" => "salt query failed.")));
			// Recreate hash
			$passhash = sha1($dbsalt->fetchSingle().$vpassword);
			// Do they match?
			if($passhash === $dbpasshash->fetchSingle()) {
				if(isset($_POST['pw-ck']) && $_POST['pw-ck'] == 1) {
					// Let's make a new salt!
					$salt = substr(md5(uniqid(rand(), true)), 0, 9);
					$passhash = sha1($salt.$password);
					// Send it up to the mothership...I mean the database!
					@$dbh->query("UPDATE users SET password='$passhash', salt='$salt' WHERE username='$c_user'") or die(json_encode(array("result" => "error", "response" => "password update failed.")));
				}
				if(isset($_POST['em-ck']) && $_POST['em-ck'] == 1) {
					// Send it to the database
					@$dbh->query("UPDATE users SET email='$email' WHERE username='$c_user'") or die(json_encode(array("result" => "error", "response" => "email update failed.")));
				}
				if(isset($_POST['dn-ck']) && $_POST['dn-ck'] == 1) {
					// Send it to the database
					@$dbh->query("UPDATE users SET displayname='$displayname' WHERE username='$c_user'") or die(json_encode(array("result" => "error", "response" => "display name update failed.")));
				}
			}
			else {
				die(json_encode(array("result" => "error", "response" => "security password incorrect.")));
			}
			die(json_encode(array("result" => "success")));
		}
		else {
			die(json_encode(array("result" => "error", "response" => "user not allowed.")));
		}
	}
}

if(isset($_POST['deleteusersubmit'])) {
	if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
		die(json_encode(array("result" => "error", "response" => "CSRF token incorrect or missing.")));
	}
	else {
		// Can the user do this?
		if(permissions(2)) {
			# Check the user level of the user being deleted
			$query = @$dbh->query("SELECT role FROM users WHERE id=".(int)$_POST['id']) or die(json_encode(array("result" => "error", "response" => "could not get the role of the user being deleted.")));
			if(userFetch('role', 2) >= $query->fetchSingle() ) {
				# Execute query to delete user
				@$dbh->query("DELETE FROM users WHERE id=".(int)$_POST['id']) or die(json_encode(array("result" => "error", "response" => "user deletion command failed.")));
				die(json_encode(array("result" => "success")));
			}
		}
	}
}

# Process comment approval
if(isset($_POST['approvecomment'])) {
	if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
		die(json_encode(array("result" => "error", "response" => "CSRF token incorrect or missing.")));
	}
	else {
		if(permissions(2)) {
			# Execute query to approve comment
			@$dbh->query("UPDATE comments SET published='1' WHERE id=".(int)$_POST['id']) or die(json_encode(array("result" => "error", "response" => "comment approval command failed.")));
			die(json_encode(array("result" => "success")));
		}
	}
}

?>
