<?php

/***********************************************

	LightBlog 0.9
	SQLite blogging platform

	Sources/Core.php

	©2008-2012 The LightBlog Team. All
	rights reserved. Released under the
	GNU General Public License 3. For
	all licensing information, please
	see the LICENSE.txt document
	included in this distribution.

***********************************************/

// Let's make sure that LightBlog has been installed.
if(!file_exists(dirname(__FILE__). '/../config.php'))
{
	// Is there an install.php file?
	if(file_exists(dirname(__FILE__). '/../install.php'))
	{
		// We will need to find the installer's relation to where we're
		// currently at.
		$filename = $_SERVER['SCRIPT_FILENAME'];

		$try_count = 0;
		while(strlen($filename) > 0 && !file_exists(($filename = dirname($filename)). '/install.php') && $try_count++ < 3);

		// Redirect to the installer, then.
		header('HTTP/1.1 307 Temporary Redirect');
		header('Location: '. str_repeat('../', $try_count). 'install.php');

		exit;
	}
	else
	{
		die('LightBlog Error: config.php file missing (no install.php).');
	}
}
else
{
	// Include the config.php file, we need it!
	require(dirname(__FILE__). '/../config.php');
}

// Check to make sure that the database exists.
if(file_exists(DBH))
{
	$dbh = new PDO('sqlite2:'.DBH);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$dbh->setAttribute(PDO::ATTR_TIMEOUT, 5);
}
else
{
	$error_message = 'The database file does not exist';
}

if(!empty($error_message))
{
	trigger_error($error_message, E_USER_ERROR);
}

// Include the extra user, database, and string functions
require(ABSPATH. '/Sources/Errors.php');
require(ABSPATH. '/Sources/CleanRequest.php');
require(ABSPATH. '/Sources/FunctionReplacements.php');
require(ABSPATH .'/Sources/DatabaseFunctions.php');
require(ABSPATH. '/Sources/User.php');
require(ABSPATH .'/Sources/UserFunctions.php');
require(ABSPATH .'/Sources/StringFunctions.php');
require(ABSPATH. '/Sources/Language.php');

// Start up our session.
session_start();

// Now output buffering, too. With compression, if supported.
/*if(function_exists('ob_gzhandler') && (get_bloginfo('disable_compression') === false || get_bloginfo('disable_compression') == 0))
{
	ob_start('ob_gzhandler');
}
else
{
	ob_start();
}*/

/*
	Function: LightyVersion

	Returns the installed version number of LightBlog.

	Parameters:

		output - Specifies whether the version will be echoed or returned.

	Returns:

		The installed version number.
*/
function LightyVersion($output = 'e') {
	# DON'T TOUCH!
	$version = '0.9.4';
	# Are we echoing or returning?
	if($output == 'e') { echo $version; }
	# Returning!
	else { return $version; }
}

/*
	Function: dirlist

	Reads a directory and outputs its directories into a sorted array.

	Parameters:

		input - The path of the directory to inspect.

	Returns:

		An array sorted in ascending order by values containing the directories in the given path.
*/
function dirlist($input) {
	# Start foreach loop and set search pattern
	foreach(glob($input.'/*', GLOB_ONLYDIR) as $dir) {
		# Remove the containing directory
		$dir = str_replace($input.'/', '', $dir);
		# Place directories in an array
		$array[$dir] = ucwords(strtolower($dir));
	}
	# Sort the array into ascending order by values
	asort($array);
	# Return it!
	return $array;
}

function currentURL() {
	$pageURL = 'http';
	if($_SERVER["HTTPS"] == "on") {
		$pageURL .= "s";
	}
	$pageURL .= "://";
	if($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	}
	else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}

/*
	Function: advancedPagination

	Creates a more advanced pagination that is more efficient for handling large amounts of data than <simplePagination>.

	Parameters:

		type - Type of content being processed.
		target - URL of the page that the pagination will be displayed on.
		page - The page the user is currently on.
		limit - Defines how many items are in a page.
		adjacents - Number of items in the pagination on either side of the current page? (not entirely sure)
		pagestring - GET argument to be used for the current page.

	Returns:

		HTML code for a full pagination menu.
*/
function advancedPagination($type, $target, $page = 1, $limit = 8, $adjacents = 1, $pagestring = "&page=") {
	// Global the database handle so we can use it in this function
	global $dbh;

	// The page cannot be less than 1.
	if($page < 1)
	{
		$page = 1;
	}

	// Set defaults
	if(!$adjacents) $adjacents = 1;
	if(!$limit) $limit = 8;
	if(!$page) $page = 1;

	// Set teh query to retrieve the number of rows
	$query = $dbh->query("
		SELECT
			COUNT(*)
		FROM '".sqlite_escape_string($type)."'");

	// Query the database
	@list($totalitems) = $query->fetch(PDO::FETCH_NUM);

	// Set various required variables
	$prev = $page - 1;						// Previous page is page - 1
	$next = $page + 1;						// Next page is page + 1
	$lastpage = ceil($totalitems/$limit);	// Last page is = total items / items per page, rounded up.
	$lpm1 = $lastpage - 1;					// Last page minus 1

	// The page also cannot exceed the last page.
	if($page > $lastpage)
	{
		$page = $lastpage;
	}

	// Clear $pagination
	$pagination = "";
	// Do we have more than one page?
	if($totalitems > $limit)
	{
		// Start the pagination div
		$pagination .= "<div class=\"pagination\">";

		// Add the previous button
		if($page > 1)
		{
			$pagination .= "<a href=\"" . $target . $pagestring . $prev . "\">&laquo; prev</a>";
		}
		else
		{
			// Disable the previous button, since we're on the first page
			$pagination .= "<span class=\"disabled\">&laquo; prev</span>";
		}

		// Add the page buttons
		if ($lastpage < 7 + ($adjacents * 2))
		{
			// There aren't enough pages to bother breaking it up
			// Loop through the pages and create links for all
			for($counter = 1; $counter <= $lastpage; $counter++)
			{
				if($counter == $page)
				{
					$pagination .= "<span class=\"current\">$counter</span>";
				}
				else
				{
					$pagination .= "<a href=\"" . $target . $pagestring . $counter . "\">$counter</a>";
				}
			}
		}
		elseif($lastpage >= 7 + ($adjacents * 2))
		{
			// We have enough pages to hide some of them now
			if($page < 1 + ($adjacents * 3))
			{
				// Start a loop and create the first few pages
				for($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if($counter == $page)
					{
						$pagination .= "<span class=\"current\">$counter</span>";
					}
					else
					{
						$pagination .= "<a href=\"" . $target . $pagestring. $counter . "\">$counter</a>";
					}
				}

				// Add the ellipses
				$pagination .= "<span class=\"elipses\">...</span>";
				$pagination .= "<a href=\"" . $target . $pagestring . $lpm1 . "\">$lpm1</a>";
				$pagination .= "<a href=\"" . $target . $pagestring . $lastpage . "\">$lastpage</a>";
			}
			// We're in the middle; hide some in the front and back
			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
			{
				// Add the first two links
				$pagination .= "<a href=\"" . $target . $pagestring . "1\">1</a>";
				$pagination .= "<a href=\"" . $target . $pagestring . "2\">2</a>";

				// Add the ellipses
				$pagination .= "<span class=\"elipses\">...</span>";

				// Start the for loop to make the page links
				for($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
				{
					if($counter == $page)
					{
						$pagination .= "<span class=\"current\">$counter</span>";
					}
					else
					{
						$pagination .= "<a href=\"" . $target . $target . $pagestring . $counter . "\">$counter</a>";
					}
				}

				// Add the ellipses and the last few pages
				$pagination .= "...";
				$pagination .= "<a href=\"" . $target . $pagestring . $lpm1 . "\">$lpm1</a>";
				$pagination .= "<a href=\"" . $target . $pagestring . $lastpage . "\">$lastpage</a>";
			}
			// We're close to the end, so only hide the early pages
			else
			{
				// Add the first few pages
				$pagination .= "<a href=\"".$target.$pagestring."1\">1</a>";
				$pagination .= "<a href=\"".$target.$pagestring."2\">2</a>";

				// Add the ellipses
				$pagination .= "<span class=\"elipses\">...</span>";
				for ($counter = $lastpage - (1 + ($adjacents * 3)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $page)
					{
						$pagination .= "<span class=\"current\">$counter</span>";
					}
					else
					{
						$pagination .= "<a href=\"".$target.$pagestring.$counter."\">$counter</a>";
					}
				}
			}
		}
		// Add the next button
		if ($page < $counter - 1)
		{
			$pagination .= "<a href=\"".$target.$pagestring.$next."\">next &raquo;</a>";
		}
		else
		{
			$pagination .= "<span class=\"disabled\">next &raquo;</span>";
		}

		// End the pagination div
		$pagination .= "</div>\n";
	}

	// Return the final pagination div
	return $pagination;
}

/*
	Function: list_themes

	Outputs a list of themes in HTML <option> tags.
*/
function list_themes()
{
	// List directories
	$dir = dirlist(ABSPATH .'/themes');

	foreach($dir as $k => $v)
	{
		echo '<option value="'. utf_htmlspecialchars($k). '"'. (get_bloginfo('theme') == $k ? ' selected="selected"' : ''). '>'. utf_htmlspecialchars($v). '</option>';
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
function list_pages($tag = 'li', $limit = 5)
{
	global $dbh;

	$query_c = "
		SELECT
			COUNT(*)
		FROM pages
		ORDER BY page_id DESC
		LIMIT 0, ". ((int)$limit);
		
	$result = $dbh->query("
		SELECT
			*
		FROM pages
		ORDER BY page_id DESC
		LIMIT 0, ". ((int)$limit));	

	if(count_rows($query_c))
	{
		while($page = $result->fetchObject())
		{
			echo '<'. $tag. '><a href="'. get_bloginfo('url'). '?page='. $page->page_id. '">'. $page->page_title. '</a></'. $tag. '>';
		}
	}
	else
	{
		echo '<'. $tag. '>No pages.</'. $tag. '>';
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
function list_categories($tag = 'li', $limit = 5, $selected = null)
{
	// Grab the database handle
	global $dbh;

	// Is there a limit? If so, typecast it and add it to the query
	if($limit != null)
	{
		$limit = "LIMIT 0, ".(int)$limit;
	}

	// Get category data from database
	$result = $dbh->query("
		SELECT
			*
		FROM categories
		ORDER BY category_id DESC
		".$limit);

	// What tag are we using?
	if($tag == 'option')
	{
		while($row = $result->fetchObject())
		{
			echo '<option value="'. $row->category_id. '"'. ($row->category_id == $selected ? ' selected="selected"' : ''). '>'. $row->full_name. '</option>';
		}
	}
	else
	{
		// Sort through and create list items
		while($row = $result->fetchObject())
		{
			echo '<'. $tag. '><a href="'. get_bloginfo('url'). '?category='. $row->category_id. '">'. $row->full_name .'</a></'. $tag. '>';
		}
	}

}

/*
	Function: list_archives

	Outputs a multi-level HTML list containing links for monthly post archives.
*/
function list_archives($limit = 10)
{
	// Grab the database handle
	global $dbh;

	// Get archive data
	$result = $dbh->query(
		"SELECT
			strftime('%m', post_date, 'unixepoch') AS 'month',
			strftime('%Y', post_date, 'unixepoch') AS 'year',
			post_date
		FROM posts
		WHERE published != 0
		GROUP BY month");

	// Sort through and create list items
	$i = 0;
	$return = '';
	while($row = $result->fetchObject())
	{
		$month = $row->month;
		$monthname = date('F', $row->post_date);
		$year = $row->year;

		$return .= '<li><a href="'. get_bloginfo('url'). '?archive='. $year. $month.'">'. $monthname. ' '. $year. '</a></li>';

		$i++;
		if($i >= $limit)
		{
			break;
		}
	}
	echo $return;
}

/*
	Function: get_commentnum

	Outputs the number of comments on a post.

	Returns:

		The number of comments as an integer.
*/
function get_commentnum($id)
{
	// Make the database handle available here
	global $dbh;

	// If it's null, use the global
	if($id == null)
	{
		$id = $GLOBALS['pid'];
	}

	// Set the query
	$query = $dbh->query("
		SELECT
			COUNT(*)
		FROM comments
		WHERE published = 1 AND post_id= ". ((int)$id));

	// Query the database
	@list($commentnum) = $query->fetch(PDO::FETCH_NUM);

	// Return data
	return $commentnum;
}

function commentnum($id)
{
	echo get_commentnum($id);
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
function alternateColor($class1, $class2)
{
	static $count = 1;

	return (($count++) % 2) == 0 ? $class1 : $class2;
}
?>