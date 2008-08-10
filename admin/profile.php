<?php session_start();define("Light", true);require('../config.php');require('corefunctions.php');
	
	if(isset($_POST['updpass'])) {
		$newpass = $_POST['newpass'];
		$cnfpass = $_POST['cnfpass'];
		if($newpass == $cnfpass) {
			$crtpass = md5($cnfpass);
			sqlite_query($handle, "UPDATE users SET password='".$crtpass."' WHERE username='".$_SESSION['username']."'") or die("SQLite query error: code 02<br>".sqlite_error_string(sqlite_last_error($handle)));		
			echo '<p style="color: #fff; text-align: center;">Password updated.</p>';		
		}
		else { echo'Passwords don\'t match!'; }
	}
	if(isset($_POST['updemail'])) {
		$newemail = $_POST['newemail'];
		$cnfemail = $_POST['cnfemail'];
		if($newemail == $cnfemail) {
			$crtemail = $cnfemail;
			sqlite_query($handle, "UPDATE users SET email='".$crtemail."' WHERE username='".$_SESSION['username']."'") or die("SQLite query error: code 02<br>".sqlite_error_string(sqlite_last_error($handle)));
			echo '<p style="color: #fff; text-align: center;">Email updated.</p>';
		}
		else { echo'Emails don\'t match!'; }
	}
?>
<!--	LightBlog v0.9.0
		Copyright 2008 soren121. Some Rights Reserved.
		Licensed under the General Public License v3.
		For more info, see the LICENSE.txt file included.
-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title><?php echo $site_name; ?></title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="generator" content="Geany 0.13/soren121" />
	<link rel="stylesheet" href="style/style.css" type="text/css" media="screen" />
	<!--[if IE]>
	<link rel="stylesheet" href="style/iefix.css" type="text/css" media="screen" />
	<![endif]-->
</head>

<body>
<div id="container">
	<div id="header">
		<div id="headerimg">
			<img class="headerimg" src="style/title.png" alt="LightBlog" />
		</div>
	</div>
	<?php include('admside.php'); ?>
	<div id="content">
	<h2>Profile</h2><br />
	<table><tr>
	<td><img src="<?php echo $gravatar; ?>" alt="Gravatar" /></td>
	<td class="profileinfo">Username: <?php echo $_SESSION['username']; ?><br />
	Email: <?php echo $_SESSION['email']; ?><br />
	Real Name: <?php echo $_SESSION['realname']; ?><br />
	Rank: <?php if($_SESSION['uservip'] == 1) { echo'Admin'; } else { echo 'Normal'; } ?></td></tr></table><br />
		<?php if(isset($_SESSION['openid_url'])) { echo ''; } else { echo '
		<h3>Update password</h3><br />
			<form action="" method="post">
      		<table>
      		<tr><td>New password: </td><td colspan="2"><input name="newpass" type="password" /></td></tr>
      		<tr><td>Confirm password: </td><td colspan="2"><input name="cnfpass" type="password" /></td></tr>
      		<tr><td>&nbsp;</td><td colspan="2"><input name="updpass" type="submit" value="Update"/></td></tr>
    		</table></form><br />
		<h3>Update email</h3><br />
			<form action="" method="post">
      		<table>
      		<tr><td>New email: </td><td colspan="2"><input name="newemail" type="text" /></td></tr>
      		<tr><td>Confirm email: </td><td colspan="2"><input name="cnfemail" type="text" /></td></tr>
      		<tr><td>&nbsp;</td><td colspan="2"><input name="updemail" type="submit" value="Update"/></td></tr>
    		</table></form>
			'; } ?>
	</div>
</div>
</body>
</html>
