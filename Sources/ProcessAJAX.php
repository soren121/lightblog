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
			$dbh->query("INSERT INTO comments (post_id,name,email,website,date,text) VALUES('$post_id','$name','$email','$website','$date','$text')") or die(sqlite_error_string($dbh->lastError));
			echo $_POST['name'].'&'.$_POST['email'].'&'.$_POST['website'].'&'.$_POST['text'];
		} 
		else {
			$dbh->query("INSERT INTO comments (post_id,name,email,date,text) VALUES('$post_id','$name','$email','$date','$text')") or die(sqlite_error_string($dbh->lastError));
			echo $_POST['name'].'&'.$_POST['email'].'&'.$_POST['text'];
		}
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

?>