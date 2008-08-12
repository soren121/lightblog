<?php session_start();define("Light", true);require('../config.php');require('corefunctions.php'); ?>
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
	<script type="text/javascript" src="includes/jquery.js"></script>
	<script type="text/javascript" src="includes/jquery-ui.js"></script>
	<script type="text/javascript">$(document).ready(function(){ $(".jqresize").resizable(); });
</script>
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
	 <?php 
	 if(isset($_POST['publish'])) {
	 	
	 	$title = clean($_POST['title']);
	 	$text = clean($_POST['text']);
	 	$date = time();
	 	$author = $_SESSION['realname'];
		if($_GET['type'] == "post") {
	 	sqlite_query($handle, "INSERT INTO posts (title,post,date,author) VALUES('".$title."','".$text."','".$date."','".$author."')") or die("SQLite query error: code 02<br>".sqlite_error_string(sqlite_last_error($handle)));
      	echo "<p>Your post has been submitted. Thank you.</p>";
		}
		elseif($_GET['type'] == "page") {
		sqlite_query($handle, "INSERT INTO pages (title,page) VALUES('".$title."','".$text."')") or die("SQLite query error: code 02<br>".sqlite_error_string(sqlite_last_error($handle)));
      	echo "<p>Your page has been submitted. Thank you.</p>";
		}
      }
	 if($_SESSION['uservip'] == "0" or !(isset($_SESSION['uservip']))) { echo'Hey, you shouldn\'t even be in here! <a href="javascript:history.go(-2)">Go back to where you came from.</a>'; }
	 if($_SESSION['uservip'] == "1") {	 	
	 echo '
	 	 <h2>Create a '.$_GET['type'].'</h2><br />
  <form action="" method="post">
    <table>
      <tr><td>Title</td><td><input name="title" type="text" maxlength="39" /></td></tr>
      <tr><td>Message:</td><td><textarea class="jqresize" name="text" cols="30" rows="10"></textarea></td></tr>
      <tr><td colspan="2"><input name="publish" type="submit" value="Publish"/></td></tr>
    </table>
  </form>'; } ?>
	</div>
</div>
</body>
</html>
