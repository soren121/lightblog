<?php

/***********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	Sources/Template.php
	
	©2009 soren121. All rights reserved.
	Released under the GNU General
	Public License. For all licensing
	information, please see the
	LICENSE.txt document included in this
	distribution.

***********************************************/

// Open database
$dbh = new SQLiteDatabase( DBH );

// Bloginfo function
// Retrieves general info stored in core
function bloginfo($var, $output = 'e') {
	# Global the database handle
	global $dbh;
	# Make PHP remember $bloginfo next time
	static $bloginfo = null;
	# If this is the first time bloginfo's been called...
	if($bloginfo == null) {
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

// Function to create text excerpts
function substrws($text, $length = 180) {
	// Make the excerpt!
	if((mb_strlen($text) > $length)) {
		$wsposition = mb_strpos($text, "", $length)-1;
		if($wsposition > 0) {
			$chars = count_chars(mb_substr($text, 0, ($wsposition+1)), 1);
			if($chars[ord('<')] > $chars[ord('>')]) {
				$wsposition = mb_strpos($text, '>', $wsposition)-1;
			}				
			$text = mb_substr($text, 0, ($wsposition+1));
	}
	// Close unclosed tags
	if(preg_match_all("|<([a-zA-Z]+)|", $text, $aBuffer)) {
		if(!empty($aBuffer[1])) {
			preg_match_all("|</([a-zA-Z]+)>|", $text, $aBuffer2);
				if(count($aBuffer[1]) != count($aBuffer2[1])) {
					foreach($aBuffer[1] as $index => $tag) {
						if(empty($aBuffer2[1][$index]) || $aBuffer2[1][$index] != $tag) {
							$text .= '</'.$tag.'>';
						}
					}
				}
			}
		}
	}
	return $text;
}

class PostLoop {
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

  	public function obtain_posts($start = 0, $limit = 10) {
    	$start = (int)$start;
    	$limit = (int)$limit;
    	$start = $start * $limit;
		$dbh = $this->dbh;

    	$this->result = $dbh->query("SELECT * FROM 'posts' ORDER BY id desc LIMIT ".$start.", ".$limit);
  	}

  	public function has_posts() {
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

  	public function permalink() {
    	if(!empty($this->cur_result))
      		echo bloginfo('url', 'return'). 'post.php?id='. $this->cur_result->id;
    	else
      		return false;
  	}

  	public function title() {
    	if(!empty($this->cur_result))
      		echo unescapeString($this->cur_result->title);
    	else
      		return false;
  	}

  	public function post() {
    	if(!empty($this->cur_result))
      		echo unescapeString($this->cur_result->post);
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

	public function commentNum() {
		if(!empty($this->cur_result))
			commentNum($this->cur_result->id);
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
	$result = $dbh->query("SELECT * FROM pages ORDER BY id desc LIMIT $limit");
	while($pages = $result->fetchObject()) {
		echo $start_tag.'<a href="'.bloginfo('url',2).'page.php?id='.$pages->id.'">'.$pages->title.'</a>'.$end_tag;
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
function commentNum($id, $output = 'e') {
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

?>