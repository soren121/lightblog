<?php session_start();define("Light", true);require('../config.php');require('corefunctions.php');
/* SQLite PHP Login System
 * Created and written by soren121 for LightBlog
 * Licensed under the GNU GPL v3 
 * 
 * DO NOT TOUCH ANYTHING BELOW UNLESS YOU KNOW WHAT YOU'RE DOING! */
if(isset($_POST['proclogin'])) {
	// get username and password from form
	$username = $_POST['username'];
	$password = md5($_POST['password']);
	// fetch user info from database
	$result15 = sqlite_query($handle, "SELECT * FROM users WHERE username='".$username."'") or die("Incorrect username or password!");
	while($logindata = sqlite_fetch_object($result15)) {
		// check if username and password are correct
		if($logindata->username == $username and $logindata->password == $password) {
			// send username, email, and first name to session
			$_SESSION['username'] = $username;
			$_SESSION['email'] = $logindata->email;
			$_SESSION['realname'] = $logindata->realname;
			// send user rank to session
			if($logindata->vip == "1") {
				$_SESSION['uservip'] = "1";
				$_SESSION['usernormal'] = "0";
			}
			else { $_SESSION['usernormal'] = "1";
				$_SESSION['uservip'] = "0"; }
				
			// send user to the dashboard	    
			header('Location: dashboard.php');
		}
		else { echo 'Incorrect username or password!'; }	
   }
}

// Process OpenID login
if(isset($_POST['openid_submit'])) {
	require('openidlib.php');
	$openid = new SimpleOpenID;
	$openid->SetIdentity($_POST['openid_url']);
	$openid->SetTrustRoot($site_url);
	$openid->SetRequiredFields(array('email','fullname'));
	if($openid->GetOpenIDServer()) {
		$openid->SetApprovedURL($site_url.'login.php');
		$_SESSION['openid_url'] = $_POST['openid_url'];
		$openid->Redirect();
	}
	else {
		$error = $openid->GetError();
	}
}

// Validate and login the user (with OpenID)
if($_GET['openid_mode'] == "id_res") {
	require('openidlib.php');
	$openid = new SimpleOpenID;
	$openid->SetIdentity($_GET['openid_identity']);
	$openid_validation = $openid->ValidateWithServer();
	if($openid_validation == "true") {
		$_SESSION['username'] = $_GET['openid.sreg.fullname'];
		$_SESSION['email'] = $_GET['openid.sreg.email'];
		$_SESSION['uservip'] = "0";
		header('Location: dashboard.php');
	}
	else {
		echo "OpenID: Validation failed.";
	}
}
	
// Logout the user
if(isset($_GET['logout'])) {
	// destroy their session and send them to the main page
	session_destroy(); header('Location: ../index.php');
}
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<!--	LightBlog v0.9.0
		Copyright 2008 soren121. Some Rights Reserved.
		Licensed under the General Public License v3.
		For more info, see the LICENSE.txt file included.
-->
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo $site_name; ?> - Login</title>
<link rel="stylesheet" type="text/css" href="style/regstyle.css" />
</head>

<body>
<div id="registerbox">
<h2 style="padding-top: 5px;"><?php echo $cmsinfo['site_title'] ?></h2><br />
<h3 style="padding-bottom: 10px;">Login</h3>
<?php

    echo '
    <form action="" method="post">
    <table style="margin-left: auto; margin-right: auto;">
    <tr><td>Username:</td><td><input name="username" type="text" size="16" value="'.$_GET['username'].'" /></td></tr>
    <tr><td>Password:</td><td><input name="password" type="password" size="16" /></td></tr>
    <tr><td colspan="2"><input name="proclogin" type="submit" value="Login"/></td></tr>
    <tr><td colspan="2">[<a href="register.php">Register</a>]</td></tr>
    <tr><td colspan="2">[<a href="forgotpass.php">Forgot password?</a>]<br /></td></tr>
	<tr><td>OpenID:</td><td><input name="openid_url" type="text" /></td></tr>
    <tr><td colspan="2"><input name="openid_submit" type="submit" value="Login"/></td></tr>	
    </table>
    </form>
    ';
?>
</div></body></html>
