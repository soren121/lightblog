<?php session_start();define("Light", true);require('../config.php');require('corefunctions.php');
/*    	LightBlog v0.9.0
		Copyright 2009 soren121. Some Rights Reserved.
		Licensed under the General Public License v3.
		For more info, see the LICENSE.txt file included.
*/

// Create post or page
if(isset($_REQUEST['publish'])) {
	// grab data from form and escape the text
	$title = sqlite_escape_string($_REQUEST['title']);
	$text = sqlite_escape_string($_REQUEST['text']);
	$date = time();
	$author = $_SESSION['realname'];
	$category = $_REQUEST['category'];
	// insert post data
	if($_GET['type'] == "post") {
	 	sqlite_query($handle, "INSERT INTO posts (title,post,date,author,catid) VALUES('".$title."','".$text."','".$date."','".$author."','".$category."')") or die("SQLite query error: code 02<br>".sqlite_error_string(sqlite_last_error($handle)));
      	echo "Your post has been submitted. Thank you.";
	}
	// insert page data
	elseif($_GET['type'] == "page") {
		sqlite_query($handle, "INSERT INTO pages (title,page) VALUES('".$title."','".$text."')") or die("SQLite query error: code 02<br>".sqlite_error_string(sqlite_last_error($handle)));
      	echo "Your page has been submitted. Thank you.";
	}
	die();
}

// Update password
if(isset($_REQUEST['updpass'])) {
	$newpass = $_REQUEST['newpass'];
	$cnfpass = $_REQUEST['cnfpass'];
	if($newpass == $cnfpass) {
		$crtpass = md5($cnfpass);
		sqlite_query($handle, "UPDATE users SET password='".$crtpass."' WHERE username='".$_SESSION['username']."'") or die("SQLite query error: code 02<br>".sqlite_error_string(sqlite_last_error($handle)));		
		echo '<p style="color: #fff; text-align: center;">Password updated.</p>';		
	}
	else { echo'Passwords don\'t match!'; }
}

// Update email
if(isset($_REQUEST['updemail'])) {
	$newemail = $_REQUEST['newemail'];
	$cnfemail = $_REQUEST['cnfemail'];
	if($newemail == $cnfemail) {
		$crtemail = $cnfemail;
		sqlite_query($handle, "UPDATE users SET email='".$crtemail."' WHERE username='".$_SESSION['username']."'") or die("SQLite query error: code 02<br>".sqlite_error_string(sqlite_last_error($handle)));
		echo '<p style="color: #fff; text-align: center;">Email updated.</p>';
	}
	else { echo 'Emails don\'t match!'; }
}

// Link an OpenID with a regular account
if(isset($_REQUEST['linkopenid'])) {
	require('openidlib.php');
	$openid = new SimpleOpenID;
	$openid = $openid->OpenID_Standarize($_SESSION['openid_url']);
	sqlite_query($handle, "UPDATE users SET openid='".$openid."' WHERE username='".$_REQUEST['username']."' AND password='".md5($_REQUEST['password'])."'") or die("SQLite query error: code 02<br>".sqlite_error_string(sqlite_last_error($handle)));
}

?>