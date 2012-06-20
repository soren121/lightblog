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
	Function: utf_substr

	Parameters:
		string $str
		int $start
		int $length
*/
function utf_substr($str, $start, $length = null)
{
	return $length === null ? call_user_func(function_exists('mb_substr') ? 'mb_substr' : 'substr', $str, $start) : call_user_func(function_exists('mb_substr') ? 'mb_substr' : 'substr', $str, $start, $length);
}

/*
	Function: utf_strtoupper

	Parameters:
		string $str
*/
function utf_strtoupper($str)
{
	return call_user_func(function_exists('mb_strtoupper') ? 'mb_strtoupper' : 'strtoupper', $str);
}

/*
	Function: utf_strtolower

	Parameters:
		string $str
*/
function utf_strtolower($str)
{
	return call_user_func(function_exists('mb_strtolower') ? 'mb_strtolower' : 'strtolower', $str);
}

/*
	Function: utf_strrpos

	Parameters:
		string $haystack
		string $needle
		int $offset
*/
function utf_strrpos($haystack, $needle, $offset = 0)
{
	return call_user_func(function_exists('mb_strrpos') ? 'mb_strrpos' : 'strrpos', $haystack, $needle, $offset);
}

/*
	Function: utf_strripos

	Parameters:
		string $haystack
		string $needle
		int $offset
*/
function utf_strripos($haystack, $needle, $offset = 0)
{
	return call_user_func(function_exists('mb_strripos') ? 'mb_strripos' : 'strripos', $haystack, $needle, $offset);
}

/*
	Function: utf_strpos

	Parameters:
		string $haystack
		string $needle
		int $offset
*/
function utf_strpos($haystack, $needle, $offset = 0)
{
	return call_user_func(function_exists('mb_strpos') ? 'mb_strpos' : 'strpos', $haystack, $needle, $offset);
}

/*
	Function: utf_strlen

	Parameters:
		string $str
*/
function utf_strlen($str)
{
	return call_user_func(function_exists('mb_strlen') ? 'mb_strlen' : 'strlen', $str);
}

/*
	Function: utf_stripos

	Parameters:
		string $haystack
		string $needle
		int $offset
*/
function utf_stripos($haystack, $needle, $offset = 0)
{
	return call_user_func(function_exists('mb_strpos') ? 'mb_stripos' : 'stripos', $haystack, $needle, $offset);
}

/*
	Function: utf_htmlspecialchars

	Parameters:
		string $str
*/
function utf_htmlspecialchars($str)
{
	return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// Set the default encoding.
mb_internal_encoding(defined('LB_ENCODING') ? LB_ENCODING : 'UTF-8');
?>