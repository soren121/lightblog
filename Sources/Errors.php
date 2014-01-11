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
	// If the error is fatal, then we need to handle it differently.
	if(in_array($errno, array(E_ERROR, E_PARSE, E_COMPILE_ERROR, E_USER_ERROR)))
	{
		// Display the fatal error.
		errorsShowFatal($message, $filename, $line);

		return true;
	}

	// Make sure the error isn't coming from this function, as that could get
	// very ugly...
	if(substr($filename, strlen($filename) - 10) == 'Errors.php')
	{
		return false;
	}

	// Do we need to make a connection to the SQLite database ourselves?
	if(defined('DBH') && !array_key_exists('dbh', $GLOBALS) && file_exists(DBH))
	{
		$GLOBALS['dbh'] = new PDO('sqlite:'.DBH);
	}

	// Make sure we can query the database.
	if(array_key_exists('dbh', $GLOBALS))
	{
		$timestamp = time();
		$error_type = (int)$errno;
		$message = sqlite_escape_string($message);
		$filename = sqlite_escape_string(substr($filename, strlen(ABSPATH)));
		$line = (int)$line;
		$error_url = sqlite_escape_string('http://'. $_SERVER['HTTP_HOST']. $_SERVER['REQUEST_URI']);

		@$GLOBALS['dbh']->exec("
			INSERT INTO 'error_log'
			('error_time', 'error_type', 'error_message', 'error_file', 'error_line', 'error_url')
			VALUES('$timestamp', '$error_type', '$message', '$filename', '$line', '$error_url')");
	}

	return true;
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
	$last_error = null;

	// If your server has the error_get_last function our life will be a whole
	// lot easier!
	if(function_exists('error_get_last'))
	{
		$last_error = error_get_last();
	}
	else
	{
		// Darn, so we will need to attempt to detect the fatal error ourselves.
		$content = trim(ob_get_contents());

		// We want to be careful doing this manually, though. Make sure that
		// no HTML has been sent or that only some of the HTML has been sent.
		if((($html_tag_found = stripos($content, '<html')) === false || ($html_tag_found && stripos($content, '</html') === false)) && (($is_fatal = stripos(strip_tags($content), 'fatal error')) !== false || stripos(strip_tags($content), 'parse error') !== false))
		{
			// Remove tags.
			$content = strip_tags($content);

			// Now get the error messages itself.
			$error_message = substr($content, strripos($content, $is_fatal ? 'fatal error' : 'parse error'));

			// Get the line that the error occurred on.
			$line = (int)substr($error_message, strrpos($error_message, 'line') + 5);
			$error_message = rtrim(substr($error_message, 0, strrpos($error_message, 'line') - 4));

			// Now we need to find the file too.
			$filename = trim(substr($error_message, stripos($error_message, ' in ') + 4));

			// Now we just have the error message itself left.
			$error_message = substr($error_message, 0, stripos($error_message, ' in '));
			$error_message = substr($error_message, strpos($error_message, ':') + 2);

			// Now emulate the error_get_last function's return value.
			$last_error = array(
											'type' => $is_fatal ? E_ERROR : E_PARSE,
											'message' => $error_message,
											'file' => $filename,
											'line' => $line,
										);
		}
	}

	// Is the error fatal? (and was there an error in the first place?)
	if(file_exists($last_error['file']) && $last_error['line'] > 0 && in_array($last_error['type'], array(E_ERROR, E_PARSE, E_COMPILE_ERROR), true))
	{
		errorsHandle($last_error['type'], $last_error['message'], $last_error['file'], $last_error['line']);

		// Alright, show it!
		errorsShowFatal($last_error['message'], $last_error['file'], $last_error['line']);
	}
}

/*
	Function: errorsShowFatal

	Displays a 'pretty' fatal error message.

	Parameters:
		string $message - The error message.
		string $filename - The file in which the error occurred.
		int $line - The line on which the error occurred.

	Returns:
		void - Nothing is returned by this function.

	Note:
		This function will call exit, so nothing after calling this function
		will execute.
*/
function errorsShowFatal($message, $filename, $line)
{
	ob_clean();

	// If this isn't an administrator, we don't want to show the whole file
	// path -- just in case (whatever case that may be).
	if(!function_exists('permissions') || !permissions(1))
	{
		$filename = realpath($filename);

		// Remove the irrelevant parts.
		$filename = substr($filename, strlen(realpath(dirname(dirname(__FILE__)))) + 1);
	}

	// It looks like it is time to show the error!
	echo '<!DOCTYPE html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="robots" content="noindex" />
	<title>Server Error: A Fatal Error Occurred</title>
	<style type="text/css">
		body
		{
			font: 13px Verdana, Tahoma, Arial, sans-serif;
		}

		h1
		{
			font-family: Georgia;
			font-weight: normal;
			color: red;
		}

		hr
		{
			height: 1px;
			background: #CCC;
			border: none;
		}

		#code-box
		{
			background: #FFFFAA;
		}

		#code-box p
		{
			display: block;
			margin: 0;
			padding: 0;
		}

		.highlighted-line
		{
			background: #FF7F50;
		}

		.line-no
		{
			float: left;
			display: block;
			font-weight: bold;
			border-right: 1px solid #000000;
			padding: 0 3px;
			margin-right: 3px;
		}

		.break { clear: both; }
	</style>
</head>
<body>
	<h1>Server Error: A Fatal Error Occurred</h1>
	<hr />
	<p>An unexpected fatal error has occurred which has prevented the page from loading properly. If you are not a server administrator, please attempt to contact one in order to have this issue resolved.</p>
	<p>Please be sure to provide the following information when contacting the administrator.</p>
	<hr />
	<p><strong>Error:</strong> ', $message, ' in <strong>', htmlspecialchars($filename), '</strong> on line <strong>', (int)$line, '</strong></p>';

	// If they are an administrator, we can go ahead and show them the content
	// of the file, but just the relevant lines (the line itself and a few
	// others surrounding it.
	if(function_exists('permissions') && permissions(1))
	{
		// We will want to open the file.
		$fp = fopen($filename, 'rb');

		// Let's find the proper lines.
		$cur_line = 1;
		$lines = array();
		while(!feof($fp))
		{
			// Do we want this line?
			if(abs(($cur_line < $line ? $cur_line + 1 : $cur_line)- $line) <= 10)
			{
				$lines[$cur_line] = fgets($fp);
			}
			elseif($cur_line > $line)
			{
				// We are over the lines we wanted to get, so no point on continuing
				// to go through the file.
				break;
			}
			else
			{
				// Not the line we want, but go ahead and keep going...
				fgets($fp);
			}

			$cur_line++;
		}

		fclose($fp);

		// Alright, let's display the lines, highlighting the one which is
		// causing the problem.
		echo '
	<div id="code-box">';

		foreach($lines as $cur_line => $line_text)
		{
			// We will want to replace a few things.
			$line_text = strtr(htmlspecialchars($line_text, ENT_QUOTES, 'UTF-8'), array(
																																							"\r\n" => '',
																																							"\r" => '',
																																							"\n" => '',
																																							"\t" => '&nbsp;',
																																							' ' => '&nbsp;',
																																						));

			echo '
		<p', $cur_line == $line ? ' class="highlighted-line"' : '', '><span class="line-no">', $cur_line, '</span> ', $line_text, '</p>
		<div class="break">
		</div>';
		}

		echo '
	</div>';
	}

	echo '
</body>
</html>';

	// Stop executing.
	exit;
}

/*
	Function: errorsMapType
*/
function errorsMapType($errno)
{
	$error_types = @array(
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
		E_ALL => 'General'
	);

	return isset($error_types[$errno]) ? $error_types[$errno] : 'Unknown';
}

// Tell PHP where to route the errors from here on out.
set_error_handler('errorsHandle', E_ALL | E_STRICT | E_NOTICE);

// Shutdown functions will be called even when a fatal error occurs, which
// is how we will catch them.
register_shutdown_function('errorsFatalCatcher');
?>