<?php session_start();define("Light", true);require('../config.php');require('corefunctions.php');
if(isset($_POST['login'])) {
	header('Location: login.php');
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
<title><?php echo $site_name; ?> - Registration</title>
<link rel="stylesheet" type="text/css" href="style/regstyle.css" />
</head>

<body>
<div id="registerbox">
<h2 style="padding-top: 5px;"><?php echo $cmsinfo['site_title'] ?></h2><br />
<h3 style="padding-bottom: 10px;">Registration</h3>
<?php
if(!$settings['registration']=='checked') {
  $error['username'] = false;
  $error['password'] = false;
  $error['vpassword'] = false;
  $error['parse'] = true;
  $error['show'] = true;
  $error['email'] = false;
  if(isset($_POST['register'])) {
    $r['user'] = strtolower($_POST['username']);
    $r['pass'] = $_POST['password'];
    $r['vpass'] = $_POST['vpassword'];
    $r['email'] = $_POST['email'];
    $r['realname'] = $_POST['realname'];
    
    $result06 = sqlite_query($handle, "SELECT * FROM users WHERE username = '".addslashes(sqlite_escape_string($r['user']))."'") or die("SQLite query error: code 02<br>".sqlite_error_string(sqlite_last_error($handle)));
      while($row = sqlite_fetch_array($result06)) {
        $check['user'] = $row['username'];
      }
    if($check['user']==$r['user']) {
      echo '<p style="font-color: #cc0000;">Error! Username is already taken!</p>';
      $error['username'] = true;
      $error['parse'] = false;
    }
    if(!$error['username']) {
    if(strlen($r['user'])<4) {
      echo '<p style="font-color: #cc0000;">Error! Username MUST be 4 characters or longer!</p>';
      $error['username'] = true;
      $error['parse'] = false;
    }
    if(strlen($r['pass'])<6) {
      echo '<p style="font-color: #cc0000;">Error! Password must be 6 characters or longer!</p>';
      $error['password'] = true;
      $error['parse'] = false;
    }
    if(!$error['password']) {
      if($r['pass']!=$r['vpass']) {
        echo '<p style="font-color: #cc0000;">Error! Those Password\'s don\'t match!</p>';
        $error['password'] = true;
        $error['vpassword'] = true;
        $error['parse'] = false;
      }
    }
    if(!preg_match("/^([a-z0-9._-](\+[a-z0-9])*)+@[a-z0-9.-]+\.[a-z]{2,6}$/i",$r['email'])) {
      echo '<p style="font-color: #cc0000;">Error! That email address isn\'t valid!</p>';
      $error['email'] = true;
      $error['parse'] = false;
    }
    if($error['parse']) {
      $username = addslashes(sqlite_escape_string($r['user']));
      $password = md5($r['pass']);
      $email = addslashes(sqlite_escape_string($r['email']));
      $realname = addslashes(sqlite_escape_string($r['realname']));
      $ip = addslashes(sqlite_escape_string($_SERVER['REMOTE_ADDR']));
      sqlite_query($handle, "INSERT INTO users (username,password,email,realname,ip) VALUES('$username','$password','$email','$realname','$ip')") or die("SQLite query error: code 02<br>".sqlite_error_string(sqlite_last_error($handle)));
      echo '<p>Thanks for registering, '.stripslashes(stripslashes($username)).'. You may now login.</p><br /><form action="" method="post">
      		<tr><td colspan="2"><input name="login" type="submit" value="Login"/></td></tr></table></form>';
      $error['show'] = false;
    }
   }
  }
  if($error['show']) {
  echo '
  <form action="" method="post" class="registerform">
    <table>
      <tr><td>Username:</td><td '; if($error['username']) { echo 'style="border: 1px solid #cc0000"'; } echo '><input name="username" type="text" maxlength="28" value="'.$_POST['username'].'"/></td></tr>
      <tr><td>Password:</td><td '; if($error['password']) { echo 'style="border: 1px solid #cc0000"'; } echo '><input name="password" type="password"/></td></tr>
      <tr><td>Verify Password:</td><td '; if($error['vpassword']) { echo 'style="border: 1px solid #cc0000"'; } echo '><input name="vpassword" type="password"/></td></tr>
      <tr><td>Email:</td><td '; if($error['email']) { echo 'style="border: 1px solid #cc0000"'; } echo '><input name="email" type="text" value="'.$_POST['email'].'"/></td></tr>
      <tr><td>First Name:</td><td><input name="realname" type="text" maxlength="16"/></td></tr>   
      <tr><td colspan="2"><input name="register" type="submit" value="Register"/></td></tr>
    </table>
  </form>';
  }
}
else {
   echo '<p style="text-align: center;">Sorry! Registration\'s are currently closed, please come back later.</p>';
}
?>
</div></body></html>
