<?php

/***********************************************

	LightBlog 0.9
	SQLite blogging platform

	Sources/Class.PostLoop.php

	©2008-2012 The LightBlog Team. All
	rights reserved. Released under the
	GNU General Public License 3. For
	all licensing information, please
	see the LICENSE.txt document
	included in this distribution.

***********************************************/

/*
	Class: PostLoop

	Provides an easy method to display a list of posts, for example, on the front page.
*/
class PostLoop {
	# Set private database variables
	private $dbh = null;
	private $result = null;
	private $cur_result = null;

	/*
		Constructor: __construct

		Sets the database handle for all functions in our class.
	*/
	public function __construct() {
		$this->set_dbh($GLOBALS['dbh']);
	}

	/*
		Function: set_dbh

		Sets the database handle.

		Parameters:

			dbh - Database handle object.
	*/
	public function set_dbh($dbh) {
		# Is this a valid handle?
		if(is_object($dbh) && $dbh instanceof SQLiteDatabase) {
			$this->dbh = $dbh;
		}
		else {
			# It's not a valid database :(
			trigger_error('Invalid object supplied.');
		}
	}

	/*
		Function: parseQuery

		Parses the very basic query given and turns it in to a full-blown SQL query!

		Parameters:

			single - Optional parameter. Used to specify whether to obtain a single post or not.

		Returns:

			A complete SQL query.
	*/
	private function parseQuery($single = false) {
		// Get the view type
		$querytype = $GLOBALS['postquery']['type'];
		// If we're only showing one post, then we might be able to show it even if it's unpublished...
		if($single == true) {
			// Specify post ID
			$pid = (int)$GLOBALS['pid'];
			$where = "id=$pid";
			// If the user is logged in...
			if(!userFetch('username', 'r') && !permissions(1)) {
				$where .= " AND published=1";
			}
		}
		else {
			$where = "published=1";
		}
		if($querytype == 'archive') {
			$queryextra = substr_replace((int)$GLOBALS['postquery']['date'], '-', 4, 0);
			$queryextra = explode('-', $queryextra);
			$firstday = mktime(0, 0, 0, $queryextra[1], 1, $queryextra[0]);
			$lastday = mktime(0, 0, 0, ($queryextra[1] + 1), 0, $queryextra[0]);
			return "SELECT * FROM posts WHERE $where AND date BETWEEN $firstday AND $lastday ORDER BY id desc";
		}
		elseif($querytype == 'category') {
			$queryextra = (int)$GLOBALS['postquery']['catid'];
			return "SELECT * FROM posts WHERE $where AND category=$queryextra ORDER BY id desc";
		}
		else {
			return "SELECT * FROM posts WHERE $where ORDER BY id desc";
		}
	}

	/*
		Function: obtain_post

		Obtains the data for a single post from the database.
	*/
	public function obtain_post() {
		# Sanitize and set variables
		$dbh = $this->dbh;

		# Query the database for the post data
		$this->result = $dbh->query($this->parseQuery(true));
	}

	/*
		Function: obtain_posts

		Obtains the data for multiple posts from the database.

		Parameters:

			start - The ID of the first row to retrieve data from.
			limit - The number of rows to retrieve data from, starting from the ID specified in start.
	*/
	public function obtain_posts($start = 1, $limit = 8) {
		# Sanitize and set variables
		$start = (int)$start;
		$limit = (int)$limit;
		$start = ($start - 1) * $limit;
		$dbh = $this->dbh;
		$this->limit = $limit;

		# Query the database for post data
		$this->result = $dbh->query($this->parseQuery()." LIMIT ".$start.", ".$limit);
	}

	/*
		Function: has_posts

		Checks if there are any posts available for us to show.

		Returns:

			Boolean value (true/false).
	*/
	public function has_posts() {
		if($this->result->numRows() > 0) {
			return true;
		}
		else {
			# Erase our useless query results
			$this->result = null;
			$this->cur_result = null;
			return false;
		}
	}

	/*
		Function: loop

		Loops through our post query until we have no more posts.

		Returns:

			Boolean value (e.g. true/false.)
	*/
	public function loop() {
		# Do we have any posts?
		if(!empty($this->result)) {
			# Convert query results into something usable
			$this->cur_result = $this->result->fetchObject();
			# This while loop will remain true until we run out of posts
			while($post = $this->cur_result) {
				return true;
			}
			# At which point it turns false, ending the loop in the template file
			return false;
		}
		# We don't have any posts :(
		else {
			return false;
		}
	}

	/*
		Function: permalink

		Outputs the permanent URL (or permalink) to the current post.
	*/
	public function permalink() {
		# We didn't screw up and keep an empty query, did we?
		if(!empty($this->cur_result)) {
			# Nope, so return the post's permalink
			echo bloginfo('url', 'return'). '?post='. $this->cur_result->id;
		}
		else {
			# Looks like we messed up, send nothing
			return false;
		}
	}

	/*
		Function: title

		Outputs the title of the current post.
	*/
	public function title() {
		# We didn't screw up and keep an empty query, did we?
		if(!empty($this->cur_result)) {
			# Nope, so remove all sanitation and echo it out
			echo stripslashes($this->cur_result->title);
		}
		else {
			# Looks like we messed up, send nothing
			return false;
		}
	}

	/*
		Function: content

		Outputs the content, in full form or as an excerpt.

		Parameters:

			ending - The excerpt suffix. If set, this function will output an excerpt. (e.g. Read More...)
	*/
	public function content($ending = false) {
		# We didn't screw up and keep an empty query, did we?
		if(!empty($this->cur_result)) {
			$text = stripslashes($this->cur_result->post);
			$length = 360;
			// The following truncator code is from CakePHP
			// http://www.cakephp.org/
			// Licensed under the MIT license

			// if the plain text is shorter than the maximum length, return the whole text
			if(strlen(preg_replace('/<.*?>/', '', $text)) <= $length || !$ending) {
				echo $text;
			}
			else {
				// splits all html-tags to scanable lines
				preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
				$total_length = strlen($ending);
				$open_tags = array();
				$truncate = '';
				foreach ($lines as $line_matchings) {
					// if there is any html-tag in this line, handle it and add it (uncounted) to the output
					if (!empty($line_matchings[1])) {
						// if it's an "empty element" with or without xhtml-conform closing slash
						if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
							// do nothing
						// if tag is a closing tag
						} else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
							// delete tag from $open_tags list
							$pos = array_search($tag_matchings[1], $open_tags);
							if ($pos !== false) {
							unset($open_tags[$pos]);
							}
						// if tag is an opening tag
						} else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
							// add tag to the beginning of $open_tags list
							array_unshift($open_tags, strtolower($tag_matchings[1]));
						}
						// add html-tag to $truncate'd text
						$truncate .= $line_matchings[1];
					}
					// calculate the length of the plain text part of the line; handle entities as one character
					$content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
					if ($total_length + $content_length > $length) {
						// the number of characters which are left
						$left = $length - $total_length;
						$entities_length = 0;
						// search for html entities
						if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
							// calculate the real length of all entities in the legal range
							foreach ($entities[0] as $entity) {
								if ($entity[1] + 1 - $entities_length <= $left) {
									$left--;
									$entities_length += strlen($entity[0]);
								} else {
									// no more characters left
									break;
								}
							}
						}
						$truncate .= substr($line_matchings[2], 0, $left + $entities_length);
						// maximum lenght is reached, so get off the loop
						break;
					} else {
						$truncate .= $line_matchings[2];
						$total_length += $content_length;
					}
					// if the maximum length is reached, get off the loop
					if($total_length >= $length) {
						break;
					}
				}
				// ...search the last occurance of a space...
				$spacepos = strrpos($truncate, ' ');
				if (isset($spacepos)) {
					// ...and cut the text in this position
					$truncate = substr($truncate, 0, $spacepos);
				}
				// close all unclosed html-tags
				foreach ($open_tags as $tag) {
					$truncate .= '</' . $tag . '>';
				}
				if($ending) {
					// add the defined ending to the text
					$truncate .= '... <a href="?post='.$this->cur_result->id.'">'.$ending.'</a>';
				}
				// and echo
				echo $truncate;
			}
		}
		# Oh no, we screwed up :(
		else {
			# Send nothing back
			return false;
		}
	}

	/*
		Function: date

		Outputs the published date of the post.

		Parameters:

			format - The format, in PHP's date() format, in which to display the date. (e.g. F js, Y)
	*/
	public function date($format = null) {
		# We didn't screw up and keep an empty query, did we?
		if(!empty($this->cur_result)) {
			# Nope, so output the date in the right format
			echo date(!empty($format) ? $format : 'F jS, Y', $this->cur_result->date);
		}
		# Oh no, we screwed up :(
		else {
			# Send nothing back
			return false;
		}
	}

	/*
		Function: author

		Outputs the author of the current post.
	*/
	public function author() {
		if(!empty($this->cur_result)) {
			echo $this->cur_result->author;
		}
		else {
			return false;
		}
	}

	/*
		Function: commentNum

		Outputs the number of comments on the current post.
	*/
	public function commentNum() {
		if(!empty($this->cur_result)) {
			return commentNum($this->cur_result->id);
		}
		else {
			return false;
		}
	}

	/*
		Function: category

		Outputs the category the post was filed under.
	*/
	public function category() {
		if(!empty($this->cur_result)) {
			$dbh = $this->dbh;
			$result = $dbh->query("SELECT fullname FROM categories WHERE id=".(int)$this->cur_result->category);
			echo $result->fetchSingle();
		}
		else {
			return false;
		}
	}

	/*
		Destructor: __destruct

		Displays simple paginations link when the PostLoop class is destroyed with unset().
	*/
	public function pagination() {
		if($GLOBALS['postquery']['type'] != 'post' && $GLOBALS['postquery']['type'] != 'page') {
			global $file, $page;
			$dbh = $this->dbh;
			$limit = $this->limit;
			echo '<div class="pagination">';
			# Set the query to retrieve the number of rows
			$query = $dbh->query(str_replace(" * ", " COUNT(*) ", $this->parseQuery())) or die(sqlite_error_string($dbh->lastError));
			# Query the database
			@list($totalitems) = $query->fetch(SQLITE_NUM);
			# Set various required variables
			$prev = $page - 1;						# Previous page is page - 1
			$next = $page + 1;						# Next page is page + 1
			# Set $pagination
			$pagination = "";
			# Do we have more than one page?
			if($totalitems > $limit) {
				# Add the next link
				if($page > 1) {
					$pagination .= "<a href=\"".$file."?p=".$prev."\" class=\"next\">Newer Posts &raquo;</a>";
				}
				# Add the previous link
				if(($page * $limit) < $totalitems) {
					$pagination .= "<a href=\"".$file."?p=".$next."\" class=\"prev\">&laquo; Older Posts</a>";
				}
			}
			# Return the links! Duh!
			echo $pagination.'</div>';
		}
	}
}

?>