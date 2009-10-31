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
	// Was everything sent?
	if(isset($_POST['username'],$_POST['password'],$_POST['vpassword'],$_POST['email'],$_POST['displayname'],$POST['role']) {
		// Clean!
		$username = sqlite_escape_string($_POST['username']);
		$password = sqlite_escape_string($_POST['password']);
		$vpassword = sqlite_escape_string($_POST['vpassword']);
		$email = sqlite_escape_string($_POST['email']);
		$displayname = sqlite_escape_string(strip_tags($_POST['displayname']));
		$role = sqlite_escape_string($_POST['role']);
		// Global the database handle for use
		global $dbh;
		// Does that username exist already?		
		$result = $dbh->query("SELECT * FROM users WHERE username='$username'") or die(sqlite_error_string($dbh->lastError));
		if(sqlite_num_rows($result) < 0) { die("Username already in use."); }
		unset($result);
		// I guess not, let's verify the password
		if($password !=== $vpassword) { die("Passwords not the same."); }
		// Let's check the email syntax with a simple regex
		if(!preg_match("\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b", $email)) { die("Email not valid."); }
		// Well, everything looks good, let's go
		// Create a password hash
		$salt = substr(md5(uniqid(rand(), true)), 0, 9);
		$passhash = sha1($salt.$password);
		// Create the user!
		$dbh->query("INSERT INTO users (username,password,email,displayname,role,salt) VALUES('$username','$password','$email','$displayname','$role','$salt')");
	}
}

?>