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

class PostLoop {
  private $dbh = null;
  private $result = null;
  private $cur_result = null;

  public function __construct()
  {
    	$this->set_dbh($GLOBALS['dbh']);
  }

  public function set_dbh($dbh)
  {
    if(is_object($dbh) && $dbh instanceof SQLiteDatabase)
      $this->dbh = $dbh;
    else
      trigger_error('Invalid object supplied.');
  }

  public function obtain_posts($start = 0, $limit = 10)
  {
    $start = (int)$start;
    $limit = (int)$limit;
    $start = $start * $limit;
		$dbh = $this->dbh;

    $this->result = $dbh->query("SELECT * FROM 'posts' ORDER BY id desc LIMIT ".$start.", ".$limit) or die('aww...');
  }

  public function has_posts()
  {
    if(!empty($this->result))
    {
      $this->cur_result = $this->result->fetchObject();
      echo true;
    }
    else
    {
      $this->result = null;
      $this->cur_result = null;
      return false;
    }
  }

  public function permalink()
  {
    if(!empty($this->cur_result))
      echo bloginfo('url', 'return'). 'post.php?id='. $this->cur_result->id;
    else
      return false;
  }

  public function title()
  {
    if(!empty($this->cur_result))
      echo unescapeString($this->cur_result->title);
    else
      return false;
  }

  public function post()
  {
    if(!empty($this->cur_result))
      echo unescapeString($this->cur_result->post);
    else
      return false;
  }

  public function excerpt($length = 10, $trailing = '...')
  {
	if(!empty($this->cur_result))
	  $length-= mb_strlen($trailing);
	  if(mb_strlen($this->cur_result->post) > $length) {
		return mb_substr($this->cur_result->post, 0, $length).$trailing;
	  }
	  else {
		return $this->cur_result->post;
	  }
  }

  public function date($format = null)
  {
    if(!empty($this->cur_result))
      echo date(!empty($format) ? $format : 'F jS, Y', $this->cur_result->date);
    else
      return false;
  }

  public function author()
  {
    if(!empty($this->cur_result))
      echo $this->cur_result->author;
    else
      return false;
  }
}

function list_pages($start_tag = '<li>', $end_tag = '</li>', $limit = 5) {
	global $dbh;
	$limit = intval($limit);
	$result = $dbh->query("SELECT * FROM pages ORDER BY id desc LIMIT $limit");
	while($pages = $result->fetchObject()) {
		echo $start_tag.'<a href="'.bloginfo('url',2).'post.php?id='.$pages->id.'">'.$pages->title.'</a>'.$end_tag;
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

?>