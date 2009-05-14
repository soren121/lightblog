<?php

/***********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	Sources/RemoveXSS.php
	
	©2009 soren121. All rights reserved.
	Released under the GNU General
	Public License. For all licensing
	information, please see the
	LICENSE.txt document included in this
	distribution.

***********************************************/

// Function to reduce the risk of a cross-site scripting attack (XSS)
// Big thanks to aldo (www.mschat.net) for writing this function!
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
			if(preg_match('~href=(?:&quot;|&#039;)(.*?)(?:&quot;|&#039;)~is', $match, $sub_match)) {
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
			if(preg_match('~src=(?:&quot;|&#039;)(.*?)(?:&quot;|&#039;)~is', $str, $sub_match)) {
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

?>