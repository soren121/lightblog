<?php
/*
    LightBlog, a PHP/SQLite blogging platform
    Copyright (C) 2008-2016 The LightBlog Team.
    
    admin/login.php

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

// Require config file
require('../Sources/Core.php');

// Do we need to process their log in request?
if(!empty($_POST['proclogin']))
{
    $messages = user_login(array(
        'username' => isset($_POST['username']) ? $_POST['username'] : '',
        'password' => isset($_POST['password']) ? $_POST['password'] : '',
        'remember_me' => !empty($_POST['rememberme']),
        'redir_to' => !empty($_REQUEST['return_to']) ? $_REQUEST['return_to'] : '',
    ));
}

// Logout the user
if(isset($_GET['logout']))
{
    // Destroy the session
    session_destroy();

    // Unset their cookie, too.
    setcookie(LBCOOKIE, '', time() - 2592000, '/');

    // Send them to the homepage
    redirect(get_bloginfo('url'));
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo l('Log In'); ?> // <?php bloginfo('title') ?> &mdash; LightBlog</title>
    <link rel="stylesheet" type="text/css" href="<?php bloginfo('url') ?>admin/style/main.css" />
    <link rel="stylesheet" type="text/css" href="<?php bloginfo('url') ?>admin/style/login.css" />
    <script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.js"></script>
</head>

<body>
    <?php

    if(isset($messages) && count($messages) > 0)
    {
        echo '<div id="login-response">';

        foreach($messages as $message)
        {
            echo '<p>', $message, '</p>';
        }

        echo '</div>';
    }

    ?>

    <div id="login-container">
        <div id="login-header">
            <h2 id="blogtitle"><a href="<?php bloginfo('url') ?>"><?php bloginfo('title') ?></a></h2>
            <h2 id="section">// <?php echo l('Log In'); ?></h2>
        </div>
        <form action="" method="post">
            <div>
                <div>
                    <label for="username"><?php echo l('Username'); ?></label>
                    <input name="username" type="text" id="username" value="<?php echo !empty($_POST['username']) ? utf_htmlspecialchars($_POST['username']) : ''; ?>" />
                    <div class="clear"></div>
                </div>
                <div>
                    <label for="password"><?php echo l('Password'); ?></label>
                    <input name="password" type="password" id="password" value="" />
                    <div class="clear"></div>
                </div>
                <span>
                    <input name="remember" type="checkbox" id="rememberme" <?php echo !empty($_POST['rememberme']) ? 'checked="checked"' : ''; ?> value="1" />
                    <label for="rememberme"><?php echo l('Remember Me'); ?></label>
                </span>
                <input name="proclogin" type="submit" value="<?php echo l('Log In'); ?>" class="submit" />
                <div class="clear" style="padding-top:15px;">
                    <a class="secondary-button" href="<?php bloginfo('url') ?>" style="float: left;">&laquo; <?php echo l('Back'); ?></a>
                    <?php //if(get_bloginfo('allow_registration')): ?>
                        <!--<a class="secondary-button" href="#" style="float: right;padding:3px 34px;">Register</a>-->
                    <?php //endif; ?>
                    <div class="clear"></div>
                </div>
            </div>
        </form>
    </div>

    <script type="text/javascript">
        $('#username').focus();
    </script>
</body>
</html>
