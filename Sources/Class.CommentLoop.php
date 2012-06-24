<?php

/***********************************************

	LightBlog 0.9
	SQLite blogging platform

	Sources/Class.CommentLoop.php

	©2008-2012 The LightBlog Team. All
	rights reserved. Released under the
	GNU General Public License 3. For
	all licensing information, please
	see the LICENSE.txt document
	included in this distribution.

***********************************************/

/*
	Class: CommentLoop

	Provides an easy method to display a list of comments associated with a post.
*/
class CommentLoop
{
	// Variable: dbh
	private $dbh;

	// Variable: data
	// An array containing the currently loaded data.
	private $data;

	// Variable: current
	// An integer containing the current location within the loaded comments.
	private $current;

	/*
		Constructor: __construct

		Sets the database handle for all functions in our class.
	*/
	public function __construct()
	{
		$this->dbh = null;
		$this->data = array(
										'post' => array(
																'id' => -1,
															),
										'comments' => array(),
									);
		$this->current = null;

		$this->set_dbh($GLOBALS['dbh']);
		$this->load();
	}

	/*
		Function: set_dbh

		Sets the database handle.

		Parameters:

			dbh - Database handle object.
	*/
	private function set_dbh($dbh)
	{
		// Is this a valid handle?
		if(is_object($dbh) && $dbh instanceof SQLiteDatabase)
		{
			$this->dbh = $dbh;
		}
		else
		{
			// It's not a valid database :(
			trigger_error('Invalid object supplied.', E_USER_ERROR);
		}
	}

	/*
		Function: load

		Loads the necessary information to display the list of comments.

		Parameters:
			none

		Returns:
			void - Nothing is returned by this method.
	*/
	private function load()
	{
		// Do we have a post ID? We need that!
		if(!isset($GLOBALS['pid']))
		{
			trigger_error('No post ID specified', E_USER_NOTICE);

			return;
		}

		$this->data['post']['id'] = (int)$GLOBALS['pid'];

		// There are a few things from the posts table we need to fetch.
		$request = $this->dbh->query("
			SELECT
				allow_comments, allow_pingbacks, comments
			FROM posts
			WHERE post_id = {$this->data['post']['id']}
			LIMIT 1");

		// Does the post exist?
		if($request->numRows() > 0)
		{
			list($allow_comments, $allow_pingbacks, $comments) = $request->fetch(SQLITE_NUM);

			$this->data['post']['comments'] = !empty($allow_comments);
			$this->data['post']['pingbacks'] = !empty($allow_pingbacks);
			$this->data['post']['comments'] = $comments;

			// Now load up all the comments.
			$request = $this->dbh->query("
				SELECT
					comment_id, comment_type, published, commenter_id, commenter_name,
					commenter_email, commenter_website, commenter_ip, comment_date,
					comment_text
				FROM comments
				WHERE post_id = {$this->data['post']['id']}". (permissions(3) ? '' : ' AND published = 1'). "
				ORDER BY comment_date ASC");

			$users = array();
			while($row = $request->fetch(SQLITE_ASSOC))
			{
				$this->data['comments'][] = array(
																			'id' => $row['comment_id'],
																			'type' => $row['comment_type'],
																			'published' => !empty($row['published']),
																			'commenter' => array(
																											 'id' => $row['commenter_id'],
																											 'name' => $row['commenter_name'],
																											 'email' => $row['commenter_email'],
																											 'website' => is_url($row['commenter_website']) ? $row['commenter_website'] : null,
																											 'ip' => $row['commenter_ip'],
																										 ),
																			'date' => date('F j, Y \a\t g:i A', $row['comment_date']),
																			'timestamp' => $row['comment_date'],
																			'text' => $row['comment_text'],
																		);

				// We will load all user information at once.
				if($row['commenter_id'] > 0)
				{
					$users[] = $row['commenter_id'];
				}
			}

			// Did we load anything?
			if(count($this->data['comments']) == 0)
			{
				$this->data['comments'] = null;
			}
			else
			{
				// Load user data.
				users_load($users);

				// Just to be sure, reset the pointer of the array to the beginning.
				reset($this->data['comments']);

				// Now set the current location within the array.
				$this->current = key($this->data['comments']);
			}
		}
	}

	/*
		Function: allowed

		Returns whether the current post allows comments.

		Parameters:
			none

		Returns:
			bool - Returns true if the post allows comments, false if not.
	*/
	public function allowed()
	{
		return !empty($this->data['post']['comments']);
	}

	/*
		Function: pingbacks_allowed

		Returns whether the current post allows pingbacks.

		Parameters:
			none

		Returns:
			bool - Returns true if the post allows pingbacks, false if not.
	*/
	public function pingbacks_allowed()
	{
		return !empty($this->data['post']['pingbacks']);
	}

	/*
		Function: count

		Returns the number of comments on the current post.

		Parameters:
			none

		Returns:
			int - Returns the number of comments on the current post.

		Note:
			This number only reflects the total number of approved comments, as
			an administrator may see more comments than indicated by this method.
	*/
	public function count()
	{
		return $this->data['post']['id'] > -1 ? $this->data['post']['comments'] : -1;
	}

	/*
		Function: has_comments

		Returns whether the post has comments to display.

		Parameters:
			none

		Returns:
			bool - Returns true if there are comments to display, false if not.
	*/
	public function has_comments()
	{
		// Do we have any comments?
		if($this->data['comments'] !== null && $this->current !== null)
		{
			// Save the current location.
			$this->current = key($this->data['comments']);

			// Move us along.
			next($this->data['comments']);

			return $this->current !== null;
		}
		else
		{
			// Nope, we don't.
			return false;
		}
	}

	/*
		Function: list_comments

		Outputs a list of comments.

		Parameters:

			tag - The HTML tag to wrap the comment in.
	*/
	public function list_comments($tag = 'div')
	{
		// Do we have a comment to display?
		if($this->current !== null)
		{
			echo '<'.$tag.' class="comment '; alternateColor('c1', 'c2'); echo '" id="comment-'. $this->id().'">
					<img class="comment_gravatar" src="'. $this->gravatar(). '" alt="" />';

					if(utf_strlen($this->commenter_website()) == 0)
					{
						echo '<span class="comment_name">'. $this->commenter_name(). '</span>';
					}
					else
					{
						echo '<a class="comment_name" href="'. $this->commenter_website(). '" rel="nofollow">'. $this->commenter_name(). '</a>';
					}

					echo '<span class="comment_says"> says:</span><br />
					<a href="'. get_bloginfo('url'). '?post='. $this->data['post']['id']. '#comment-'. $this->id(). '" class="comment_date">'. $this->date().'</a><br />
					<p class="comment_text">'. $this->text(). '</p>
			</'.$tag.'>';
		}
		// Oh no, we screwed up :(
		else
		{
			// Send nothing back
			return false;
		}
	}

	/*
		Function: gravatar

		Outputs the user's Gravatar, derived from their email address.

		Parameters:

			size - The square pixel size to display the Gravatar (e.g. 32 == 32x32.) Defaults to 32.
	*/
	private function gravatar($size = 32)
	{
		// We didn't screw up and keep an empty query, did we?
		if($this->current !== null)
		{
			return 'http://www.gravatar.com/avatar.php?gravatar_id='. md5($this->commenter_email()). '&amp;size='. ((int)$size);
		}
		// Oh no, we screwed up :(
		else
		{
			// Send nothing back
			return false;
		}
	}

	/*
		Function: id

		Returns the ID of the current comment.

		Parameters:
			none

		Returns:
			int - Returns the comment ID, but null if there is no comment.
	*/
	public function id()
	{
		return $this->current !== null ? $this->data['comments'][$this->current]['id'] : -1;
	}

	public function type()
	{
		return $this->current !== null ? $this->data['comments'][$this->current]['type'] : null;
	}

	public function published()
	{
		return $this->current !== null ? $this->data['comments'][$this->current]['published'] : null;
	}

	public function commenter_id()
	{
		return $this->current !== null ? $this->data['comments'][$this->current]['commenter']['id'] : null;
	}

	public function commenter_name()
	{
		// Do we need to load the user's current name?
		if(($user = users_get($this->commenter_id())) !== false)
		{
			return $user['name'];
		}

		return $this->current !== null ? $this->data['comments'][$this->current]['commenter']['name'] : null;
	}

	public function commenter_email()
	{
		return $this->current !== null ? $this->data['comments'][$this->current]['commenter']['email'] : null;
	}

	public function commenter_website()
	{
		return $this->current !== null ? $this->data['comments'][$this->current]['commenter']['website'] : null;
	}

	public function commenter_ip()
	{
		return $this->current !== null ? $this->data['comments'][$this->current]['commenter']['ip'] : null;
	}

	public function date()
	{
		return $this->current !== null ? $this->data['comments'][$this->current]['date'] : null;
	}

	public function timestamp()
	{
		return $this->current !== null ? $this->data['comments'][$this->current]['timestamp'] : null;
	}

	public function text()
	{
		return $this->current !== null ? $this->data['comments'][$this->current]['text'] : null;
	}

	/*
		Function: messageHook

		Outputs a response, if any, from the comment processor.

		Parameters:

			tag - The HTML tag to wrap the message in.
	*/
	public function messageHook($tag = '')
	{
		if(isset($_SESSION['cmessage']))
		{
			// Get the end tag by removing attributes and adding a slash
			$endtag = explode(' ', str_replace('<', '</', $tag));

			// Put it all together
			echo $tag.$_SESSION['cmessage'].$endtag[0].'>';

			// Destroy the message, we're done with it now
			unset($_SESSION['cmessage']);
		}
		else
		{
			return false;
		}
	}

	/*
		Function: formHook

		Injects form inputs required by LightBlog into the comment form.
	*/
	public function formHook()
	{
		// Output the post ID as a hidden form input
		echo '<p style="display:none;"><input name="comment_pid" type="hidden" value="'.$GLOBALS['pid'].'" /></p>';
	}
}
?>