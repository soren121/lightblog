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
	# Static $bloginfo and null the value
	static $bloginfo = null;

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
function fetchGravatar($email, $size = 30, $output = 'e') {
	# Is the Gravatar being echoed?
	if($output == 'e') {
		# Yep, so echo the URL
		echo "http://www.gravatar.com/avatar.php?gravatar_id=".md5($email)."&amp;size=".$size;
	}
	else {
		# It's not being echoed, so return the URL
		return "http://www.gravatar.com/avatar.php?gravatar_id=".md5($email)."&amp;size=".$size;
	}
}

// Function to fetch user data
function userFetch($var, $output = 'e') {
	# Is this being echoed?
	if($output == 'e') {
		# Does that value exist?
		if(!isset($_SESSION[$var])) { 
			# Nope, so return nothing
			return null;
		}
		else {
			# It exists, so echo it
			echo $_SESSION[$var];
		}
	}
	# It's not echoing, so we'll return it
	else { 
		# Does the value exist?
		if(!isset($_SESSION[$var])) { 
			# Nope, so return nothing
			return null;
		}
		else {
			# Return it like they asked
			return $_SESSION[$var];
		}
	}
}

// Function to reduce the risk of a cross-site scripting attack (XSS)
function removeXSS($str) {
	$str = htmlspecialchars($str, ENT_QUOTES);
	# Allowed BASIC tags! like strong, b, i, etc.
	# and what they should be converted too... like 'i' => 'em'
	$allowed_tags = array(
		'strong' => false,
		'b' => 'strong',
		'em' => false,
		'i' => 'em',
		'u' => 'span style="text-decoration: underline;"',
		'left' => 'p style="text-align: left;"',
		'center' => 'p style="text-align: center;"',
		'right' => 'p style="text-align: right;"',
		'pre' => false,
	);
	preg_match_all('~&lt;(.*?)&gt;~is', $str, $matches);
	# Loop through and check XD!
	if(count($matches['1'])) {
		# Our replacement array
		$replacements = array();
		foreach($matches['1'] as $key => $match) {
			# Maybe its a closing tag?
			if(substr($match, 0, 1) == '/') {
				# So remove the first character, which is the /
				$tag_name = substr($match, 1, strlen($match));
				# So is it allowed..?
				if(isset($allowed_tags[strtolower($tag_name)])) {
					# Yeah it is... Maybe it has a replacement..?
					if($allowed_tags[strtolower($tag_name)] !== false)
						$replacements[$matches['0'][$key]] = '</'. (strpos($allowed_tags[$tag_name], ' ') !== false ? substr($allowed_tags[$tag_name], 0, strpos($allowed_tags[$tag_name], ' ')) : $allowed_tags[$tag_name]). '>';
					else
						$replacements[$matches['0'][$key]] = '</'. strtolower($tag_name). '>';
				}
				# Else? Nope! Leave it alone!
			}
			else {
				# Or not... xD.
				# So get the tag name...
				# And only get the first part... before the space, if any...
				if(strpos($match, ' ') !== false)
					$tag_name = substr($match, 0, strpos($match, ' '));
				else
					$tag_name = $match;
				# Check if the tag is allowed...
				if(isset($allowed_tags[strtolower($tag_name)])) {
					# Any replacement?
					if($allowed_tags[strtolower($tag_name)] !== false)
						$replacements[$matches['0'][$key]] = '<'. $allowed_tags[$tag_name]. '>';
					else
						$replacements[$matches['0'][$key]] = '<'. strtolower($tag_name). '>';
				}
			}		
		}
		# Now that we got that, replace anything?
		if(count($replacements))
		$str = strtr($str, $replacements);
	}
	# Now links (<a href="..."></a>) and image tags!
	# This takes a bit more because we must moderately validate the URLs
	preg_match_all('~&lt;a (.*?)&gt;(.*?)&lt;/a&gt;~is', $str, $matches);
	# Oh ya, and an allowed list of protocols Also for images
	$allowed_protocols = array('http', 'https', 'ftp', 'ftps');
	# Open in a new window/tab? (target="_blank")
	$target_blank = true;
	# Anything even found..?
	if(count($matches['1'])) {
		# Our other replacements array
		$replacements = array();
		# Lets get going shall we?
		foreach($matches['1'] as $key => $match) {
			# We need to get the href out
			if(preg_match('~href=(?:&quot;|&rsquo;)(.*?)(?:&quot;|&rsquo;)~is', $match, $sub_match)) {
				# Sweet! We found it!
				$url = $sub_match['1'];
				# Get the protocol...
				if(strpos($url, ':') !== false) {
					$protocol = substr($url, 0, strpos($url, ':'));
					# Is it there?
					if(in_array($protocol, $allowed_protocols)) {
						# Add the replacements...
						$replacements[$matches['0'][$key]] = '<a href="'. $url. '"'. ($target_blank ? ' target="_blank"' : ''). '>'. $matches['2'][$key]. '</a>';
					}
					# Nothing? Screw it XD.
				}
				# Nothing too? Screw it as well...!
			}
		}
		# Anything get replaced..?
		if(count($replacements))
			$str = strtr($str, $replacements);
	}
	# Images!
	preg_match_all('~&lt;im(?:g|age) (.*?)&gt;~is', $str, $matches);
	# Now... Anything?
	if(count($matches['1'])) {
		# Once again... our replacements array >_>
		$replacements = array();
		foreach($matches['1'] as $key => $match) {
			# Get out the src!
			if(preg_match('~src=(?:&quot;|&rsquo;)(.*?)(?:&quot;|&rsquo;)~is', $str, $sub_match)) {
				# So we got something!
				$url = $sub_match['1'];
				# Get the protocol
				if(strpos($url, ':') !== false) {
					$protocol = substr($url, 0, strpos($url, ':'));
					# So is the protocol allowed?
					if(in_array($protocol, $allowed_protocols)) {
						# Yeah, lets do it!!!!
						$replacements[$matches['0'][$key]] = '<img src="'. $url. '" alt="" />';
					}
					# Nothing... Screw you... Oh Did I say that? Not like you can see it! XD
				}
			}
		}
		# Replace it?
		if(count($replacements))
			$str = strtr($str, $replacements);
	}
	# We need to close open tags, and open close tags =P
	$tags_before = '';
	$tags_after = '';
	# Get going...
	foreach($allowed_tags as $tag => $convert_to) {
		# Hmm, tag name..?
		if($convert_to === false)
			# Its just the tag
			$tag_name = $tag;
		elseif(strpos($convert_to, ' ') !== false)
			# Get the closing tag name
			$tag_name = trim(substr($convert_to, 0, strpos($convert_to, ' ')));
		else
			# Its just the convert to...
			$tag_name = $convert_to;
			# If it is being converted, and the convert to is set, let the convert
			# to handle it, not this!
		if($convert_to !== false && isset($allowed_tags[$tag_name]))
			# Skip.
			continue;
		# Now, lets check to see the number of opening and closings of this tag...
		$open_tags = substr_count($str, '<'. ($convert_to === false ? $tag_name : $convert_to). '>');
		$close_tags = substr_count($str, '</'. $tag_name. '>');
		# So, they not the same..?
		if($open_tags > $close_tags) {
			# We don't have enough closing tags...
			for($i = 0; $i < ($open_tags - $close_tags); $i++)
				$tags_after .= '</'. $tag_name. '>';
		}
		elseif($close_tags > $open_tags) {
			# This probably is the best, but hey! Learn to count
			# And I won't make your comment/post/whatever ugly!
			for($i = 0; $i < ($close_tags - $open_tags); $i++)
				$tags_before .= '<'. ($convert_to === false ? $tag_name : $convert_to). '>';
		}
	}
	# Add the tags
	$str = $tags_before. $str. $tags_after;
	return $str;
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

?>