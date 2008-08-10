<?php session_start();define("Light", true);require('../config.php');require('corefunctions.php');
/* SQLite PHP Login System
 * Created and written by soren121 for LightBlog
 * Licensed under the GNU GPL v3 
 * 
 * DO NOT TOUCH ANYTHING BELOW UNLESS YOU KNOW WHAT YOU'RE DOING! */
function createRandomPassword() {
    $chars = "abcdefghijkmnopqrstuvwxyz023456789";
    srand((double)microtime()*1000000);
    $i = 0;
    $pass = '' ;
    while ($i <= 7) {
        $num = rand() % 27;
        $tmp = substr($chars, $num, 1);
        $pass = $pass . $tmp;
        $i++;
    }
    return $pass;
}
$p4a7s2s8 = createRandomPassword();

if(isset($_POST['forgotpass'])) {
	$result3 = sqlite_query($handle, "SELECT * FROM users WHERE username='".$_POST['username']."'") or die("SQLite query error: code 02<br>".sqlite_error_string(sqlite_last_error($handle)));
	while($s3cr37 = sqlite_fetch_object($result3)) { $email = $s3cr37->email; }
	if($_POST['email'] == $email) {
	sqlite_query($handle, "UPDATE users SET password='".md5($p4a7s2s8)."' WHERE username='".$_POST['username']."'") or die("SQLite query error: code 02<br>".sqlite_error_string(sqlite_last_error($handle)));
	$password = $p4a7s2s8;
	}
	else { die("Email incorrect. <a href=\"forgotpass.php\">Try again.</a>"); }
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
<h2 style="padding-top: 5px;"><?php echo $site_name; ?></h2><br />
<h3 style="padding-bottom: 10px;">Forgot Password</h3>
<?php
	if(isset($password)) {
		echo '<p>Your new password is '.$password.'</p><br /><p>You can change it in your profile once you login.</p><br />
		<form action="login.php" method="get"><input name="username" type="hidden" value="'.$_POST['username'].'" />
		<input name="password" type="hidden" value="'.$password.'" />
		<input type="submit" value="Login" /></form> ';
	}
	if(!(isset($password))) {	
    echo '
	<form action="" method="post">
    <table style="margin-left: auto; margin-right: auto;">
    <tr><td>Username: </td><td colspan="2"><input name="username" type="text" /></td></tr>
    <tr><td>Email: </td><td colspan="2"><input name="email" type="text" /></td></tr>
    <tr><td>&nbsp;</td><td colspan="2"><input name="forgotpass" type="submit" value="Submit"/></td></tr>
    </table>
    '; }
?>
</div></body></html>
