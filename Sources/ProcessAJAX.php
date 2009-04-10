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
if(isset($_POST['publish'])) {
	// grab data from form and escape the text
	$title = sqlite_escape_string($_POST['title']);
	$text = sqlite_escape_string($_POST['text']);
	$date = time();
	$author = sqlite_escape_string(userFetch('realname', 'r'));
	// insert post data
	if($_POST['type'] == "post") {
	 	$dbh->query("INSERT INTO posts (title,post,date,author,catid) VALUES('".$title."','".$text."','".$date."','".$author."','1')") or die(sqlite_error_string($dbh->lastError));
      	echo "Your post has been submitted. Thank you.";
	}
	// insert page data
	elseif($_POST['type'] == "page") {
		$dbh->query("INSERT INTO pages (title,page) VALUES('".$title."','".$text."')") or die(sqlite_error_string($dbh->lastError));
      	echo "Your page has been submitted. Thank you.";
	}
	die();
}

?>