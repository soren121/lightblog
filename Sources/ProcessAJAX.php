<?php session_start();

/*********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	Sources/ProcessAJAX.php
	
	©2009 soren121. All rights reserved.
	Released under the GNU General
	Public License. For all licensing
	information, please see the
	LICENSE.txt document included in this
	distribution.

*********************************************/

// Require config file
require('../config.php');
require(ABSPATH .'/Sources/Core.php');

# Process post/page creation
if(isset($_POST['create'])) {
	# Check permissions
	if(permissions(1)) {
		# Grab data from form and escape the text
		$title = sqlite_escape_string(strip_tags(cleanHTML($_POST['title']));
		$text = sqlite_escape_string(cleanHTML($_POST['text']));
		$date = time();
		$author = sqlite_escape_string(userFetch('displayname', 'r'));
		# Insert post/page into database
		$dbh->query("INSERT INTO ".$type."s (title,".$type.",date,author) VALUES('".$title."','".$text."','".$date."','".$author."')") or die(sqlite_error_string($dbh->lastError));
		# Fetch post ID from database
		$id = $dbh->lastInsertRowid();
		# Return full url to post to jQuery
		echo bloginfo('url', 'r').$type.".php?id=".$id;
		# Unset variables
		unset($result, $id);
		# Prevent the rest of the page from loading
		die();
	}
}

# Process post/page editing
if(isset($_POST['edit'])) {
	# Check permissions
	if(permissions(2)) {
		# Grab data from form and escape the text
		$title = sqlite_escape_string(strip_tags(cleanHTML($_POST['title']));
		$text = sqlite_escape_string(cleanHTML($_POST['text']));
		$type = sqlite_escape_string(strip_tags(cleanHTML($_POST['type'])));
		$id = (int)$_POST['id'];
		# Query for previous data
		$result = $dbh->query("SELECT * FROM ".$type."s WHERE id=".$id) or die(sqlite_error_string($dbh->lastError));
		# Fetch previous data
		while($past = $result->fetchObject()) {
			$ptitle = $past->title;
			if($type == 'post') { $ptext = $past->post; }
			elseif($type == 'page') { $ptext = $past->page; }
		}
		# Set default query
		$update = "UPDATE ".$type."s SET title='".$title."', ".$type."='".$text."' WHERE id=".$id;
		# Run through possible change scenarios and update query
		if($title == $ptitle and $text !== $ptext) { str_replace("title='".$title."',", "title='".$title."'", $update); }
		if($title == $ptitle) { str_replace("title='".$title."'", "", $update); }
		if($text == $ptext) { str_replace($type."='".$text."'", "", $update); }
		if($title == $ptitle and $text == $ptext) {
			# Nothing changed, so forget the query and send them the URL
			echo bloginfo('url', 'r').$type.".php?id=".$id;
			die();
		}
		else {
			# Execute modified query
			$dbh->query($update) or die(sqlite_error_string($dbh->lastError));		
			# Return full url to page to jQuery
			echo bloginfo('url', 'r').$type.".php?id=".$id;
			# Prevent the rest of the page from loading
			die();
		}
	}
}

# Process post/page deletion
if(isset($_POST['delete']) && $_POST['delete'] == 'true') {
	# Do they have the proper permissions?
	if(permissions(2)) {
		# Execute query to delete post/page
		$dbh->query("DELETE FROM ".sqlite_escape_string(strip_tags(cleanHTML($_POST['type'])))."s WHERE id=".(int)$_POST['id']) or die(sqlite_error_string($dbh->lastError));
	}
}

# Process theme change
if(isset($_POST['themesubmit'])) {
	# Do they have the proper permissions?
	if(permissions(3)) {
		# Execute query to change theme
		$dbh->query("UPDATE core SET value='".sqlite_escape_string(strip_tags(cleanHTML($_POST['changetheme'])))."' WHERE variable='theme'");
	}
}

# Process title change
if(isset($_POST['changetitle'])) {
	# Do they have the proper permissions?
	if(permissions(3)) {
		# Execute query to change title
		$dbh->query("UPDATE core SET value='".sqlite_escape_string(strip_tags(cleanHTML($_POST['changetitle'])))."' WHERE variable='title'");
	}
}

# Process URL change
if(isset($_POST['changeurl'])) {
	# Do they have the proper permissions?
	if(permissions(3)) {
		# Execute query to change url
		$dbh->query("UPDATE core SET value='".sqlite_escape_string(strip_tags(cleanHTML($_POST['changeurl']))."' WHERE variable='url'");
	}
}

// User creation
if(isset($_POST['addusersubmit'])) {
	// Can the user do this?
	if(permissions(3)) {
		// Set
		if(!isset($_POST['username'])){die("span style=\"color:red;margin-left:5px;\" class=\"inform\">Fatal error: ALL fields need to be filled in.");}else{$username = sqlite_escape_string(strip_tags(cleanHTML($_POST['username'])));}
		if(!isset($_POST['password'])){die("span style=\"color:red;margin-left:5px;\" class=\"inform\">Fatal error: ALL fields need to be filled in.");}else{$password = $_POST['password'];}
		if(!isset($_POST['vpassword'])){die("span style=\"color:red;margin-left:5px;\" class=\"inform\">Fatal error: ALL fields need to be filled in.");}else{$vpassword = $_POST['vpassword'];}
		if(!isset($_POST['email'])){die("span style=\"color:red;margin-left:5px;\" class=\"inform\">Fatal error: ALL fields need to be filled in.");}else{$email = $_POST['email'];}
		if(!isset($_POST['displayname'])){die("span style=\"color:red;margin-left:5px;\" class=\"inform\">Fatal error: ALL fields need to be filled in.");}else{$displayname = $_POST['displayname'];}
		if(!isset($_POST['role'])){die("span style=\"color:red;margin-left:5px;\" class=\"inform\">Fatal error: ALL fields need to be filled in.");}else{$role = $_POST['role'];}
		// Does that username exist already?		
		$result = $dbh->query("SELECT * FROM users WHERE username='$username';") or die("span style=\"color:red;margin-left:5px;\" class=\"inform\">".sqlite_error_string($dbh->lastError));
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
		$email = sqlite_escape_string(strip_tags(cleanHTML($email)));
		$displayname = sqlite_escape_string(strip_tags(cleanHTML($displayname)));
		$role = sqlite_escape_string(strip_tags(cleanHTML($role)));
		// Create the user!
		$dbh->query("INSERT INTO users (username,password,email,displayname,role,ip,salt) VALUES('$username','$passhash','$email','$displayname','$role', '".get_ip()."', '$salt');") or die("span style=\"color:red;margin-left:5px;\" class=\"inform\">".sqlite_error_string($dbh->lastError));
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
		$password = sqlite_escape_string(strip_tags(cleanHTML($_POST['password'])));
		$email = sqlite_escape_string(strip_tags(cleanHTML($_POST['email'])));
		$displayname = sqlite_escape_string(strip_tags(cleanHTML($_POST['displayname'])));
		$vpassword = $_POST['vpassword'];
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
		# Execute query to delete user
		$dbh->query("DELETE FROM users WHERE id=".(int)$_POST['id']) or die(sqlite_error_string($dbh->lastError));
	}
}

?>