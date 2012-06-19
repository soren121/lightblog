<?php session_start();

/*********************************************

	LightBlog 0.9
	SQLite blogging platform

	Sources/ProcessBrowser.php

	©2008-2012 The LightBlog Team. All
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
			require('Class.htmLawed.php');
			// Escape values
			$comment_name = sqlite_escape_string(strip_tags($_POST['comment_name']));
			$comment_email = sqlite_escape_string(strip_tags($_POST['comment_email']));
			$comment_website = sqlite_escape_string(strip_tags($_POST['comment_website']));
			$comment_date = time();
			$comment_text = sqlite_escape_string(htmLawed::hl($_POST['comment_text'], array('safe' => 1, 'elements' => 'a, b, strong, i, em, li, ol, ul, br, span, u, s, img, abbr, blockquote, strike, code')));

			if(get_bloginfo('comment_moderation') == 'approval') {
				// Submit the comment
				$dbh->query("INSERT INTO comments (published,pid,name,email,website,date,text) VALUES(0,$comment_pid,'$comment_name','$comment_email','$comment_website',$comment_date,'$comment_text')") or die(sqlite_error_string($dbh->lastError));
				// Set message
				$_SESSION['cmessage'] = 'Your comment will appear as soon as it is approved by a moderator.';
			}
			elseif(get_bloginfo('comment_moderation') == 'none') {
				// Submit the comment
				$dbh->query("INSERT INTO comments (pid,name,email,website,date,text) VALUES($comment_pid,'$comment_name','$comment_email','$comment_website',$comment_date,'$comment_text')") or die(sqlite_error_string($dbh->lastError));
			}
		}
		// Send the user back to the page they came from
		header('Location: '.get_bloginfo('url').'?post='.$comment_pid.'#commentform');
	}
}

?>
