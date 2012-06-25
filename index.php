<?php
/*********************************************

	LightBlog 0.9
	SQLite blogging platform

	index.php

	©2008-2012 The LightBlog Team. All
	rights reserved. Released under the
	GNU General Public License 3. For
	all licensing information, please
	see the LICENSE.txt document
	included in this distribution.

*********************************************/

// This constant will allows us to include files that can't be viewed
// directly.
define('INLB', true);

// We definitely need this, it will setup everything we need.
require('Sources/Core.php');

// Which theme are you using?
$themeName = get_bloginfo('theme');

// This could be fatal!
if(!file_exists('themes/'. basename($themeName). '/main.php'))
{
	trigger_error('The theme "'. utf_htmlspecialchars($themeName). '" does not exist', E_USER_ERROR);
}

// If it isn't a post or page we're showing, then make a list of posts.
if(!isset($_GET['post']) && !isset($_GET['page']))
{
	// Require the proper loop class
	require(ABSPATH .'/Sources/Class.PostLoop.php');

	// Pagination variables
	$file = basename($_SERVER['SCRIPT_FILENAME']);
	if(isset($_GET['p']))
	{
		$page = (int)$_GET['p'];
	}
	else
	{
		$page = 1;
	}

	// Display the right post view
	if(isset($_GET['archive']))
	{
		$GLOBALS['postquery']['type'] = 'archive';
		$GLOBALS['postquery']['date'] = (int)$_GET['archive'];
	}
	elseif(isset($_GET['category']))
	{
		$GLOBALS['postquery']['type'] = 'category';
		$GLOBALS['postquery']['catid'] = (int)$_GET['category'];
	}
	else
	{
		$GLOBALS['postquery']['type'] = 'latest';
	}

	// Include main theme file
	include('themes/'. $themeName. '/main.php');
}

// Looks like it is a post or page
else
{
	if(isset($_GET['post']))
	{
		function formCallback($response)
		{
			if(!empty($response))
			{
				if($response['result'] == 'error' || $response['result'] == 'success')
				{
					if(isset($response['response']))
					{
						return $response['response'];
					}
				}
				else
				{
					return 'No response from form processor.';
				}
			}
			return;
		}
		
		// Require the proper loop class
		require(ABSPATH .'/Sources/Class.PostLoop.php');
		require(ABSPATH .'/Sources/Class.CommentLoop.php');
		require(ABSPATH .'/Sources/Process.php');
		$_SESSION['cmessage'] = formCallback(processForm($_POST));

		// Get post ID
		$GLOBALS['pid'] = (int)$_GET['post'];
		$GLOBALS['postquery']['type'] = 'post';

		// Display appropriate theme file
		include('themes/'.$themeName.'/post.php');
	}

	elseif(isset($_GET['page']))
	{
		// Require the proper loop class
		require(ABSPATH .'/Sources/Class.PageLoop.php');

		// Get page ID
		$GLOBALS['pid'] = (int)$_GET['page'];
		$GLOBALS['postquery']['type'] = 'page';

		// Display appropriate theme file
		include('themes/'.$themeName.'/page.php');
	}
}

?>