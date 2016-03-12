<?php
/*
    LightBlog, a PHP/SQLite blogging platform
    Copyright (C) 2008-2016 The LightBlog Team.
    
    Sources/CleanRequest.php

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

    Removes the $_COOKIE variable from $_REQUEST.

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

    // $_REQUEST should only contain $_POST and $_GET, no cookies!
    $_REQUEST = array_merge($_POST, $_GET);
}

// Clean up our request, then :P
clean_request();
?>
