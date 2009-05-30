<?php session_start();

/*********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	Sources/ProcessBrowser.php
	
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

// Process comment submission
if(isset($_POST['comment_submit'])) {
	if(strlen($_POST['comment_name']) && strlen($_POST['comment_email']) > 0) {
		// Has the user enabled Akismet?
		if(bloginfo('akismet',2) == 3) {
			// Initiate the Akismet class
			include(ABSPATH .'/Sources/Akismet.php');
			// Create a new Akismet object
			$akismet = new Akismet(bloginfo('url',2),bloginfo('akismet_key',2));
			// Set the comment data
			$akismet->setCommentAuthor($_POST['comment_name']);
			$akismet->setCommentAuthorEmail($_POST['comment_email']);
			$akismet->setCommentAuthorURL($_POST['comment_website']);
			$akismet->setCommentContent($_POST['comment_text']);
			$akismet->setPermalink(bloginfo('url',2).'post.php?id='.(int)$_POST['comment_pid']);
			// Check if it's spam or not
			if($akismet->isCommentSpam()) {
				// Escape values
				$comment_pid = intval($_POST['comment_pid']);
				$comment_name = sqlite_escape_string($_POST['comment_name']);
				$comment_email = sqlite_escape_string($_POST['comment_email']);
				$comment_website = sqlite_escape_string($_POST['comment_website']);
				$comment_date = time();
				$comment_text = sqlite_escape_string(cleanHTML($_POST['comment_text']));
				// Submit the comment
				$dbh->query("INSERT INTO comments (pid,name,email,website,date,text,spam) VALUES($comment_pid,'$comment_name','$comment_email','$comment_website',$comment_date,'$comment_text',1)") or die(sqlite_error_string($dbh->lastError));
			}
		}
		else {
			// Escape values
			$comment_pid = intval($_POST['comment_pid']);
			$comment_name = sqlite_escape_string($_POST['comment_name']);
			$comment_email = sqlite_escape_string($_POST['comment_email']);
			$comment_website = sqlite_escape_string($_POST['comment_website']);
			$comment_date = time();
			$comment_text = sqlite_escape_string(cleanHTML($_POST['comment_text']));
			// Submit the comment
			$dbh->query("INSERT INTO comments (pid,name,email,website,date,text) VALUES($comment_pid,'$comment_name','$comment_email','$comment_website',$comment_date,'$comment_text')") or die(sqlite_error_string($dbh->lastError));
		}
	}
	// Send the user back to the page they came from
	header('Location: '.bloginfo('url',2).'post.php?id='.(int)$_POST['comment_pid']);
}

?>