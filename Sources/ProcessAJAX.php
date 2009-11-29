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
	# Grab data from form and escape the text
	$title = sqlite_escape_string($_POST['title']);
	$text = sqlite_escape_string($_POST['text']);
	$date = time();
	$author = sqlite_escape_string(userFetch('displayname', 'r'));
	$type = $_POST['type'];
	# Insert post/page into database
	$dbh->query("INSERT INTO ".$type."s (title,".$type.",date,author) VALUES('".$title."','".$text."','".$date."','".$author."')") or die(sqlite_error_string($dbh->lastError));
	# Fetch post ID from database
	$result = $dbh->query("SELECT id FROM ".$type."s WHERE date='".$date."'");
	$id = $result->fetchSingle();
	# Return full url to post to jQuery
	echo bloginfo('url', 'r').$type.".php?id=".$id;
	# Unset variables
	unset($result, $id);
	# Prevent the rest of the page from loading
	die();
}

# Process post/page editing
if(isset($_POST['edit'])) {
	# Grab data from form and escape the text
	$title = sqlite_escape_string($_POST['title']);
	$text = sqlite_escape_string($_POST['text']);
	$type = $_POST['type'];
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

# Process post/page deletion
if(isset($_POST['delete']) && $_POST['delete'] == 'true') {
	# Execute query to delete post/page
	$dbh->query("DELETE FROM ".sqlite_escape_string($_POST['type'])."s WHERE id=".(int)$_POST['id']) or die(sqlite_error_string($dbh->lastError));
}

# Process theme change
if(isset($_POST['themesubmit'])) {
	# Execute query to change theme
	$dbh->query("UPDATE core SET value='".sqlite_escape_string($_POST['changetheme'])."' WHERE variable='theme'");
}

# Process title change
if(isset($_POST['changetitle'])) {
	# Execute query to change title
	$dbh->query("UPDATE core SET value='".sqlite_escape_string($_POST['changetitle'])."' WHERE variable='title'");
}

# Process URL change
if(isset($_POST['changeurl'])) {
	# Execute query to change url
	$dbh->query("UPDATE core SET value='".sqlite_escape_string($_POST['changeurl'])."' WHERE variable='url'");
}

// User creation
if(isset($_POST['addusersubmit'])) {
	// Can the user do this?
	if(permissions(3)) {
		// Set
		$username = sqlite_escape_string($_POST['username']);
		$password = $_POST['password'];
		$vpassword = $_POST['vpassword'];
		$email = $_POST['email'];
		$displayname = $_POST['displayname'];
		$role = $_POST['role'];
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
		$email = sqlite_escape_string($email);
		$displayname = sqlite_escape_string(strip_tags($displayname));
		$role = sqlite_escape_string($role);
		// Create the user!
		$dbh->query("INSERT INTO users (username,password,email,displayname,role,ip,salt) VALUES('$username','$passhash','$email','$displayname','$role', '".get_ip()."', '$salt');") or die("span style=\"color:red;margin-left:5px;\" class=\"inform\">".sqlite_error_string($dbh->lastError));
		echo "span style=\"color:green;margin-left:5px;\" class=\"inform\">User ".$username." created successfully.";
	}
	else {
		die("span style=\"color:red;margin-left:5px;\" class=\"inform\">Fatal error: user not allowed!");
	}
}

?>