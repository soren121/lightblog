<?php
/*********************************************

	LightBlog 0.9.4
	SQLite blogging platform

	CleanRequest.php

	©2008-2012 The LightBlog Team. All
	rights reserved. This file is dual-licensed
	and can be reused under either the GNU GPL
	v3 or the Microsoft Reciprocal License (MS-RL),
	please see the LICENSE.txt document
	included in this distribution.

*********************************************/

/*
	Function: remove_magic

	Removes the effects of magic quotes from the specified variable.

	Parameters:
		array $array - The array to remove magic quotes effects from.
		int $depth - The maximum depth to go within the array.

	Returns:
		array - Returns the cleaned array.
*/
function remove_magic($array, $depth = 5)
{
	if(count($array) == 0 || !is_array($array))
	{
		return array();
	}
	elseif($depth <= 0)
	{
		return $array;
	}

	foreach($array as $key => $value)
	{
		// The keys can also be escaped, so we will delete it.
		unset($array[$key]);

		$array[stripslashes($key)] = is_array($value) ? remove_magic($value, $depth - 1) : stripslashes($value);
	}

	return $array;
}

/*
	Function: redirect

	Redirects the browser to the specified URL.

	Parameters:
		string $location - The new location redirect the browser to.
		mixed $status - The type of redirect to issue, such as 301 (Moved
										Permanently) or 307 (Temporary). If you don't want to
										remember either, you can supply permanent[ly] or
										temporary. This defaults to a temporary move.

	Returns:
		void - Nothing is returned by this function.

	Note:
		If you are wondering what the difference between a temporary and a
		permanent redirect are, and cannot deduce what it means through their
		names, I suppose I should tell you! A temporary redirect ensures that a
		browser will not cache the redirection when the same page is requested
		at a later time, while a permanent redirect can cause certain browsers
		(I know Chrome does, not sure about others) to cache the redirect until
		the cache is cleared, which can be bad if you don't want the browser to
		assume anything. Did that help? Bet not.
*/
function redirect($location = null, $status = 307)
{
	if(ob_get_length() > 0)
	{
		// Clear the output buffer, if anything has been written to it.
		@ob_clean();

		if(function_exists('ob_gzhandler') && (get_bloginfo('disable_compression') === false || get_bloginfo('disable_compression') == 0))
		{
			ob_start('ob_gzhandler');
		}
		else
		{
			ob_start();
		}
	}

	if(empty($location))
	{
		$location = get_bloginfo('url');
	}

	// What type of redirect? Temporary, or permanent?
	if((int)$status == 307 || strtolower($status) == 'temporary')
	{
		// We may need to make a minor modification. Some browsers like to show
		// an annoying 'This web page is being redirect to another location'
		// blah  blah blah if there is POST data involved. This can be fixed
		// pretty  easily.
		if(count($_POST) > 0)
		{
			header('HTTP/1.1 303 See Other');
		}
		else
		{
			header('HTTP/1.1 307 Temporary Redirect');
		}
	}
	else
	{
		header('HTTP/1.0 301 Moved Permanently');
	}

	// Don't cache this! Geez. (This is done because browsers still send the
	// POST data when doing a 307 redirect, but then with a 301 redirect they
	// cache the resulting redirect)
	header('Cache-Control: no-cache');

	// Now redirect to the location of your desire!
	header('Location: '. $location);

	// Execution, HALT!
	exit;
}

/*
	Function: clean_request

	Removes any affects magic quotes has on $_COOKIE, $_GET, $_POST or
	$_REQUEST. It also removes the $_COOKIE variable from $_REQUEST.

	Parameters:
		none

	Returns:
		void - Nothing is returned by this function.

	Note:
		This function is overloadable.
*/
function clean_request()
{
	global $_COOKIE, $_GET, $_POST, $_REQUEST;

	// Remove magic quotes, if it is on...
	if((function_exists('get_magic_quotes_gpc') && @get_magic_quotes_gpc() == 1) || @ini_get('magic_quotes_sybase'))
	{
		$_COOKIE = remove_magic($_COOKIE);
		$_GET = remove_magic($_GET);
		$_POST = remove_magic($_POST);
	}

	// $_REQUEST should only contain $_POST and $_GET, no cookies!
	$_REQUEST = array_merge($_POST, $_GET);

	// While we're at it, let's disable magic quotes runtime, if enabled.
	if(function_exists('get_magic_quotes_runtime') && @get_magic_quotes_runtime())
	{
		@set_magic_quotes_runtime(false);
	}
}

// Clean up our request, then :P
clean_request();
?>