<?php
/*
    LightBlog, a PHP/SQLite blogging platform
    Copyright (C) 2008-2016 The LightBlog Team.
    
    index.php

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

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
