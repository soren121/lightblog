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
	$author = sqlite_escape_string(userFetch('realname', 'r'));
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
	$update = "UPDATE ".$type."s SET title='".$title."', ".$type."='".$text."' WHERE id=".$id;
	if($title == $ptitle and $text !== $ptext) { str_replace("title='".$title."',", "title='".$title."'", $update); }
	if($title == $ptitle) { str_replace("title='".$title."'", "", $update); }
	if($text == $ptext) { str_replace($type."='".$text."'", "", $update); }
	if($title == $ptitle and $text == $ptext) {
		echo bloginfo('url', 'r').$type.".php?id=".$id;
		die();
	}
	else {
		$dbh->query($update) or die(sqlite_error_string($dbh->lastError));		
		# Return full url to page to jQuery
		echo bloginfo('url', 'r').$type.".php?id=".$id;
		# Prevent the rest of the page from loading
		die();
	}
}

# Process registration
if(isset($_POST['processregistration'])) {
	# Initiate MathValidator
	require(ABSPATH .'/Sources/MathValidator.php');
	$mv = new MathValidator;
	# Generate and set salt
	$salt = substr(md5(uniqid(rand(), true)), 0, 9);
	# Set and escape all variables for easy access
	$username = sqlite_escape_string($_POST['username']);
	$password = md5($salt.$_POST['password']);
	$email = sqlite_escape_string($_POST['email']);
	$dname = sqlite_escape_string($_POST['dname']);
	$ip = sqlite_escape_string($_SERVER['REMOTE_ADDR']);
	$arians = (int)$_POST['arians'];
	# Check math answer
	if($mv->checkResult($arians, $_SESSION['mathvalidator_c']) == false) { echo "Incorrect answer!"; }
	# Insert into database
	$dbh->query("INSERT INTO users (username,password,email,displayname,role,ip,salt) VALUES('".$username."', '".$password."', '".$email."', '".$displayname."', 0, '".$ip."', '".$salt."')");	
	# Kill this file so jQuery can finish
	die();
}

# Process comment submission
if(isset($_POST['comment_submit'])) {
	# Check if the required fields have been filled
	if(strlen($_POST['name']) and strlen($_POST['email']) and strlen($_POST['text']) > 0) {
		# Escape and format input
		$name = sqlite_escape_string($_POST['name']);
		$email = sqlite_escape_string($_POST['email']);
		$website = sqlite_escape_string(htmlentities($_POST['website']));
		$text = sqlite_escape_string(removeXSS($_POST['text']));
		$post_id = (int)$_POST['post_id'];
		$date = time();
		# Insert comment into database
		if(strlen($_POST['website']) > 0) {
			$dbh->query("INSERT INTO comments (post_id,name,email,website,date,text) VALUES('$post_id','$name','$email','$website','$date','$text')");
		} else {
			$dbh->query("INSERT INTO comments (post_id,name,email,date,text) VALUES('$post_id','$name','$email','$date','$text')");
		}
	}
}

# Process post/page deletion
if(isset($_POST['delete']) && $_POST['delete'] == 'true') {
	# Delete post/page
	$dbh->query("DELETE FROM ".sqlite_escape_string($_POST['type'])."s WHERE id=".(int)$_POST['id']) or die(sqlite_error_string($dbh->lastError));
}

?>