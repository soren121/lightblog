<?php

/***********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	Sources/Core.php
	
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

// Open database if not open
$dbh = new SQLiteDatabase( DBH );

// Set default timezone
date_default_timezone_set('UTC');

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
	$version = '0.9.3 SVN';
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
	# Global the database handle so we can use it in this function
	global $dbh;	
	# Set defaults
	if(!$adjacents) $adjacents = 1;
	if(!$limit) $limit = 8;
	if(!$page) $page = 1;
	# Set teh query to retrieve the number of rows
	$query = $dbh->query("SELECT COUNT(*) FROM ".sqlite_escape_string($type)."s") or die(sqlite_error_string($dbh->lastError));
	# Query the database
	@list($totalitems) = $query->fetch(SQLITE_NUM);	
	# Set various required variables
	$prev = $page - 1;						# Previous page is page - 1
	$next = $page + 1;						# Next page is page + 1
	$lastpage = ceil($totalitems/$limit);	# Last page is = total items / items per page, rounded up.
	$lpm1 = $lastpage - 1;					# Last page minus 1	
	
	# Clear $pagination
	$pagination = "";
	# Do we have more than one page?
	if($totalitems > $limit) {	
		# Start the pagination div
		$pagination .= "<div class=\"pagination\">";
		
		# Add the previous button
		if($page > 1) {
			$pagination .= "<a href=\"" . $target . $pagestring . $prev . "\">« prev</a>";
		}
		else {
			# Disable the previous button, since we're on the first page
			$pagination .= "<span class=\"disabled\">« prev</span>";	
		}
		# Add the page buttons	
		if ($lastpage < 7 + ($adjacents * 2)) {	
			# There aren't enough pages to bother breaking it up
			# Loop through the pages and create links for all
			for($counter = 1; $counter <= $lastpage; $counter++) {
				if($counter == $page) {
					$pagination .= "<span class=\"current\">$counter</span>";
				}
				else {
					$pagination .= "<a href=\"" . $target . $pagestring . $counter . "\">$counter</a>";		
				}
			}
		}
		elseif($lastpage >= 7 + ($adjacents * 2)) {
			# We have enough pages to hide some of them now
			if($page < 1 + ($adjacents * 3)) {
				# Start a loop and create the first few pages
				for($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
					if($counter == $page) {
						$pagination .= "<span class=\"current\">$counter</span>";
					}
					else {
						$pagination .= "<a href=\"" . $target . $pagestring. $counter . "\">$counter</a>";					
					}
				}
				# Add the ellipses
				$pagination .= "<span class=\"elipses\">...</span>";
				$pagination .= "<a href=\"" . $target . $pagestring . $lpm1 . "\">$lpm1</a>";
				$pagination .= "<a href=\"" . $target . $pagestring . $lastpage . "\">$lastpage</a>";		
			}
			# We're in the middle; hide some in the front and back
			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
				# Add the first two links
				$pagination .= "<a href=\"" . $target . $pagestring . "1\">1</a>";
				$pagination .= "<a href=\"" . $target . $pagestring . "2\">2</a>";
				# Add the ellipses
				$pagination .= "<span class=\"elipses\">...</span>";
				# Start the for loop to make the page links
				for($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
					if($counter == $page) {
						$pagination .= "<span class=\"current\">$counter</span>";
					}
					else {
						$pagination .= "<a href=\"" . $target . $target . $pagestring . $counter . "\">$counter</a>";	
					}
				}
				# Add the ellipses and the last few pages
				$pagination .= "...";
				$pagination .= "<a href=\"" . $target . $pagestring . $lpm1 . "\">$lpm1</a>";
				$pagination .= "<a href=\"" . $target . $pagestring . $lastpage . "\">$lastpage</a>";		
			}
			# We're close to the end, so only hide the early pages
			else {
				# Add the first few pages
				$pagination .= "<a href=\"".$target.$pagestring."1\">1</a>";
				$pagination .= "<a href=\"".$target.$pagestring."2\">2</a>";
				# Add the ellipses
				$pagination .= "<span class=\"elipses\">...</span>";
				for ($counter = $lastpage - (1 + ($adjacents * 3)); $counter <= $lastpage; $counter++) {
					if ($counter == $page) {
						$pagination .= "<span class=\"current\">$counter</span>";
					}
					else {
						$pagination .= "<a href=\"".$target.$pagestring.$counter."\">$counter</a>";	
					}
				}				
			}
		}		
		# Add the next button
		if ($page < $counter - 1) {
			$pagination .= "<a href=\"".$target.$pagestring.$next."\">next »</a>";
		}
		else {
			$pagination .= "<span class=\"disabled\">next »</span>";
		}
		# End the pagination div
		$pagination .= "</div>\n";
	}
	# Return the final pagination div
	return $pagination;
}

/*
	Function: login
	
	Logs in a user.
	
	Parameters:
	
		method - Method used to login the user. Will be used more when 0.9.4 rolls around.
		
	Returns:
	
		An error message if something failed. If it worked, it will send the user to the admin dashboard.
*/
function login($method) {
	// Global the database handle so we can use it
	global $dbh;
	// Are we logging the user in via username/password?
	if($method == 'userpass') {
		// Set easy variables and escape username
		$username = sqlite_escape_string($_POST['username']);
		$password = $_POST['password'];
		// Does that user exist?
		$saltquery = $dbh->query("SELECT salt FROM users WHERE username='$username'") or null;
		// Well?
		if($saltquery !== null) {
			// It exists, so recreate the password hash so we can match it
			$passhash = sha1($saltquery->fetchSingle().$password);
			// Retrieve all user data
			$userquery = $dbh->query("SELECT * FROM users WHERE username='$username'") or die(sqlite_error_string($dbh->lastError));
			// Start the while loop
			while($user = $userquery->fetchObject()) {
				// Does the provided password match the database hash?
				if($passhash == $user->password) {
					// Set the session timeout to 20 minutes
					$_SESSION['expires_by'] = time() + 60*20;
					// Send the user data to the session
					$_SESSION['username'] = $user->username;
					$_SESSION['email'] = $user->email;
					$_SESSION['displayname'] = $user->displayname;
					$_SESSION['role'] = $user->role;
					$_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
					// Resalt password
					$salt = substr(md5(uniqid(rand(), true)), 0, 9);
					$passhash = sha1($salt.$password);
					// Send new hash and salt to database
					$dbh->query("UPDATE users SET password='$passhash', salt='$salt' WHERE username='$username'");
					// Does the user want to remember their data?
					if(isset($_POST['remember']) && !isset($_COOKIE[bloginfo('title','r').'user'])) {
						setcookie('username', $user->username, time()+60*60*24*30, "/");
						setcookie('password', $_POST['password'], time()+60*60*24*30, "/");
					}
					// Send the user to the dashboard
					header('Location: '.bloginfo('url','r').'admin/dashboard.php');
				}
			}
		}
		else {
			echo 'Incorrect username or password.';
		}
	}
}

/*
	Function: list_themes
	
	Outputs a list of themes in HTML <option> tags.
*/
function list_themes() {
	// List directories
	$dir = dirlist(ABSPATH .'/themes'); 
	foreach($dir as $k => $v) {
		if(bloginfo('theme','r') == $k) {
			echo '<option selected="selected" value="'.$k.'">'.$v.'</option>';
		}
		else {
			echo '<option value="'.$k.'">'.$v.'</option>';
		}		
	}
}

/*
	Function: list_categories
	
	Outputs a list of categories in HTML <option> tags.
	
	Parameters:
	
		selected - The category ID that will be selected by default in the drop-down list. Not required.
*/
function list_categories($selected = null) {
	// Grab the database handle
	global $dbh;
	// Get category data from database
	$result = $dbh->query("SELECT * FROM categories") or die(sqlite_error_string($dbh->lastError));
	// Sort through and create list items
	while($row = $result->fetchObject()) {
		if($selected == $row->id) {
			echo '<option value="'.$row->id.'" selected="selected">'.stripslashes($row->fullname).'</option>';
		}
		else {
			echo '<option value="'.$row->id.'">'.stripslashes($row->fullname).'</option>';
		}
	}
}

?>
