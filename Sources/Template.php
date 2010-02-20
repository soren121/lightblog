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

// Open database
$dbh = new SQLiteDatabase( DBH );

// Set default timezone
date_default_timezone_set('UTC');

// Bloginfo function
// Retrieves general info stored in core
function bloginfo($var, $output = 'e') {
	# Global the database handle
	global $dbh;
	# Make PHP remember $bloginfo next time
	static $bloginfo = null;
	# If this is the first time bloginfo's been called...
	if($bloginfo == null) {
		# Fetch all the info from our core
		$result = $dbh->query('SELECT * FROM core') or die(sqlite_error_string($dbh->lastError));
		# Let's make an array!
		$bloginfo = array();
		# For each row, set a key with the value
		while($row = $result->fetchObject()) {
			$bloginfo[$row->variable] = $row->value;
		}
		// Set the theme URL
		$bloginfo['themeurl'] = $bloginfo['url'].'themes/'.$bloginfo['theme'];
	}   		
	# Are we echoing or returning?
	if($output == 'e') { echo !empty($bloginfo[$var]) ? $bloginfo[$var] : false; }
	# Looks like we're returning...
	else { return !empty($bloginfo[$var]) ? $bloginfo[$var] : false; }	
}

// Function to undo Magic Quotes in strings
function unescapeString($str) {
	# Is Magic Quotes on?
	if(function_exists('magic_quotes_gpc') && magic_quotes_gpc() == 1) {
		# It is, so undo its filthy mess
		return stripslashes(stripslashes($str));
	}
	else {
		# Magic Quotes is off, so leave it as is
		return stripslashes($str);
	}
}

// The PostLoop class, which loops through posts for display
class PostLoop {
	# Set private database variables
	private $dbh = null;
	private $result = null;
	private $cur_result = null;
	
	# Set database handle for all functions in our class
	public function __construct() {
    	$this->set_dbh($GLOBALS['dbh']);
  	}

	# Function for setting database handle
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
	
	# Function to obtain single post
	public function obtain_post($pid) {
		# Sanitize and set variables
		$pid = (((int)$pid) -1);
		$dbh = $this->dbh;
		
		# Query the database for the post data
		$this->result = $dbh->query("SELECT * FROM 'posts' LIMIT ".$pid.", 1");
	}
	
	# Function to obtain multiple posts, depending on page and limit
  	public function obtain_posts($start = 0, $limit = 10) {
		# Sanitize and set variables
    	$start = (int)$start;
    	$limit = (int)$limit;
    	$start = $start * $limit;
		$dbh = $this->dbh;

		# Query the database for post data
    	$this->result = $dbh->query("SELECT * FROM 'posts' ORDER BY id desc LIMIT ".$start.", ".$limit);
  	}

	# Loop function to check for posts
  	public function has_posts() {
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
			# Erase our useless query results
      		$this->result = null;
      		$this->cur_result = null;
      		# Send the bad news (aka end the while loop)
      		return false;
    	}
  	}

	# Function to echo the post's permalink
  	public function permalink() {
		# We didn't screw up and keep an empty query, did we?
    	if(!empty($this->cur_result)) {
			# Nope, so return the post's permalink
      		echo bloginfo('url', 'return'). 'post.php?id='. $this->cur_result->id;
      	}
    	else {
			# Looks like we messed up, send nothing
      		return false;
      	}
  	}

	# Function to echo the post's title
  	public function title() {
		# We didn't screw up and keep an empty query, did we?
    	if(!empty($this->cur_result)) {
			# Nope, so remove all sanitation and echo it out
      		echo unescapeString($this->cur_result->title);
      	}
    	else {
			# Looks like we messed up, send nothing
      		return false;
      	}
  	}
	
	# Function to output the full content of a post and excerpts
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
				if(mb_strlen(unescapeString($this->cur_result->post)) > $length) {
					# Echo the shortened post content along with our suffix
      				echo mb_substr(unescapeString($this->cur_result->post), 0, $length).' ... <a href="post.php?id='. $this->cur_result->id.'">'.$excerpt.'</a>';
				}
				# It's short enough already, unsanitize & echo it now
				else { echo unescapeString($this->cur_result->post); }
			}
			# Looks like we're echoing the full post
			else {
				# Unsanitize it and echo it
				echo unescapeString($this->cur_result->post);
			}
		}
		# Oh no, we screwed up :(
    	else {
			# Send nothing back
      		return false;
		}
  	}

	# Function to output a post's creation date
  	public function date($format = null) {
    	if(!empty($this->cur_result))
      		echo date(!empty($format) ? $format : 'F jS, Y', $this->cur_result->date);
    	else
      		return false;
  	}

  	public function author() {
    	if(!empty($this->cur_result))
      		echo $this->cur_result->author;
    	else
      		return false;
  	}

	public function commentNum() {
		if(!empty($this->cur_result))
			return commentNum($this->cur_result->id);
		else
			return false;
	}
}

class PageLoop {
	private $dbh = null;
	private $result = null;
	private $cur_result = null;

	public function __construct() {
    	$this->set_dbh($GLOBALS['dbh']);
  	}

  	public function set_dbh($dbh) {
    	if(is_object($dbh) && $dbh instanceof SQLiteDatabase)
      		$this->dbh = $dbh;
    	else
      		trigger_error('Invalid object supplied.');
  	}

	public function obtain_page($pid) {
		$pid = (((int)$pid) -1);
		$dbh = $this->dbh;
		
		$this->result = $dbh->query("SELECT * FROM 'pages' LIMIT ".$pid.", 1");
	}

  	public function obtain_pages($start = 0, $limit = 10) {
    	$start = (int)$start;
    	$limit = (int)$limit;
    	$start = $start * $limit;
		$dbh = $this->dbh;

    	$this->result = $dbh->query("SELECT * FROM 'pages' ORDER BY id desc LIMIT ".$start.", ".$limit);
  	}

  	public function has_pages() {
    	if(!empty($this->result)) {
      		$this->cur_result = $this->result->fetchObject();
			while($page = $this->cur_result) {
				return true;
			}
      		return false;
    	}
   		else {
      		$this->result = null;
      		$this->cur_result = null;
      		return false;
    	}
  	}

  	public function permalink() {
    	if(!empty($this->cur_result))
      		echo bloginfo('url', 'return'). 'page.php?id='. $this->cur_result->id;
    	else
      		return false;
  	}

  	public function title() {
    	if(!empty($this->cur_result))
      		echo unescapeString($this->cur_result->title);
    	else
      		return false;
  	}

  	public function page() {
    	if(!empty($this->cur_result))
      		echo unescapeString($this->cur_result->page);
    	else
      		return false;
  	}

  	public function date($format = null) {
    	if(!empty($this->cur_result))
      		echo date(!empty($format) ? $format : 'F jS, Y', $this->cur_result->date);
    	else
      		return false;
  	}

  	public function author() {
    	if(!empty($this->cur_result))
      		echo $this->cur_result->author;
    	else
      		return false;
  	}
}

class CommentLoop {
	private $dbh = null;
	private $result = null;
	private $cur_result = null;

	public function __construct() {
    	$this->set_dbh($GLOBALS['dbh']);
  	}

  	public function set_dbh($dbh) {
    	if(is_object($dbh) && $dbh instanceof SQLiteDatabase)
      		$this->dbh = $dbh;
    	else
      		trigger_error('Invalid object supplied.');
  	}

  	public function obtain_comments($pid) {
		$dbh = $this->dbh;
		$pid = (int)$pid;
    	$this->result = $dbh->query("SELECT * FROM 'comments' WHERE pid='$pid'");
  	}

  	public function has_comments() {
    	if(!empty($this->result)) {
      		$this->cur_result = $this->result->fetchObject();
			while($post = $this->cur_result) {
				return true;
			}
      		return false;
    	}
   		else {
      		$this->result = null;
      		$this->cur_result = null;
      		return false;
    	}
  	}

	public function comment() {
		if(!empty($this->cur_result))
			echo unescapeString($this->cur_result->text);
		else
			return false;
	}

  	public function name() {
    	if(!empty($this->cur_result))
      		echo $this->cur_result->name;
    	else
      		return false;
  	}

  	public function website() {
    	if(!empty($this->cur_result))
      		echo unescapeString($this->cur_result->website);
    	else
      		return false;
  	}

  	public function date($format = null) {
    	if(!empty($this->cur_result))
      		echo date(!empty($format) ? $format : 'F jS, Y', $this->cur_result->date);
    	else
      		return false;
  	}

	public function id() {
		if(!empty($this->cur_result))
			echo $this->cur_result->id;
		else
			return false;
	}
	
	public function gravatar($size = 32) {
		if(!empty($this->cur_result)) {
			$email = md5($this->cur_result->email);
			$size = (int)$size;
			echo "http://www.gravatar.com/avatar.php?gravatar_id=".$email."&amp;size=".$size;
		}
		else {
			return false;	
		}		
	}
}

function list_pages($start_tag = '<li>', $end_tag = '</li>', $limit = 5) {
	global $dbh;
	$limit = intval($limit);
	$result = $dbh->query("SELECT * FROM pages ORDER BY id desc LIMIT 0, ".$limit);
	while($pages = $result->fetchObject()) {
		echo $start_tag.'<a href="'.bloginfo('url',2).'page.php?id='.$pages->id.'">'.$pages->title.'</a>'.$end_tag;
	}
}

// Function to list categories
function list_categories($tag) {
	// Grab the database handle
	global $dbh;
	// Get category data from database
	$result = $dbh->query("SELECT * FROM categories") or die(sqlite_error_string($dbh->lastError));
	// What tag are we using?
	if($tag == 'option') {
		$arg = 'value="'.$row->shortname.'"';
	}
	// Sort through and create list items
	while($row = $result->fetchObject()) {
		echo '<'.$tag.' '.$arg.'>'.unescapeString($row->fullname).'</'.$tag.'>';
	}
}

# Function to return simple pagination links
function simplePagination($type, $target, $page = 1, $limit = 6, $pagestring = "?page=") {
	# Global the database handle so we can use it in this function
	global $dbh;
	# Set defaults
	if(!$limit) $limit = 6;
	if(!$page) $page = 1;
	# Set the query to retrieve the number of rows
	$query = $dbh->query("SELECT COUNT(*) FROM ".sqlite_escape_string($type)."s") or die(sqlite_error_string($dbh->lastError));
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
		# Add the previous link
		if($page > 1) {
			$pagination .= "<a href=\"" . $target . $pagestring . $prev . "\" class=\"prev\">&laquo; Older Posts</a>";
		}
		# Add the next link
		if($page < $lastpage) {
			$pagination .= "<a href=\"" . $target . $pagestring . $next . "\" class=\"next\">Newer Posts &raquo;</a>";
		}
	}
	# Return the links! Duh!
	echo $pagination;
}

// Function for identifying the number of comments
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

// Function to alternate row colors
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

// Function to correct plurals and such on dynamic numbers, mainly comment numbers
function grammarFix($number, $singular, $plural) {
	if($number == 1) {
		// The number is 1, so we will use the singular form of the word
		echo $number.' '.$singular;
	}
	else {
		// The number is something other than 1, so we'll use the plural form
		echo $number.' '.$plural;
	}
}

?>
