<?php session_start();

/*********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	admin/login.php
	
	©2009 soren121. All rights reserved.
	Released under the GNU General
	Public License. For all licensing
	information, please see the
	LICENSE.txt document included in this
	distribution.

*********************************************/

// Open config if not open
require('../config.php');
require(ABSPATH .'/Sources/Core.php');

// Process normal login
if(isset($_POST['proclogin'])) {
	// get username from form
	$username = $_POST['username'];
	// fetch salt from database
	$result10 = $dbh->query("SELECT salt FROM users WHERE username='".sqlite_escape_string($username)."'");
	$salt = $result10->fetchSingle();
	// recreate password hash
	$password = md5($salt.$_POST['password']);
	// fetch user info from database
	$result11 = $dbh->query("SELECT * FROM users WHERE username='".sqlite_escape_string($username)."'") or die("Incorrect username or password!");
	while($user = $result11->fetchObject()) {
		// check if username and password are correct
		if($user->username == $username and $user->password == $password) {
			// send username, email, display name, and role to session
			$_SESSION['username'] = $user->username;
			$_SESSION['email'] = $user->email;
			$_SESSION['realname'] = $user->displayname;
			$_SESSION['role'] = $user->role;
			// create new salt
			$salt = substr(md5(uniqid(rand(), true)), 0, 9);
			$hash = md5($salt.$_POST['password']);
			// update password and salt
			$dbh->query("UPDATE users SET password='".$hash."', salt='".$salt."';");
			// send user to the dashboard	    
			header('Location: dashboard.php');
		}
		else { echo 'Incorrect username or password!'; }	
   }
}
	
// Logout the user
if(isset($_GET['logout'])) {
	// destroy their session and send them to the main page
	session_destroy(); header('Location: '.bloginfo('url', 'r').'index.php');
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php bloginfo('title') ?> - Login</title>
<link rel="stylesheet" type="text/css" href="style/regstyle.css" />
</head>

<body>
<div id="registerbox">
<h2 style="padding-top: 5px;"><?php bloginfo('title') ?></h2>
<h3 style="padding-bottom: 5px;">Login</h3>
<div id="tabs">
    <div id="fragment-1">
        <form action="" method="post">
		<table style="margin-left: auto; margin-right: auto;">
		<tr><td>Username:</td><td><input name="username" type="text" size="16" value="<?php echo $_GET['username']; ?>" /></td></tr>
		<tr><td>Password:</td><td><input name="password" type="password" size="16" /></td></tr>
		<tr><td colspan="2"><input name="proclogin" type="submit" value="Login"/></td></tr>
		<tr><td colspan="2">[<a href="register.php">Register</a>]</td></tr>
		<tr><td colspan="2">[<a href="forgotpass.php">Forgot password?</a>]</td></tr>
		</table>
		</form>
    </div>
</div>	
</div></body></html>
