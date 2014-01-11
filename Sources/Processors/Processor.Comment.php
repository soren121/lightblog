<?php
/*********************************************

	LightBlog 0.9
	SQLite blogging platform

	Sources/Processors/Processor.Comment.php

	©2008-2012 The LightBlog Team. All
	rights reserved. Released under the
	GNU General Public License 3. For
	all licensing information, please
	see the LICENSE.txt document
	included in this distribution.

*********************************************/

class Comment
{
	private $dbh;

	public function __construct()
	{
		$this->dbh = $GLOBALS['dbh'];
	}

	public function processor($data)
	{
		// We need to make sure that the post exists and allows comments!
		$comment_pid = (int)$_POST['comment_pid'];
		$query = $this->dbh->query("
			SELECT
				allow_comments
			FROM posts
			WHERE post_id = ". $comment_pid. "
			LIMIT 1");
			
		$comments_allowed = $query->fetch(PDO::FETCH_NUM);
		$query->closeCursor();
			
		if(!$query)
		{
			return array("result" => "error", "response" => "Failed to query database.");
		}
	
		if($comments_allowed[0] == 1)
		{
			// If they're a logged in user, we will set these ourselves.
			if(user()->is_logged())
			{
				// We will remove any effects of htmlspecialchars, as they will be
				// reapplied in a bit.
				$_POST['commenter_name'] = htmlspecialchars_decode(user()->userName(), ENT_QUOTES);
				$_POST['commenter_email'] = htmlspecialchars_decode(user()->email(), ENT_QUOTES);
	
				// !!! todo: auto-fill website
			}
	
			if(utf_strlen($_POST['commenter_name']) && utf_strlen($_POST['commenter_email']) > 0)
			{
				// Require the HTML filter class
				require(ABSPATH .'/Sources/Class.htmLawed.php');
	
				// Escape values
				$commenter_id = (int)user()->id();
				$published = get_bloginfo('comment_moderation') == 'none' ? 1 : 0;
				$commenter_ip = sqlite_escape_string(user()->ip());
				$commenter_name = sqlite_escape_string(utf_htmlspecialchars($_POST['commenter_name']));
				$commenter_email = sqlite_escape_string(utf_htmlspecialchars($_POST['commenter_email']));
				$commenter_website = is_url($_POST['commenter_website']) ? sqlite_escape_string(utf_htmlspecialchars($_POST['commenter_website'])) : '';
				$comment_date = time();
				$comment_text = sqlite_escape_string(htmLawed::hl($_POST['comment_text'], array('safe' => 1, 'elements' => 'a, b, strong, i, em, li, ol, ul, br, span, u, s, img, abbr, blockquote, strike, code')));
	
				// Do they want us to remember them?
				if(!empty($_POST['remember_me']))
				{
					setcookie(LBCOOKIE. '_cname', $_POST['commenter_name'], time() + 2592000, '/');
					setcookie(LBCOOKIE. '_cemail', $_POST['commenter_email'], time() + 2592000, '/');
					setcookie(LBCOOKIE. '_curl', !empty($_POST['commenter_website']) && is_url($_POST['commenter_website']) ? $_POST['commenter_website'] : '', time() + 2592000, '/');
				}
	
				$submit_query = $this->dbh->exec("
					INSERT INTO comments
					(post_id, published, commenter_id, commenter_name, commenter_email, commenter_website,
					 commenter_ip, comment_date, comment_text)
					VALUES($comment_pid, $published, '$commenter_id', '$commenter_name', '$commenter_email', '$commenter_website',
					 '$commenter_ip', '$comment_date', '$comment_text')");
	
				if(!$submit_query)
				{
					return array("result" => "error", "response" => "Failed to submit comment to database.");
				}
				
				if(get_bloginfo('comment_moderation') == 'approval')
				{
					// Set message
					return array("result" => "success", "response" => "Your comment will appear as soon as it is approved by a moderator.");
				}
				else
				{
					// Increment the posts comment count, then.
					$update_query = $this->dbh->exec("
						UPDATE posts
						SET comments = comments + 1
						WHERE post_id = $comment_pid");
						
					if(!$update_query)
					{
						return array("result" => "error", "response" => "Failed to query database.");
					}
					return array("result" => "success");
				}
			}
		}
	}
}

?>