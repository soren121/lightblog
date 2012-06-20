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
	// Set private database variables
	private $dbh = null;
	private $result = null;
	private $cur_result = null;

	/*
		Constructor: __construct

		Sets the database handle for all functions in our class.
	*/
	public function __construct()
	{
		$this->set_dbh($GLOBALS['dbh']);
	}

	/*
		Function: set_dbh

		Sets the database handle.

		Parameters:

			dbh - Database handle object.
	*/
	public function set_dbh($dbh)
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
		Function: comments_open

		Checks if comments are enabled.

		Returns:

			Boolean value (e.g. true/false.)
	*/
	public function comments_open()
	{
		$dbh = $this->dbh;
		$pid = (int)$GLOBALS['pid'];
		$query = $dbh->query("SELECT comments FROM 'posts' WHERE id='$pid'");

		if($query->fetchSingle() == 1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/*
		Function: obtain_comments

		Obtains the data for comments associated with a post.
	*/
	public function obtain_comments()
	{
		$dbh = $this->dbh;
		$pid = (int)$GLOBALS['pid'];
		$this->result = $dbh->query("SELECT * FROM 'comments' WHERE published=1 AND pid='$pid'");
	}

	/*
		Function: has_comments

		Checks if the query result we got contained any comments for that post.

		Returns:

			Boolean value (e.g. true/false.)
	*/
	public function has_comments()
	{
		// Do we have any comments?
		if(!empty($this->result))
		{
			// Convert query results into something usable
			$this->cur_result = $this->result->fetchObject();

			// This while loop will remain true until we run out of comments
			while($post = $this->cur_result)
			{
				return true;
			}

			// At which point it turns false, ending the loop in the template file
			return false;
		}
		// We don't have any comments :(
		else
		{
			// Erase our useless query results
			$this->result = null;
			$this->cur_result = null;

			// Send the bad news (aka end the while loop)
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
		if(!empty($this->cur_result))
		{
			echo '<'.$tag.' class="comment '; alternateColor('c1', 'c2'); echo '" id="comment-'.(int)$this->cur_result->id.'">
					<img class="comment_gravatar" src="'.$this->gravatar().'" alt="" />';

					if($this->cur_result->website == '')
					{
						echo '<span class="comment_name">'.$this->cur_result->name.'</span>';
					}
					else
					{
						echo '<a class="comment_name" href="'.$this->cur_result->website.'">'.stripslashes($this->cur_result->name).'</a>';
					}

					echo '<span class="comment_says"> says:</span><br />
					<a href="'.bloginfo('url','r').'?post='.(int)$this->cur_result->pid.'#comment-'.(int)$this->cur_result->id.'" class="comment_date">'.date('F j, Y \a\t g:i A', (int)$this->cur_result->date).'</a><br />
					<p class="comment_text">'.$this->cur_result->text.'</p>
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
		if(!empty($this->cur_result))
		{
			$email = md5($this->cur_result->email);
			$size = (int)$size;
			return "http://www.gravatar.com/avatar.php?gravatar_id=".$email."&amp;size=".$size;
		}
		// Oh no, we screwed up :(
		else
		{
			// Send nothing back
			return false;
		}
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