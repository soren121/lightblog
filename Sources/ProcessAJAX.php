<?php session_start();

/*********************************************

	LightBlog 0.9
	SQLite blogging platform

	Sources/ProcessAJAX.php

	©2009-2010 The LightBlog Team. All 
	rights reserved. Released under the 
	GNU General Public License 3. For 
	all licensing information, please 
	see the LICENSE.txt document 
	included in this distribution.

*********************************************/

// Require config file
require('../config.php');
require(ABSPATH .'/Sources/Core.php');

# Process post/page/category creation
if(isset($_POST['create'])) {
	$type = $_POST['type'];
	if($type !== 'category' && permissions(1) || $type === 'category' && permissions(2)) {
		if($type !== 'category') {
			# Require the HTML filter class
			require('Class.InputFilter.php');
			# Set allowed tags
			$allowed_tags = array('b', 'i', 'u', 'em', 'strong', 'img', 'a', 'ul', 'ol', 'li', 'span', 'quote', 'br');
			$allowed_attr = array('id', 'class', 'href', 'title', 'alt', 'style');
			# Initialize class
			$filter = new InputFilter($allowed_tags, $allowed_attr, 0, 0, 1);
			# Grab the data from form and escape the text
			$title = sqlite_escape_string(strip_tags($_POST['title']));
			$text = sqlite_escape_string($filter->process($_POST['text']));
			$date = time();
			$author = sqlite_escape_string(userFetch('displayname', 'r'));
			$category = (int)$_POST['category'];
			# Check published checkbox
			if(isset($_POST['published']) && $_POST['published'] == 1) { $published = 1; }
			else { $published = 0; }
			# Check comments checkbox
			if(isset($_POST['comments']) && $_POST['comments'] == 1) { $comments = 1; }
			else { $comments = 0; }
			# Insert post/page into database
			if($type == 'post') {
				$dbh->query("INSERT INTO posts (title,post,date,author,published,category,comments) VALUES('".$title."','".$text."',$date,'".$author."',$published,$category,$comments)") or die(sqlite_error_string($dbh->lastError()));
			}
			else {
				$dbh->query("INSERT INTO pages (title,page,date,author,published) VALUES('".$title."','".$text."',$date,'".$author."',$published)") or die(sqlite_error_string($dbh->lastError()));
			}
		}
		else {
			$title = sqlite_escape_string(strip_tags($_POST['title']));
			$info = sqlite_escape_string(cleanHTML($_POST['text']));
			$shortname = substr(str_replace(array(" ", ".", ","), "", strtolower($title)), 0, 15);
			$type = "category";
			$dbh->query("INSERT INTO categories (shortname,fullname,info) VALUES('$shortname','$title','$info')");
		}
		# Fetch post ID from database
		$id = $dbh->lastInsertRowid();
		# Return full url to post to jQuery
		echo bloginfo('url', 'r')."?".$type."=".$id;
		# Prevent the rest of the page from loading
		die();
	}
}

# Process post/page editing
if(isset($_POST['edit'])) {
	# Require the HTML filter class
	require('Class.InputFilter.php');
	# Set allowed tags
	$allowed_tags = array('b', 'i', 'u', 'em', 'strong', 'img', 'a', 'ul', 'ol', 'li', 'span', 'quote', 'br');
	$allowed_attr = array('id', 'class', 'href', 'title', 'alt', 'style');
	# Initialize class
	$filter = new InputFilter($allowed_tags, $allowed_attr, 0, 0, 1);
	# Grab the data from form and escape the text
	$title = sqlite_escape_string(strip_tags($_POST['title']));
	$text = sqlite_escape_string($filter->process($_POST['text']));
	$id = (int)$_POST['id'];
	$type = sqlite_escape_string($_POST['type']);
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
		$shortname = substr(str_replace(array(" ", ".", ","), "", strtolower($title)), 0, 15);
	}
	# Query for previous data
	$result = $dbh->query("SELECT * FROM ".($type == 'category' ? 'categorie' : $type)."s WHERE id=".$id) or die(sqlite_error_string($dbh->lastError()));
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
	# Run through scenarios
	if($type == 'post' || $type == 'page') {
		if(stripslashes($ptitle) !== $title) { $base .= "title='".sqlite_escape_string($title)."', "; }
		if(stripslashes($ptext) !== $text) { $base .= $type."='".sqlite_escape_string($text)."', "; }
		if((int)$ppublished !== $published) { $base .= "published='".(int)$published."', "; }
		if($type == 'post') {
			if((int)$pcategory !== $category) { $base .= "category='".(int)$category."', "; }
			if((int)$pcomments !== $comments) { $base .= "comments='".(int)$comments."', "; }
		}
	}
	else {
		if(stripslashes($ptitle) !== $title) { $base .= "fullname='".sqlite_escape_string($title)."', "; }
		if(stripslashes($pshortname) !== $shortname) { $base .= "shortname='".sqlite_escape_string($shortname)."', "; }
		if(stripslashes($ptext) !== $text) { $base .= "info='".sqlite_escape_string($text)."', "; }
	}
	# Remove last comma & space
	$base = substr($base, 0, -2);
	$base .= " WHERE id=".(int)$id;
	# Execute modified query
	$dbh->query($base) or die(sqlite_error_string($dbh->lastError));		
	# Return full url to page to jQuery
	echo bloginfo('url', 'r')."?".$type."=".$id;
	# Prevent the rest of the page from loading
	die();
}

# Process post/page/category deletion
if(isset($_POST['delete']) && $_POST['delete'] == 'true') {
	# Execute query to delete post/page/category
	$dbh->query("DELETE FROM ".sqlite_escape_string(strip_tags($_POST['type']))." WHERE id=".(int)$_POST['id']) or die(sqlite_error_string($dbh->lastError()));
	if($_POST['type'] == 'posts') {
		# Delete comments associated with this post
		$dbh->query("DELETE FROM comments WHERE pid=".(int)$_POST['id']) or die(sqlite_error_string($dbh->lastError()));
	}
}

# Process theme change
if(isset($_POST['themesubmit'])) {
	if(permissions(3)) {
		# Execute query to change theme
		$dbh->query("UPDATE core SET value='".sqlite_escape_string(strip_tags($_POST['changetheme']))."' WHERE variable='theme'") or die(sqlite_error_string($dbh->lastError()));
	}
}

# Process title change
if(isset($_POST['changetitle'])) {
	# Check permissions
	if(permissions(3)) {
		# Execute query to change title
		$dbh->query("UPDATE core SET value='".sqlite_escape_string(strip_tags($_POST['changetitle']))."' WHERE variable='title'") or die(sqlite_error_string($dbh->lastError()));
	}
}

# Process URL change
if(isset($_POST['changeurl'])) {
	# Check permissions
	if(permissions(3)) {
		# Execute query to change url
		$dbh->query("UPDATE core SET value='".sqlite_escape_string(strip_tags($_POST['changeurl']))."' WHERE variable='url'") or die(sqlite_error_string($dbh->lastError()));
	}
}

# Process comment moderation setting change
if(isset($_POST['commentmoderation'])) {
	# Check permissions
	if(permissions(3)) {
		# Execute query to change setting
		$dbh->query("UPDATE core SET value='".sqlite_escape_string(strip_tags($_POST['commentmoderation']))."' WHERE variable='comment_moderation'") or die(sqlite_error_string($dbh->lastError()));
	}
}

// User creation
if(isset($_POST['addusersubmit'])) {
	// Can the user do this?
	if(permissions(3)) {
		// Set
		if(!isset($_POST['username'])){die("span style=\"color:red;margin-left:5px;\" class=\"inform\">Fatal error: ALL fields need to be filled in.");}else{$username = sqlite_escape_string($_POST['username']);}
		if(!isset($_POST['password'])){die("span style=\"color:red;margin-left:5px;\" class=\"inform\">Fatal error: ALL fields need to be filled in.");}else{$password = $_POST['password'];}
		if(!isset($_POST['vpassword'])){die("span style=\"color:red;margin-left:5px;\" class=\"inform\">Fatal error: ALL fields need to be filled in.");}else{$vpassword = $_POST['vpassword'];}
		if(!isset($_POST['email'])){die("span style=\"color:red;margin-left:5px;\" class=\"inform\">Fatal error: ALL fields need to be filled in.");}else{$email = $_POST['email'];}
		if(!isset($_POST['displayname'])){die("span style=\"color:red;margin-left:5px;\" class=\"inform\">Fatal error: ALL fields need to be filled in.");}else{$displayname = $_POST['displayname'];}
		if(!isset($_POST['role'])){die("span style=\"color:red;margin-left:5px;\" class=\"inform\">Fatal error: ALL fields need to be filled in.");}else{$role = $_POST['role'];}
		// Does that username exist already?		
		$result = $dbh->query("SELECT * FROM users WHERE username='$username';") or die("span style=\"color:red;margin-left:5px;\" class=\"inform\">".sqlite_error_string($dbh->lastError()));
		if($result->numRows() < 0) { die("span style=\"color:red;margin-left:5px;\" class=\"inform\">Fatal error: Username already in use."); }
		unset($result);
		// I guess not, let's verify the password
		if($password !== $vpassword) { die("span style=\"color:red;margin-left:5px;\" class=\"inform\">Fatal error: Passwords not the same."); }
		// Let's verify the email
		if(!preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/',$email)) { die("span style=\"color:red;margin-left:5px;\" class=\"inform\">Fatal error: Email syntax not valid."); }
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
		$dbh->query("INSERT INTO users (username,password,email,displayname,role,ip,salt) VALUES('$username','$passhash','$email','$displayname','$role', '$ip', '$salt');") or die("span style=\"color:red;margin-left:5px;\" class=\"inform\">".sqlite_error_string($dbh->lastError()));
		echo "span style=\"color:green;margin-left:5px;\" class=\"inform\">User ".$username." created successfully.";
	}
	else {
		die("span style=\"color:red;margin-left:5px;\" class=\"inform\">Fatal error: user not allowed!");
	}
}

if(isset($_POST['editprofilesubmit'])) {
	// Can the user do this?
	if(permissions(1)) {
		// Sanitize input fields
		$password = sqlite_escape_string($_POST['password']);
		$email = sqlite_escape_string($_POST['email']);
		$displayname = sqlite_escape_string($_POST['displayname']);
		$vpassword = sqlite_escape_string($_POST['vpassword']);
		$c_user = sqlite_escape_string(userFetch('username', 1));
		// Run database query to get current password
		$dbpasshash = $dbh->query("SELECT password FROM users WHERE username='$c_user'") or die("span style=\"color:red;margin-left:5px;\" class=\"inform\">Fatal error: Username query failed.");
		$dbsalt = $dbh->query("SELECT salt FROM users WHERE username='$c_user'") or die("span style=\"color:red;margin-left:5px;\" class=\"inform\">Fatal error: Salt query failed.");
		// Recreate hash
		$passhash = sha1($dbsalt->fetchSingle().$vpassword);
		// Do they match?
		if($passhash === $dbpasshash->fetchSingle()) {
			if(isset($_POST['pw-ck']) && $_POST['pw-ck'] == 1) {
				// Let's make a new salt!
				$salt = substr(md5(uniqid(rand(), true)), 0, 9);
				$passhash = sha1($salt.$password);
				// Send it up to the mothership...I mean the database!
				$dbh->query("UPDATE users SET password='$passhash', salt='$salt' WHERE username='$c_user'") or die("span style=\"color:red;margin-left:5px;\" class=\"inform\">Fatal error: Password update failed.");
			}
			if(isset($_POST['em-ck']) && $_POST['em-ck'] == 1) {
				// Send it to the database
				$dbh->query("UPDATE users SET email='$email' WHERE username='$c_user'") or die("span style=\"color:red;margin-left:5px;\" class=\"inform\">Fatal error: Email update failed.");
			}
			if(isset($_POST['dn-ck']) && $_POST['dn-ck'] == 1) {
				// Send it to the database
				$dbh->query("UPDATE users SET displayname='$displayname' WHERE username='$c_user'") or die("span style=\"color:red;margin-left:5px;\" class=\"inform\">Fatal error: Display name update failed.");
			}	
		}
		else {
			die("span style=\"color:red;margin-left:5px;\" class=\"inform\">Fatal error: Security password check failed.");
		}
		echo "span style=\"color:green;margin-left:5px;\" class=\"inform\">Success! Changes will appear on next login.";
	}
	else {
		die("span style=\"color:red;margin-left:5px;\" class=\"inform\">Fatal error: user not allowed!");
	}
}

if(isset($_POST['deleteusersubmit'])) {
	// Can the user do this?
	if(permissions(2)) {
		# Check the user level of the user being deleted
		$query = $dbh->query("SELECT role FROM users WHERE id=".(int)$_POST['id']) or die(sqlite_error_string($dbh->lastError()));
		if(userFetch('role', 2) >= $query->fetchSingle()) {		
			# Execute query to delete user
			$dbh->query("DELETE FROM users WHERE id=".(int)$_POST['id']) or die(sqlite_error_string($dbh->lastError()));
		}
	}
}

# Process comment approval
if(isset($_POST['approvecomment'])) {
	if(permissions(2)) {
		# Execute query to approve comment
		$dbh->query("UPDATE comments SET published='1' WHERE id=".(int)$_POST['id']) or die(sqlite_error_string($dbh->lastError()));
	}
}

?>
