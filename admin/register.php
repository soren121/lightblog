<?php session_start();define("Light", true);require('../config.php');require('corefunctions.php');
if(isset($_POST['register'])) {
	$username = strtolower($_POST['username']);
	$password = $_POST['password'];
	$vpassword = $_POST['vpassword'];
	$email = $_POST['email'];
	$realname = $_POST['realname'];
	$captchacode = $_POST['captchacode'];
   
	$result06 = sqlite_query($handle, "SELECT * FROM users WHERE username = '".addslashes(sqlite_escape_string($r['user']))."'") or die("SQLite query error: code 02<br>".sqlite_error_string(sqlite_last_error($handle)));
    while($row = sqlite_fetch_array($result06)) {
      $check['user'] = $row['username'];
    }
	if(!$check['user'] == $username) { $error1 = false; }
	if(isset($username{6})) { $error2 = false; }
	if(isset($password{4})) { $error3 = false; }
	if($password == $vpassword) { $error4 = false; }
	if(preg_match("/^([a-z0-9._-](\+[a-z0-9])*)+@[a-z0-9.-]+\.[a-z]{2,6}$/i",$email)) { $error5 = false; }
	if($_SESSION['string'] == $captchacode) { $error6 = false; }
	if($error1 == false && $error2 == false && $error3 == false && $error4 == false && $error5 == false && $error6 == false) {
		$username = addslashes(sqlite_escape_string($username));
		$password = md5($password);
		$email = addslashes(sqlite_escape_string($email));
		$realname = addslashes(sqlite_escape_string($realname));
		$ip = addslashes(sqlite_escape_string($_SERVER['REMOTE_ADDR']));
		sqlite_query($handle, "INSERT INTO users (username,password,email,realname,vip,ip) VALUES('$username','$password','$email','$realname',0,'$ip')") or die("SQLite query error: code 02<br>".sqlite_error_string(sqlite_last_error($handle)));
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
<title><?php echo $site_name; ?> - Registration</title>
<link rel="stylesheet" type="text/css" href="style/regstyle.css" />
<script type="text/javascript" src="includes/jquery.js"></script>
<script type="text/javascript" src="includes/jquery-pstrength.js"></script>
<script type="text/javascript">$(function() {$('.password').pstrength();});</script>
<style type="text/css">
.password {
font-size : 12px;
border : 1px solid #cc9933;
width : 145px;
font-family : arial, sans-serif;
}
.pstrength-minchar {
font-size : 10px;
}</style>
</head>

<body>
<div id="registerbox">
<h2 style="padding-top: 5px;"><?php echo $cmsinfo['site_title'] ?></h2><br />
<h3 style="padding-bottom: 10px;">Registration</h3>
<?php if($error1 == true){echo'Username is already taken!';}if($error2 == true){echo'Your username is too short! Please make it 4 characters or longer.';}if($error2 == true){echo'Your password is too short! Please make it at least 6 characters long.';}
if($error4 == true){echo'Your passwords don\'t match!';}if($error5 == true){echo'Your email address is invalid. Please fix it.';}if($error6 = true && isset($_POST['register'])){echo'You entered the CAPTCHA wrong. Try again.';} ?>
  <?php if($wnotice == true) { echo '<p>Thanks for registering, '.stripslashes(stripslashes($username)).'. You may now login.</p><br /><form action="" method="post">
      	<p><input name="login" type="submit" value="Login"/></p>'; } else { echo '
  <form action="" method="post" class="registerform">
    <table>
      <tr><td>Username:</td><td><input name="username" type="text" maxlength="28" value="'.$_POST['username'].'"/></td></tr>
      <tr><td>Password:</td><td><input class="password" name="password" type="password"/></td></tr>
      <tr><td>Verify Password:</td><td><input name="vpassword" type="password"/></td></tr>
      <tr><td>Email:</td><td><input name="email" type="text" value="'.$_POST['email'].'"/></td></tr>
      <tr><td>First Name:</td><td><input name="realname" type="text" maxlength="16"/></td></tr> 
	  <tr><td>CAPTCHA:</td><td><img src="captchalibsettings.php" alt="CAPTCHA" /></td></tr>
	  <tr><td>CAPTCHA Code:</td><td><input type="text" name="code" size="8" /></td></tr>
      <tr><td colspan="2"><input name="register" type="submit" value="Register"/></td></tr>
    </table>
  </form>'; } ?>
</div></body></html>
