<?php

/***********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	Sources/Admin.php
	
	©2009 soren121. All rights reserved.
	Released under the GNU General
	Public License. For all licensing
	information, please see the
	LICENSE.txt document included in this
	distribution.

***********************************************/

// Require Core.php if it isn't already loaded
require_once('Core.php');

// Authentication check
if(($_SESSION['securestring'] !== $_COOKIE[strtolower(bloginfo('title','r')).'securestring']) && (!isset($_SESSION['SESSIONID']))) {
	session_destroy();
	die('Session is invalid. Return to the <a href="'.bloginfo('url','r').'">homepage</a>.');
}

// Similar IP lock
	// Get first 3 octets of current IP
	$current_ip = explode('.', get_ip());
	unset($current_ip[3]);
	$current_ip = implode('.', $current_ip);

	// Get first 3 octets of session IP
	$session_ip = explode('.', $_SESSION['ip']);
	unset($session_ip[3]);
	$session_ip = implode('.', $session_ip);

	if($current_ip !== $session_ip) {
		session_destroy();
		die('Session is invalid. Return to the <a href="'.bloginfo('url','r').'">homepage</a>.');
	}
	
// Function to list themes in a drop-down box
function list_themes() {
	// List directories
	$dir = dirlist(ABSPATH .'/themes'); 
	foreach($dir as $k => $v) {
		if(bloginfo('theme','r') == $k) {
			echo '<option selected="selected" value="'.$k.'">'.$v.'</option>';
		}
		else {
			echo '<option value="'.$k.'">'.$v.'</option>';
		}		
	}
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