<?php

/***********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	Sources/Template.php
	
	©2008-2012 The LightBlog Team. All 
	rights reserved. Released under the 
	GNU General Public License 3. For 
	all licensing information, please 
	see the LICENSE.txt document 
	included in this distribution.

***********************************************/

// Include the extra user, database, and string functions
require(ABSPATH .'/Sources/DatabaseFunctions.php');
require(ABSPATH .'/Sources/UserFunctions.php');
require(ABSPATH .'/Sources/StringFunctions.php');

// Open database
$dbh = new SQLiteDatabase( DBH );

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

/*
	Class: PageLoop
	
	Provides an easy way for theme developers to display pages.
*/
class PageLoop {
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
		Function: obtain_page

		Obtains the data for a page from the database.
	*/
	public function obtain_page() {
		# Sanitize and set variables
		$pid = (int)$GLOBALS['pid'];
		$dbh = $this->dbh;
		
		# Query the database for the page data
		$this->result = $dbh->query("SELECT * FROM 'pages' WHERE id=".$pid);
	}

	/*
		Function: has_pages
		
		Checks if the query result we got contained any pages.

		Returns:

			Boolean value (e.g. true/false.)
	*/
  	public function has_pages() {
		# Do we have any pages?
		if(!empty($this->result)) {
			# Convert query results into something usable
			$this->cur_result = $this->result->fetchObject();
			# This while loop will remain true until we run out of pages
			while($post = $this->cur_result) {
				return true;
			}
			# At which point it turns false, ending the loop in the template file
			return false;
		}
		# We don't have any pages :(
		else {
			# Erase our useless query results
			$this->result = null;
			$this->cur_result = null;
			# Send the bad news (aka end the while loop)
			return false;
		}
	}
	
	/*
		Function: permalink
		
		Outputs a permanent link to the page.
	*/
  	public function permalink() {
		# We didn't screw up and keep an empty query, did we?
		if(!empty($this->cur_result)) {
      		echo bloginfo('url', 'return').'?page='.(int)$this->cur_result->id;
		}
		# Oh no, we screwed up :(
		else {
			# Send nothing back
			return false;
		}
  	}

	/*
		Function: title
		
		Outputs the title of the page.
	*/
  	public function title() {
		# We didn't screw up and keep an empty query, did we?
		if(!empty($this->cur_result)) {
      		echo stripslashes($this->cur_result->title);
		}
		# Oh no, we screwed up :(
		else {
			# Send nothing back
			return false;
		}
  	}

	/*
		Function: content
		
		Outputs the content of the page.
	*/
  	public function content() {
		# We didn't screw up and keep an empty query, did we?
		if(!empty($this->cur_result)) {
      		echo stripslashes($this->cur_result->page);
		}
		# Oh no, we screwed up :(
		else {
			# Send nothing back
			return false;
		}
  	}
}

/*
	Class: CommentLoop
	
	Provides an easy method to display a list of comments associated with a post.
*/
class CommentLoop {
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
		Function: comments_open
		
		Checks if comments are enabled.

		Returns:

			Boolean value (e.g. true/false.)
	*/
	public function comments_open() {
		$dbh = $this->dbh;
		$pid = (int)$GLOBALS['pid'];
		$query = $dbh->query("SELECT comments FROM 'posts' WHERE id='$pid'");
		if($query->fetchSingle() == 1) {
			return true;
		}
		else {
			return false;
		}
	}
	
	/*
		Function: obtain_comments

		Obtains the data for comments associated with a post.
	*/
  	public function obtain_comments() {
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
  	public function has_comments() {
		# Do we have any comments?
		if(!empty($this->result)) {
			# Convert query results into something usable
			$this->cur_result = $this->result->fetchObject();
			# This while loop will remain true until we run out of comments
			while($post = $this->cur_result) {
				return true;
			}
			# At which point it turns false, ending the loop in the template file
			return false;
		}
		# We don't have any comments :(
		else {
			# Erase our useless query results
			$this->result = null;
			$this->cur_result = null;
			# Send the bad news (aka end the while loop)
			return false;
		}
  	}
	
	/*
		Function: list_comments
		
		Outputs a list of comments.
		
		Parameters:
		
			tag - The HTML tag to wrap the comment in.
	*/	
	public function list_comments($tag = 'div') {
		if(!empty($this->cur_result)) {
			echo '<'.$tag.' class="comment '; alternateColor('c1', 'c2'); echo '" id="comment-'.(int)$this->cur_result->id.'">
					<img class="comment_gravatar" src="'.$this->gravatar().'" alt="" />';
					if(stripslashes($this->cur_result->website) == '') {
						echo '<span class="comment_name">'.stripslashes($this->cur_result->name).'</span>';
					}
					else {
						echo '<a class="comment_name" href="'.stripslashes($this->cur_result->website).'">'.stripslashes($this->cur_result->name).'</a>';
					}					
					echo '<span class="comment_says"> says:</span><br />
					<a href="'.bloginfo('url','r').'?post='.(int)$this->cur_result->pid.'#comment-'.(int)$this->cur_result->id.'" class="comment_date">'.date('F j, Y \a\t g:i A', (int)$this->cur_result->date).'</a><br />
					<p class="comment_text">'.stripslashes($this->cur_result->text).'</p>
			</'.$tag.'>';
		}
		# Oh no, we screwed up :(
		else {
			# Send nothing back
			return false;
		}	
	}
	
	/*
		Function: gravatar
		
		Outputs the user's Gravatar, derived from their email address.
		
		Parameters:
		
			size - The square pixel size to display the Gravatar (e.g. 32 == 32x32.) Defaults to 32.
	*/
	private function gravatar($size = 32) {
		# We didn't screw up and keep an empty query, did we?
		if(!empty($this->cur_result)) {
			$email = md5($this->cur_result->email);
			$size = (int)$size;
			return "http://www.gravatar.com/avatar.php?gravatar_id=".$email."&amp;size=".$size;
		}
		# Oh no, we screwed up :(
		else {
			# Send nothing back
			return false;
		}	
	}
	
	/*
		Function: messageHook
	
		Outputs a response, if any, from the comment processor.
		
		Parameters:
		
			tag - The HTML tag to wrap the message in.
	*/
	public function messageHook($tag = '') {
		if(isset($_SESSION['cmessage'])) {
			// Get the end tag by removing attributes and adding a slash
			$endtag = explode(' ', str_replace('<', '</', $tag));
			// Put it all together
			echo $tag.$_SESSION['cmessage'].$endtag[0].'>';
			// Destroy the message, we're done with it now
			unset($_SESSION['cmessage']);
		}
		else {
			return false;
		}
	}
	
	/*
		Function: formHook
	
		Injects form inputs required by LightBlog into the comment form.
	*/	
	public function formHook() {
		// Output the post ID as a hidden form input
		echo '<p style="display:none;"><input name="comment_pid" type="hidden" value="'.$GLOBALS['pid'].'" /></p>';
	}
}

/*
	Function: list_pages
	
	Lists pages as HTML list items.
	
	Parameters:
	
		tag - The HTML tag that will contain the link (e.g. li)
		limit - The maximum number of pages to list.
	
	Returns:
	
		HTML list items for however many pages were requested.
*/
function list_pages($tag = 'li', $limit = 5) {
	global $dbh;
	$limit = intval($limit);
	$result = $dbh->query("SELECT * FROM pages ORDER BY id desc LIMIT 0, ".$limit);
	if($result->numRows() > 0) {
		while($pages = $result->fetchObject()) {
			echo '<'.$tag.'><a href="'.bloginfo('url',2).'?page='.$pages->id.'">'.$pages->title.'</a>'.'</'.$tag.'>';
		}
	}
	else {
		echo 'No pages to list.';
	}
}

/*
	Function: list_categories

	Lists categories as HTML list items.

	Parameters:

		tag - The HTML tag that will contain the link (e.g. li or option)
		limit - The maximum number of pages to list.

	Returns:

		HTML list items for however many pages were requested.
*/
function list_categories($tag = 'li', $limit = 5) {
	// Grab the database handle
	global $dbh;
	// Get category data from database
	$result = $dbh->query("SELECT * FROM categories ORDER BY id desc LIMIT 0, ".$limit) or die(sqlite_error_string($dbh->lastError));
	// What tag are we using?
	if($tag == 'option') {
		// Sort through and create list items
		while($row = $result->fetchObject()) {
			echo '<option value="'.$row->shortname.'">'.stripslashes($row->fullname).'</option>';
		}
	}
	else {
		// Sort through and create list items
		while($row = $result->fetchObject()) {
			echo '<li><a href="'.bloginfo('url','r').'?category='.(int)$row->id.'">'.stripslashes($row->fullname).'</a></li>';
		}
	}

}

/*
	Function: list_archives
	
	Outputs a multi-level HTML list containing links for monthly post archives.
*/
function list_archives($limit = 10) {
	// Grab the database handle
	global $dbh;
	// Get archive data
	$result = $dbh->query("SELECT date FROM posts WHERE published=1 ORDER BY id desc LIMIT 0, ".(int)$limit);
	// Sort through and create list items
	while($row = $result->fetchObject()) {
		$month = date('m', $row->date);
		$monthname = date('F', $row->date);
		$year = date('Y', $row->date);
		if(!isset($post[$year][$month])) {
			echo "<li><a href=\"".bloginfo('url','r')."?archive=".$year.$month."\">".$monthname." ".$year."</a></li>";
		}
		$post[$year][$month] = true;
	}
}

/*
	Function: commentNum
	
	Outputs the number of comments on a post.
	
	Parameters:

		output - Specifies whether to echo the number or return it. Default is to return it.
		
	Returns:
	
		The number of comments as an integer.
*/
function commentNum($id, $output = 'r') {
	// Make the database handle available here
	global $dbh;
	// If it's null, use the global
	if($id == null) $id = $GLOBALS['pid'];
	// Set the query
	$query = $dbh->query("SELECT COUNT(*) FROM comments WHERE published=1 AND pid=".(int)$id) or die(sqlite_error_string($dbh->lastError));
	// Query the database
	@list($commentnum) = $query->fetch(SQLITE_NUM);
	// Return or echo data
	if($output == 'e') {
		echo $commentnum;
	}
	else {
		return $commentnum;
	}
}

/*
	Function: alternateColor
	
	Alternates colors using CSS classes. Technically, it could alternate anything.
	
	Parameters:
	
		class1 - Name of the first class.
		class2 - Name of the second class.
		
	Returns:
	
		The appropriate class name.
*/
function alternateColor($class1, $class2) {
	# If $count isn't set, set it as 1
	if(!isset($count)) { $count = 1; }
	# Make PHP remember $count
	static $count;
	# Is it odd or even?
	if($count % 2 == 0) {
		# It's even!
		echo $class1;
	}
	else {
		# It's odd...
		echo $class2;
	}
	# Increase $count by 1 for next time
	$count++;
}

?>
