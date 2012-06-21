<?php
/***********************************************

	LightBlog 0.9
	SQLite blogging platform

	admin/login.php

	©2008-2012 The LightBlog Team. All
	rights reserved. Released under the
	GNU General Public License 3. For
	all licensing information, please
	see the LICENSE.txt document
	included in this distribution.

***********************************************/

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
	setcookie(LBCOOKIE, '', time() - 86400);

	// Send them to the homepage
	redirect(get_bloginfo('url'));
}
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php bloginfo('title') ?> - Login</title>
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('url') ?>admin/style/loginstyle.css" />
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){
		  $("#username").focus();
		});
	</script>
</head>

<body>
	<div id="registerbox">
<?php
if(isset($messages) && count($messages) > 0)
{
	echo '
			<div style="font-weight:bold;color:#FF0000;font-size:1.2em;padding: 5px 0 5px 0;text-align:center;">';

	foreach($messages as $message)
	{
		echo '
				<p>', $message, '</p>';
	}

	echo '
			</div>';
}
?>
    	<form action="" method="post">
			<div>
				<label for="username">Username</label>
				<p><input name="username" type="text" size="16" id="username" value="<?php echo !empty($_POST['username']) ? utf_htmlspecialchars($_POST['username']) : ''; ?>" /></p>
				<label for="password">Password</label>
				<p><input name="password" type="password" size="16" id="password" value="" /></p>
				<p class="remember"><input name="remember" type="checkbox" id="rememberme" <?php echo !empty($_POST['rememberme']) ? 'checked="checked"' : ''; ?> value="1" />
				<label for="rememberme">Remember Me</label></p>
				<p><input name="proclogin" type="submit" value="Login" id="submit" /></p>
			</div>
		</form>
	</div>
</body>
</html>
