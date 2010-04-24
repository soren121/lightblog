<?php

/***********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	Sources/Template.php
	
	©2009-2010 The LightBlog Team. All 
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

// Set default timezone
date_default_timezone_set('UTC');

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
		
			where - Optional parameter. You can use this to add an extra condition to the WHERE selector in the query.

		Returns:
		
			A complete SQL query.
	*/
	private function parseQuery($where = '') {
		// Get the view type
		$querytype = $GLOBALS['postquery']['type'];
		if($querytype == 'archive') {
			$queryextra = substr_replace((int)$GLOBALS['postquery']['date'], '-', 4, 0);
			$queryextra = explode('-', $queryextra);
			$firstday = mktime(0, 0, 0, $queryextra[1], 1, $queryextra[0]);
			$lastday = mktime(0, 0, 0, ($queryextra[1] + 1), 0, $queryextra[0]);
			return "SELECT * FROM posts WHERE date BETWEEN $firstday AND $lastday AND published='1' $where ORDER BY id desc";
		}
		elseif($querytype == 'category') {
			$queryextra = (int)$GLOBALS['postquery']['catid'];
				return "SELECT * FROM posts WHERE category=$queryextra AND published='1' $where ORDER BY id desc";
		}
		else {
			return "SELECT * FROM posts WHERE published='1' $where ORDER BY id desc";
		}
	}
	
	/*
		Function: obtain_post
		
		Obtains the data for a single post from the database.
		
		Parameters:
		
			pid - The post's ID.
	*/
	public function obtain_post($pid) {
		# Sanitize and set variables
		$pid = (int)$pid;
		$dbh = $this->dbh;
		
		# Query the database for the post data
		$this->result = $dbh->query($this->parseQuery("AND id=$pid"));
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
		
			excerpt - The excerpt suffix. If set, this function will output an excerpt. (e.g. Read More...)
	*/
  	public function content($excerpt = '') {
		# We didn't screw up and keep an empty query, did we?
    	if(!empty($this->cur_result)) {
			# Was an excerpt suffix specified?
			if($excerpt !== '') {
				# Let's set a default length
				$length = 360;
				# Open FunctionReplacements incase the mb_ functions aren't available
				include_once(ABSPATH .'/Sources/FunctionReplacements.php');
				# Take out any ellipsises
				$length -= mb_strlen('...');
				# Do we need to shorten the post?
				if(mb_strlen(stripslashes($this->cur_result->post)) > $length) {
					# Echo the shortened post content along with our suffix
      				echo mb_substr(stripslashes($this->cur_result->post), 0, $length).' ... <a href="post.php?id='. $this->cur_result->id.'">'.$excerpt.'</a>';
				}
				# It's short enough already, unsanitize & echo it now
				else { echo stripslashes($this->cur_result->post); }
			}
			# Looks like we're echoing the full post
			else {
				# Unsanitize it and echo it
				echo stripslashes($this->cur_result->post);
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

		Parameters:

			pid - The page's ID.
	*/
	public function obtain_page($pid) {
		# Sanitize and set variables
		$pid = (((int)$pid) -1);
		$dbh = $this->dbh;
		
		# Query the database for the page data
		$this->result = $dbh->query("SELECT * FROM 'pages' LIMIT ".$pid.", 1");
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
		Function: obtain_comments

		Obtains the data for comments associated with a post.

		Parameters:

			pid - The post ID associated with the comment.
	*/
  	public function obtain_comments($pid) {
		$dbh = $this->dbh;
		$pid = (int)$pid;
    	$this->result = $dbh->query("SELECT * FROM 'comments' WHERE pid='$pid'");
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
		Function: content

		Outputs the content of the comment.
	*/
	public function content() {
		# We didn't screw up and keep an empty query, did we?
		if(!empty($this->cur_result)) {
			echo stripslashes($this->cur_result->text);
		}
		# Oh no, we screwed up :(
		else {
			# Send nothing back
			return false;
		}
	}

	/*
		Function: name
		
		Outputs the comment author's name.
	*/
  	public function name() {
		# We didn't screw up and keep an empty query, did we?
		if(!empty($this->cur_result)) {
      		echo stripslashes($this->cur_result->name);
		}
		# Oh no, we screwed up :(
		else {
			# Send nothing back
			return false;
		}
  	}

	/*
		Function: website
		
		Outputs the comment author's website URL, if specified.
	*/
  	public function website() {
		# We didn't screw up and keep an empty query, did we?
		if(!empty($this->cur_result)) {
      		echo stripslashes($this->cur_result->website);
		}
		# Oh no, we screwed up :(
		else {
			# Send nothing back
			return false;
		}
  	}

	/*
		Function: date

		Outputs the submittal date of the comment.

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
		Function: id
		
		Outputs the ID of the comment.
	*/
	public function id() {
		# We didn't screw up and keep an empty query, did we?
		if(!empty($this->cur_result)) {
			echo (int)$this->cur_result->id;
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
	public function gravatar($size = 32) {
		# We didn't screw up and keep an empty query, did we?
		if(!empty($this->cur_result)) {
			$email = md5($this->cur_result->email);
			$size = (int)$size;
			echo "http://www.gravatar.com/avatar.php?gravatar_id=".$email."&amp;size=".$size;
		}
		# Oh no, we screwed up :(
		else {
			# Send nothing back
			return false;
		}	
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
	Function: simplePagination
	
	Paginates a list of data using simple Previous/Next links.
	
	Parameters:
	
		type - Type of content (e.g. post.)
		target - Base URL of executing file (e.g. http://localhost/lighty/.)
		page - The current page.
		limit - Number of items on a single page.
		pagestring - Argument string for our page GET.
		
	Returns:
	
		Previous/Next HTML anchor links when applicable.
*/
function simplePagination($type, $target, $page = 1, $limit = 8, $pagestring = "?p=") {
	# Global the database handle so we can use it in this function
	global $dbh;
	# Set the query to retrieve the number of rows
	$query = $dbh->query("SELECT COUNT(*) FROM ".sqlite_escape_string($type)."s WHERE published=1") or die(sqlite_error_string($dbh->lastError));
	# Query the database
	@list($totalitems) = $query->fetch(SQLITE_NUM);	
	# Set various required variables
	$prev = $page - 1;						# Previous page is page - 1
	$next = $page + 1;						# Next page is page + 1
	$lastpage = ceil($totalitems/$limit);	# Last page is = total items / items per page, rounded up.	
	# Clear $pagination
	$pagination = "";
	# Do we have more than one page?
	if($totalitems > $limit) {
		# Add the next link
		if($page > 1) {
			$pagination .= "<a href=\"" . $target . $pagestring . $prev . "\" class=\"next\">Newer Posts &raquo;</a>";
		}
		# Add the previous link
		if($page < $lastpage) {
			$pagination .= "<a href=\"" . $target . $pagestring . $next . "\" class=\"prev\">&laquo; Older Posts</a>";
		}
	}
	# Return the links! Duh!
	echo $pagination;
}

/*
	Function: commentNum
	
	Outputs the number of comments on a post.
	
	Parameters:
	
		id - The ID of the post that the comments are associated with.
		output - Specifies whether to echo the number or return it. Default is to return it.
		
	Returns:
	
		The number of comments as an integer.
*/
function commentNum($id, $output = 'r') {
	// Make the database handle available here
	global $dbh;
	// Set the query
	$query = $dbh->query("SELECT COUNT(*) FROM comments WHERE pid=".(int)$id) or die(sqlite_error_string($dbh->lastError));
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
