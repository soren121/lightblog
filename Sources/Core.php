<?php

/***********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	Sources/Core.php
	
	©2009 soren121. All rights reserved.
	Released under the GNU General
	Public License. For all licensing
	information, please see the
	LICENSE.txt document included in this
	distribution.

***********************************************/

// Open database if not open
$dbh = new SQLiteDatabase( DBH );

// Set default timezone
date_default_timezone_set('UTC');

// Function to output the current version of LightBlog
function LightyVersion($output = 'e') {
	# DON'T TOUCH!
	$version = '0.9.2 SVN';
	# Are we echoing or returning?
	if($output == 'e') { echo $version; }
	# Returning!
	else { return $version; }
}

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

// Function to fetch Gravatars
function fetchGravatar($email, $size = 32, $output = 'e') {
	# Is the Gravatar being echoed?
	if($output == 'e') {
		# Yep, so echo the URL
		echo "http://www.gravatar.com/avatar.php?gravatar_id=".md5($email)."&amp;size=".(int)$size;
	}
	else {
		# It's not being echoed, so return the URL
		return "http://www.gravatar.com/avatar.php?gravatar_id=".md5($email)."&amp;size=".(int)$size;
	}
}

// Function to fetch user data
function userFetch($var, $output = 'e') {
	# Does that value exist?
	if(!isset($_SESSION[$var])) { 
		# Nope, so return nothing
		return null;
	}
	else {
		# It exists, so echo/return it
		if($output == 'e') {
			echo $_SESSION[$var];
		}
		else {
			return $_SESSION[$var];
		}
	}
}


// Function to retrieve directory names
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

// Function to undo Magic Quotes in arrays
function undoMagicArray($array, $max_depth = 1, $cur_depth = 0) {
	if($cur_depth > $max_depth)
		return $array;
	else {
		$new_array = array();
		foreach($array as $key => $value) {
			if(!is_array($value))
				$new_array[stripslashes($key)] = stripslashes($value);
			else
				$new_array[stripslashes($key)] = undoMagic($value, $max_depth, $cur_depth + 1);
		}
		return $new_array;
	}
}

# Function to return an advanced Digg-style pagination thingy
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
				# Add the dots
				$pagination .= "<span class=\"elipses\">...</span>";
				$pagination .= "<a href=\"" . $target . $pagestring . $lpm1 . "\">$lpm1</a>";
				$pagination .= "<a href=\"" . $target . $pagestring . $lastpage . "\">$lastpage</a>";		
			}
			# We're in the middle; hide some in the front and back
			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
				# Add the first two links
				$pagination .= "<a href=\"" . $target . $pagestring . "1\">1</a>";
				$pagination .= "<a href=\"" . $target . $pagestring . "2\">2</a>";
				# Add the dots
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
				# Add the dots and the last few pages
				$pagination .= "...";
				$pagination .= "<a href=\"" . $target . $pagestring . $lpm1 . "\">$lpm1</a>";
				$pagination .= "<a href=\"" . $target . $pagestring . $lastpage . "\">$lastpage</a>";		
			}
			# We're close to the end, so only hide the early pages
			else {
				# Add the first few pages
				$pagination .= "<a href=\"" . $target . $pagestring . "1\">1</a>";
				$pagination .= "<a href=\"" . $target . $pagestring . "2\">2</a>";
				# Add the dots
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
			$pagination .= "<a href=\"" . $target . $pagestring . $next . "\">next »</a>";
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

// Function to generate a random string of specified length
function randomString($length) {
	if((is_numeric($length)) && ($length > 0) && (!is_null($length))) {
		// Start with a blank string
		$string = '';
		$accepted_chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890-,';
		// Loop through and make a string
		for($i=0;$i<=$length;$i++) {
			$random_number = rand(0, (strlen($accepted_chars) -1));
			$string .= $accepted_chars[$random_number];
		}
		// Return the final string
		return $string;
	}
}

// Login function
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
					// Generate secure random string
					$secure_string = randomString(48);
					// Set it in the session
					$_SESSION['securestring'] = $secure_string;
					// ...And a cookie
					setcookie(strtolower(bloginfo('title','r')).'securestring', $secure_string, time()+60*20, "/");					
					// Set the session timeout to 20 minutes
					$_SESSION['expires_by'] = time() + 60*20;
					// Send the user data to the session
					$_SESSION['username'] = $user->username;
					$_SESSION['email'] = $user->email;
					$_SESSION['displayname'] = $user->displayname;
					$_SESSION['role'] = $user->role;
					$_SESSION['ip'] = get_ip();
					// Resalt password
					$salt = substr(md5(uniqid(rand(), true)), 0, 9);
					$passhash = sha1($salt.$password);
					// Send new hash and salt to database
					$dbh->query("UPDATE users SET password='$passhash', salt='$salt' WHERE username='$username'");
					// Does the user want to remember their data?
					if(isset($_POST['remember']) && !isset($_COOKIE[bloginfo('title','r').'user'])) {
						setcookie(strtolower(bloginfo('title','r')).'user', $user->username, time()+60*60*24*30, "/");
						setcookie(strtolower(bloginfo('title','r')).'pass', $_POST['password'], time()+60*60*24*30, "/");
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

// Function to get a real IP
function get_ip() {
	// Look for an IP address
	if(!empty($_SERVER['REMOTE_ADDR'])) {
		$client_ip = $_SERVER['REMOTE_ADDR'];
	}
	// Look for proxies
	if(isset($_SERVER['HTTP_CLIENT_IP'])) {
		$proxy_ip = $_SERVER['HTTP_CLIENT_IP'];
	}
	elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$proxy_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	// Look for a real IP underneath a proxy
	if(isset($proxy_ip)) {
		if(preg_match("/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $proxy_ip, $ip_list)) {
				$private_ip = array(
					'/^0\./',
					'/^127\.0\.0\.1/',
					'/^192\.168\..*/',
					'/^172\.16\..*/',
					'/^10.\.*/',
					'/^224.\.*/',
					'/^240.\.*/');
				// A generic private IP is useless to us, so don't use those
				$client_ip = preg_replace($private_ip, $client_ip, $ip_list[1]);
		}
	}
	// Fix a strange localhost IP problem
	if($client_ip == '::1') {
		$client_ip = '127.0.0.1';
	}
	// Return what we think the IP is
	return $client_ip;
}

// Function to clean form input to reduce the risk of XSS attacks
function cleanHTML($str) {
	// Remove empty space
	$str = trim($str);
	// Prevent Unicode codec problems
	$str = utf8_decode($str);
	// Strip out CDATA
	preg_match_all('/<!\[cdata\[(.*?)\]\]>/is', $str, $matches);
    $str = str_replace($matches[0], $matches[1], $str);
	// Strip out all JavaScript
    $str = preg_replace("/href=(['\"]).*?javascript:(.*)?\\1/i", "onclick=' $2 '", $str);
    while(preg_match("/<(.*)?javascript.*?\(.*?((?>[^()]+)|(?R)).*?\)?\)(.*)?>/i", $str))
        $str = preg_replace("/<(.*)?javascript.*?\(.*?((?>[^()]+)|(?R)).*?\)?\)(.*)?>/i", "<$1$3$4$5>", $str);
    // Remove all expressions
	$str = preg_replace("/:expression\(.*?((?>[^(.*?)]+)|(?R)).*?\)\)/i", "", $str);
    while(preg_match("/<(.*)?:expr.*?\(.*?((?>[^()]+)|(?R)).*?\)?\)(.*)?>/i", $str))
        $str = preg_replace("/<(.*)?:expr.*?\(.*?((?>[^()]+)|(?R)).*?\)?\)(.*)?>/i", "<$1$3$4$5>", $str);
	// Remove all on* attributes
    while(preg_match("/<(.*)?\s?on.+?=?\s?.+?(['\"]).*?\\2\s?(.*)?>/i", $str))
       $str = preg_replace("/<(.*)?\s?on.+?=?\s?.+?(['\"]).*?\\2\s?(.*)?>/i", "<$1$3>", $str);
	// Strip all but allowed tags
	$str = strip_tags($str, "<b><strong><i><em><u><a><img><quote><p>");
	// Convert symbols to HTML entities to kill hex attacks
	$str = str_replace("#", "&#35;", $str);
	$str = str_replace("%", "&#37;", $str);
	// Return the final string
	return $str;
}

// User permissions function
function permissions($group) {
	# Fetch the session info
	if(userFetch('role', 1) >= $group) {
		# Return true if they're allowed
		return true;
	}
	
	else {
		# Return false if they aren't
		return false;
	}
}

?>