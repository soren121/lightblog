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
	$result10 = $dbh->query("SELECT salt FROM salt WHERE username='".$username."'");
	$salt = $result10->fetchSingle();
	// recreate password hash
	$password = $salt.md5($salt . $_POST['password']);
	// fetch user info from database
	$result11 = $dbh->query("SELECT * FROM users WHERE username='".$username."'") or die("Incorrect username or password!");
	while($user = $result11->fetchObject()) {
		// check if username and password are correct
		if($user->username == $username and $user->password == $password) {
			// send username, email, display name, and role to session
			$_SESSION['username'] = $user->username;
			$_SESSION['email'] = $user->email;
			$_SESSION['realname'] = $user->realname;
			$_SESSION['role'] = $user->role;
			// create new salt
			$salt = substr(md5(uniqid(rand(), true)), 0, 9);
			$hash = $salt.md5($salt . $_POST['password']);
			// submit hash to database
			$dbh->query("UPDATE salt SET salt='".$salt."', etime='".time()."';");
			// update password
			$dbh->query("UPDATE users SET password='".$hash."';");
			// send user to the dashboard	    
			header('Location: dashboard.php');
		}
		else { echo 'Incorrect username or password!'; }	
   }
}

// Process OpenID login (if used)
if(isset($_POST['openid_submit'])) {
	// load library file
	require('openidlib.php');
	// start class
	$openid = new SimpleOpenID;
	// set prefrences
	$openid->SetIdentity($_POST['openid_url']);
	$openid->SetTrustRoot($site_url);
	$openid->SetRequiredFields(array('email','fullname'));
	// find the OpenID server given
	if($openid->GetOpenIDServer()) {
		// set URL to come back to
		$openid->SetApprovedURL($site_url.'admin/login.php');
		$_SESSION['openid_url'] = $openid->GetOpenIDServer();
		// redirect to the user's provider
		$openid->Redirect();
	}
	else {
		$error = $openid->GetError();
		echo $error;
	}
}

// Validate and login the user with OpenID
if($_GET['openid_mode'] == "id_res") {
	// load library file
	require('openidlib.php');
	// start class
	$openid = new SimpleOpenID;
	$openid->SetIdentity($_GET['openid_identity']);
	// validate OpenID URL with server
	$openid_validation = $openid->ValidateWithServer();
	if($openid_validation == "true") {
		// find OpenID in database
		$openid_db_safe = $openid->OpenID_Standarize($_SESSION['openid_url']);
		$result12 = $dbh->query("SELECT openid FROM users WHERE openid='".$openid_db_safe."'");
		if($result12 == $openid_db_safe) {
			$result13 = $dbh->query("SELECT * FROM users WHERE openid='".$openid_db_safe."'");
			while($user = $result13->fetchObject()) {
				// send name and email to session
				$_SESSION['username'] = $user->username;
				$_SESSION['email'] = $user->email;
				$_SESSION['realname'] = $user->realname;
				$_SESSION['role'] = $user->role;
				// redirect to the dashboard
				header('Location: dashboard.php');
			}
		}
		else {
		$result17 = $dbh->query("SELECT * FROM users WHERE openid='".$openid_db_safe."'");
		// send name and email to session
		$_SESSION['username'] = $openid->GetAttribute('fullname');
		$_SESSION['email'] = $openid->GetAttribute('email');
		$_SESSION['realname'] = $openid->GetAttribute('fullname');
		$_SESSION['uservip'] = "0";
		// redirect to the dashboard
		header('Location: dashboard.php');
		}
	}
	else {
		echo "OpenID: Validation failed.";
	}
}
	
// Logout the user
if(isset($_GET['logout'])) {
	// destroy their session and send them to the main page
	session_destroy(); header('Location: '. ABSPATH .'\index.php');
}
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo bloginfo('title') ?> - Login</title>
<link rel="stylesheet" type="text/css" href="style/regstyle.css" />
</head>

<body>
<div id="registerbox">
<h2 style="padding-top: 5px;"><?php echo bloginfo('title') ?></h2>
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
    <div id="fragment-2">
		<form action="" method="post">
		<table style="margin-left: auto; margin-right: auto;">
		<tr><td>OpenID:</td><td><input name="openid_url" type="text" style="background-image:url(style/openid.png);background-repeat: repeat-y;padding-left:20px;" /></td></tr>
		<tr><td colspan="2"><input name="openid_submit" type="submit" value="Login"/></td></tr>	
		<tr><td colspan="2">[<a href="http://openid.net/get/">Want an OpenID?</a>]</td></tr>
		</table>
		</form>    
	</div>
</div>	
</div></body></html>
