<?php session_start();

/*********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	Sources/ProcessBrowser.php
	
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

// Process comment submission
if(isset($_POST['comment_submit'])) {
	if(bloginfo('comment_moderation', 1) === 'none') {
		if(strlen($_POST['comment_name']) && strlen($_POST['comment_email']) > 0) {
				// Escape values
				$comment_pid = intval($_POST['comment_pid']);
				$comment_name = sqlite_escape_string(strip_tags(cleanHTML($_POST['comment_name'])));
				$comment_email = sqlite_escape_string(strip_tags(cleanHTML($_POST['comment_email'])));
				$comment_website = sqlite_escape_string(strip_tags(cleanHTML($_POST['comment_website'])));
				$comment_date = time();
				$comment_text = sqlite_escape_string(cleanHTML($_POST['comment_text']));
				// Submit the comment
				$dbh->query("INSERT INTO comments (pid,name,email,website,date,text) VALUES($comment_pid,'$comment_name','$comment_email','$comment_website',$comment_date,'$comment_text')") or die(sqlite_error_string($dbh->lastError));
		}
		// Send the user back to the page they came from
		header('Location: '.bloginfo('url',2).'?post='.(int)$_POST['comment_pid']);
	}
	if(bloginfo('comment_moderation', 1) === 'approval') {
	
	}
}

?>