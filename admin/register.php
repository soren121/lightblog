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

// Process registration
if(isset($_POST['register'])) {
	$username = strtolower($_POST['username']);
	$password = $_POST['password'];
	$vpassword = $_POST['vpassword'];
	$email = $_POST['email'];
	$realname = $_POST['realname'];
	$captchacode = $_POST['captchacode'];
   
	$result06 = $dbh->query("SELECT * FROM users WHERE username = '".addslashes(sqlite_escape_string($r['user']))."'") or die("SQLite query error: code 02<br>".sqlite_error_string(sqlite_last_error($handle)));
    while($row = $result06->fetch(SQLITE_ASSOC)) {
      $check['user'] = $row['username'];
    }
	if($check['user'] == $username) { $error = true; }
	if(!isset($username{6})) { $error = true; }
	if(!isset($password{4})) { $error = true; }
	if(!$password == $vpassword) { $error = true; }
	if(!preg_match("/^([a-z0-9._-](\+[a-z0-9])*)+@[a-z0-9.-]+\.[a-z]{2,6}$/i",$email)) { $error = true; }
	if(!empty($_SESSION['freecap_word_hash']) && !empty($_POST['word'])) {
		if($_SESSION['hash_func'](strtolower($_POST['word']))==$_SESSION['freecap_word_hash']) {
			$_SESSION['freecap_attempts'] = 0; $_SESSION['freecap_word_hash'] = false;
		}
		else {
			$error = true;
		}
	}
	else {
		$error = true;
	}
	if(!isset($error)) {
		$username = addslashes(sqlite_escape_string($username));
		$password = md5($password);
		$email = addslashes(sqlite_escape_string($email));
		$realname = addslashes(sqlite_escape_string($realname));
		$ip = addslashes(sqlite_escape_string($_SERVER['REMOTE_ADDR']));
		$dbh->query("INSERT INTO users (username,password,email,realname,vip,ip) VALUES('$username','$password','$email','$realname',0,'$ip')") or die(sqlite_error_string($dbh->lastError));
		$wnotice = true;
	}
}
if(isset($_POST['login'])) {
	header('Location: login.php');
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<!--	LightBlog v0.9.0
		Copyright 2009 soren121. Some Rights Reserved.
		Licensed under the General Public License v3.
		For more info, see the LICENSE.txt file included.
-->
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo bloginfo('title'); ?> - Registration</title>
<link rel="stylesheet" type="text/css" href="style/regstyle.css" />
<script type="text/javascript" src="<?php echo bloginfo('url') ?>Sources/jquery.js"></script>
<script type="text/javascript" src="includes/jquery-pstrength.js"></script>
<script type="text/javascript">
$(function() {$('.password').pstrength();});

function new_freecap()
{
	// loads new freeCap image
	if(document.getElementById)
	{
		// extract image name from image source (i.e. cut off ?randomness)
		thesrc = document.getElementById("freecap").src;
		thesrc = thesrc.substring(0,thesrc.lastIndexOf(".")+4);
		// add ?(random) to prevent browser/isp caching
		document.getElementById("freecap").src = thesrc+"?"+Math.round(Math.random()*100000);
	} else {
		alert("Sorry, cannot autoreload freeCap image\nManually reload the page and a new freeCap will be loaded");
	}
}
</script>
<style type="text/css">
.password {
font-size: 12px;
border: 1px solid #cc9933;
width: 145px;
font-family: arial, sans-serif;
}
.pstrength-minchar {
font-size: 10px;
}</style>
</head>

<body>
<div id="registerbox">
<h2 style="padding-top: 5px;"><?php echo bloginfo('title'); ?></h2><br />
<h3 style="padding-bottom: 10px;">Registration</h3>
  <?php if($wnotice == true) { echo '<p>Thanks for registering, '.stripslashes(stripslashes($username)).'. You may now login.</p><br /><form action="" method="post">
      	<p><input name="login" type="submit" value="Login"/></p>'; } else { echo '
  <form action="" method="post" class="registerform">
    <table>
      <tr><td>Username:</td><td><input name="username" type="text" maxlength="28" value="'.$_POST['username'].'"/></td></tr>
      <tr><td>Password:</td><td><input class="password" name="password" type="password"/></td></tr>
      <tr><td>Verify Password:</td><td><input name="vpassword" type="password"/></td></tr>
      <tr><td>Email:</td><td><input name="email" type="text" value="'.$_POST['email'].'"/></td></tr>
      <tr><td>Display Name:</td><td><input name="realname" type="text" maxlength="16"/></td></tr> 
	  <tr><td>CAPTCHA:</td><td><img src="'.bloginfo('url').'Sources/freecap.php" id="freecap" alt="Failed to load CAPTCHA" />
	  <a href="#" onclick="this.blur();new_freecap();return false;"><img src="style/refresh.png" style="border: 1px solid #ccc;" alt="Reload" /></a></td></tr>
	  <tr><td>CAPTCHA Code:</td><td><input type="text" name="word" size="6" /></td></tr>
      <tr><td colspan="2"><input name="register" type="submit" value="Register"/></td></tr>
    </table>
  </form>'; } ?>
</div></body></html>
