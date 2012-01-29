<?php session_start();

/*********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	Sources/ProcessBrowser.php
	
	�2008-2012 The LightBlog Team. All 
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
	$comment_pid = (int)$_POST['comment_pid'];
	$query = $dbh->query("SELECT comments FROM posts WHERE id=".$comment_pid) or die(sqlite_error_string($dbh->lastError()));
	if($query->fetchSingle() == '1') {
		if(strlen($_POST['comment_name']) && strlen($_POST['comment_email']) > 0) {
			// Require the HTML filter class
			require('Class.InputFilter.php');
			// Set allowed tags
			$allowed_tags = array('b', 'i', 'u', 'em', 'strong', 'img', 'a', 'ul', 'ol', 'li', 'span', 'quote', 'br');
			$allowed_attr = array('id', 'class', 'href', 'title', 'alt', 'style');
			// Initialize class
			$filter = new InputFilter($allowed_tags, $allowed_attr, 0, 0, 1);
			// Escape values
			$comment_name = sqlite_escape_string(strip_tags($_POST['comment_name']));
			$comment_email = sqlite_escape_string(strip_tags($_POST['comment_email']));
			$comment_website = sqlite_escape_string(strip_tags($_POST['comment_website']));
			$comment_date = time();
			$comment_text = sqlite_escape_string($filter->process($_POST['comment_text']));
			if(bloginfo('comment_moderation','r') == 'approval') {
				// Submit the comment
				$dbh->query("INSERT INTO comments (published,pid,name,email,website,date,text) VALUES(0,$comment_pid,'$comment_name','$comment_email','$comment_website',$comment_date,'$comment_text')") or die(sqlite_error_string($dbh->lastError));
				// Set message
				$_SESSION['cmessage'] = 'Your comment will appear as soon as it is approved by a moderator.';
			}
			if(bloginfo('comment_moderation','r') == 'none') {
				// Submit the comment
				$dbh->query("INSERT INTO comments (pid,name,email,website,date,text) VALUES($comment_pid,'$comment_name','$comment_email','$comment_website',$comment_date,'$comment_text')") or die(sqlite_error_string($dbh->lastError));
			}
		}
		// Send the user back to the page they came from
		header('Location: '.bloginfo('url',2).'?post='.$comment_pid.'#commentform');
	}
}

?>
