<?php

/***********************************************

	LightBlog 0.9
	SQLite blogging platform

	Sources/Template.php

	©2008-2012 The LightBlog Team. All
	rights reserved. Released under the
	GNU General Public License 3. For
	all licensing information, please
	see the LICENSE.txt document
	included in this distribution.

***********************************************/

if(!defined('E_USER_DEPRECATED'))
{
	define('E_USER_DEPRECATED', 16384);
}

ob_start();

// We will go ahead and have PHP report all errors.
error_reporting(E_ALL | E_STRICT | E_NOTICE);

// No HTML errors, please.
@ini_set('html_errors', false);

/*
	Function: errorHandle

	This function will handle any errors thrown by PHP.

	Parameters:
		int $errno - The error number.
		string $message - The error message.
		string $filename - The file which threw the error.
		int $line - The line at which the error occurred.

	Returns:
		bool - Returns true if the error handler properly dealt with the error
					 (logged it, for example) and false if not.
*/
function errorsHandle($errno, $message, $filename, $line, $errcontext)
{
	// Make sure the error isn't coming from this function, as that could get
	// very ugly...
	if(substr($filename, strlen($filename) - 10) == 'Errors.php')
	{
		return false;
	}

	// Do we need to make a connection to the SQLite database ourselves?
	if(defined('DBH') && !array_key_exists('dbh', $GLOBALS))
	{
		$GLOBALS['dbh'] = new SQLiteDatabase(DBH);
	}

	// Make sure we can query the database.
	if(!empty($GLOBALS['dbh']))
	{
		$timestamp = time();
		$error_type = (int)$errno;
		$message = sqlite_escape_string($message);
		$filename = sqlite_escape_string(substr($filename, strlen(ABSPATH)));
		$line = (int)$line;
		$error_url = sqlite_escape_string('http://'. $_SERVER['HTTP_HOST']. $_SERVER['REQUEST_URI']);

		return $GLOBALS['dbh']->query("
			INSERT INTO 'error_log'
			('error_time', 'error_type', 'error_message', 'error_file', 'error_line', 'error_url')
			VALUES('$timestamp', '$error_type', '$message', '$filename', '$line', '$error_url')") !== false;
	}
	else
	{
		// We couldn't handle it.
		return false;
	}
}

/*
	Function: errorsFatalCatcher

	If a fatal error occurs while the page is executing this function will
	handle the issue properly.

	Parameters:
		none

	Returns:
		void - Nothing is returned by this function.
*/
function errorsFatalCatcher()
{

}

/*
	Function: errorsMapType
*/
function errorsMapType($errno)
{
	$error_types = array(
		E_ERROR => 'Fatal',
		E_WARNING => 'Warning',
		E_PARSE => 'Parse',
		E_NOTICE => 'Notice',
		E_USER_ERROR => 'Fatal',
		E_USER_WARNING => 'Warning',
		E_USER_NOTICE => 'Notice',
		E_STRICT => 'Compatibility',
		E_DEPRECATED => 'Deprecated',
		E_USER_DEPRECATED => 'Deprecated',
		E_ALL => 'General',
	);

	return isset($error_types[$errno]) ? $error_types[$errno] : 'Unknown';
}

// Tell PHP where to route the errors from here on out.
set_error_handler('errorsHandle', E_ALL | E_STRICT | E_NOTICE);

// Shutdown functions will be called even when a fatal error occurs, which
// is how we will catch them.
register_shutdown_function('errorsFatalCatcher');
?>