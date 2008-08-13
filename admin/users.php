<?php session_start();define("Light", true);require('../config.php');require('corefunctions.php');
$result01 = sqlite_query($handle, "SELECT * FROM users ORDER BY id desc") or die("SQLite query error: code 02<br>".sqlite_error_string(sqlite_last_error($handle)));
if(isset($_POST['promote'])) {
	sqlite_query($handle, "UPDATE users SET vip=1 WHERE id='".$_POST['p']."'") or die("SQLite query error: code 02<br>".sqlite_error_string(sqlite_last_error($handle)));
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
	<h2>Manage Users</h2><br />
	<div id="postlist">
	 <?php if($_SESSION['uservip'] == "0" or !(isset($_SESSION['uservip']))) { echo'Hey, you shouldn\'t even be in here! <a href="javascript:history.go(-2)">Go back to where you came from.</a>'; }
	 if($_SESSION['uservip'] == "1") {	 	
	// run blog post query
	if (sqlite_num_rows($result01) > 0) {
		echo'<table class="postlist">'; 
		// start post loop
		while($user = sqlite_fetch_object($result01)) {
			// start row
			echo '<tr>';
			// output ID
			echo '<td class="postlist">'.$user->id.'</td>';
			// output username
			echo '<td class="postlist">'.$user->username.'</td>';
			// output email and vip status
			echo '<td class="postlist">'.$user->email.'</td>';
			echo '<td class="postlist">'.$user->vip.'</td>';
			// output promote, ban & delete links
			echo '<td class="postlist"><form action="" method="post"><input name="p" value="p'.$user->id.'"type="hidden"/><input name="promote" type="button" value="Promote" /></form></td>';
			//echo '<td class="postlist"><form action="" method="post"><input name="b" value="p'.$user->id.'"type="hidden"/><input name="ban" type="button" value="Ban" /></form></td>';
			echo '<td class="postlist"><a style="text-align: center;" href="delete.php?type=user&id='.$user->id.'">Delete</a></td>';
			// end row
			echo '</tr>';
			// this code is repeated for every user in your database
		}
	echo '</table>';
	}
	else { echo "There's a serious problem here. The database reports there are no users!"; }
 
	 } ?>
	</div>
	</div>
</div>
</body>
</html>
