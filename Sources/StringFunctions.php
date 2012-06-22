<?php

/***********************************************

	LightBlog 0.9
	SQLite blogging platform

	Sources/StringFunctions.php

	©2008-2012 The LightBlog Team. All
	rights reserved. Released under the
	GNU General Public License 3. For
	all licensing information, please
	see the LICENSE.txt document
	included in this distribution.

***********************************************/

/*
	Function: randomString

	Returns a random alphanumeric string.

	Parameters:

		length - Length of string to make.

	Returns:

		A completely random string.
*/
function randomString($length)
{
	if(is_numeric($length) && $length > 0)
	{
		// Start with a blank string
		$string = '';
		$accepted_chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890-,';
		$char_length = strlen($accepted_chars);

		// Loop through and make a string
		for($i = 0; $i <= $length; $i++)
		{
			$string .= $accepted_chars[mt_rand(0, $char_length - 1)];
		}

		// Return the final string
		return $string;
	}

	return false;
}

// Function to correct plurals and such on dynamic numbers, mainly comment numbers
function grammarFix($number, $singular, $plural)
{
	if($number == 1)
	{
		// The number is 1, so we will use the singular form of the word
		echo $number.' '.$singular;
	}
	else
	{
		// The number is something other than 1, so we'll use the plural form
		echo $number.' '.$plural;
	}
}

/*
	Function: is_url

	Checks to see whether or not the supplied value is a URL.

	Parameters:
		string $url - The URL to validate.
		array $protocols - An array containing protocols which should be
											 considered valid (without the :// part). Defaults
											 to http and https.

	Returns:
		bool - Returns true if the supplied URL is actually valid, false if
					 not.
*/
function is_url($url, $protocols = array())
{
	// Don't even try it...
	if(utf_strtolower(trim(utf_substr($url, 0, 11))) == 'javascript:')
	{
		return false;
	}

	// Any protocols supplied?
	if(!is_array($protocols) || count($protocols) == 0)
	{
		// None I see, so just HTTP and HTTPS then.
		$protocols = array('http', 'https');
	}

	// The PHP documentation says parse_url isn't meant to validate URL's,
	// but we are sure going to use it to check! :-P
	$parsed = parse_url($url);

	// Is the protocol valid?
	if(empty($parsed['scheme']) || !in_array(utf_strtolower($parsed['scheme']), $protocols))
	{
		// No, it is not.
		return false;
	}
	// Is there a host supplied?
	elseif(empty($parsed['host']))
	{
		// Nope.
		return false;
	}
	else
	{
		// Hopefully this is okay >.<
		return true;
	}
}

// Set the default encoding.
mb_internal_encoding(defined('LB_ENCODING') ? LB_ENCODING : 'UTF-8');
?>