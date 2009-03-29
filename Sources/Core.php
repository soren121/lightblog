<?php

/***********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	Core.php
	
	©2009 soren121. All rights reserved.
	Released under the GNU General
	Public License. For all licensing
	information, please see the
	LICENSE.txt document included in this
	distribution.

***********************************************/

// Open database if not open
$dbh = new SQLiteDatabase( DBH );

function bloginfo($var, $output = 'e') {
	global $dbh;
	static $bloginfo = null;

	if($bloginfo == null) {
		$result = $dbh->query('SELECT * FROM core') or die(sqlite_error_string($dbh->lastError));
		$bloginfo = array();
		while($row = $result->fetchObject()) {
			$bloginfo[$row->variable] = $row->value;
		}
	}
	if($output == 'e') {
		return !empty($bloginfo[$var]) ? $bloginfo[$var] : false;
	}
	else {
		echo !empty($bloginfo[$var]) ? $bloginfo[$var] : false;
	}	
}

function fetchGravatar($email, $size = 30, $output = 'e') {
	if($output == 'e') {
		echo "http://www.gravatar.com/avatar.php?gravatar_id=".md5($email)."&amp;size=".$size;
	}
	else {
		return "http://www.gravatar.com/avatar.php?gravatar_id=".md5($email)."&amp;size=".$size;
	}
}

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
  );

  preg_match_all('~&lt;(.*?)&gt;~is', $str, $matches);
  echo '<pre>'; print_r($matches); echo '</pre>';

	# Loop through and check XD!
	if(count($matches['1'])) {
	# Our replacement array :)
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
					$replacements[$matches['0'][$key]] = '</'. strpos($allowed_tags[$tag_name], ' ') !== false ? substr($allowed_tags[$tag_name], 0, strpos($allowed_tags[$tag_name], ' ')) : $allowed_tags[$tag_name]. '>';
				else
					$replacements[$matches['0'][$key]] = '</'. strtolower($tag_name). '>';
			}
			# Else? Nope! Leave it alone! :P
		}
		else {
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

  return $str;
}

?>