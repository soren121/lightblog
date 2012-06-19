<?php session_start();

/*********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	admin/login.php
	
	©2008-2012 The LightBlog Team. All 
	rights reserved. Released under the 
	GNU General Public License 3. For 
	all licensing information, please 
	see the LICENSE.txt document 
	included in this distribution.

*********************************************/

// Open config if not open
require('../config.php');
require(ABSPATH .'/Sources/Core.php');

// Check for cookies
if(isset($_COOKIE['username']) && isset($_COOKIE['password'])) {
	// set cookie info
	$c_username = $_COOKIE['username'];
	$c_password = $_COOKIE['password'];
}

// Process normal login
if(isset($_POST['proclogin'])) {
	login('userpass');
}
	
// Logout the user
if(isset($_GET['logout'])) {
	// Destroy the session
	session_destroy();
	// Send them to the homepage
	header('Location: '.bloginfo('url', 'r'));
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title><?php bloginfo('title') ?> - Login</title>
	<link rel="stylesheet" type="text/css" href="style/loginstyle.css" />
</head>

<body>
	<div id="registerbox">
		<?php if(isset($_POST['proclogin'])): $c_username = htmlspecialchars($_POST['username']); ?>
			<div style="font-weight:bold;color:#FF0000;font-size:1.2em;padding:-5px 0 5px 0;text-align:center;">
				Incorrect Username or Password
			</div>
		<?php endif; ?>
    	<form action="" method="post">
			<div>
				<label for="username">Username</label>
				<p><input name="username" type="text" size="16" id="username" value="<?php if(isset($c_username)){echo $c_username;} ?>" /></p>
				<label for="password">Password</label>
				<p><input name="password" type="password" size="16" id="password" value="<?php if(isset($c_password)){echo $c_password;} ?>" /></p>
				<p class="remember"><input name="remember" type="checkbox" id="rememberme" />
				<label for="rememberme">Remember Me</label></p>
				<p><input name="proclogin" type="submit" value="Login" id="submit" /></p>
			</div>
		</form>	
	</div>
</body>
</html>
