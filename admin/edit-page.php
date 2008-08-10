<?php session_start();define("Light", true);require('../config.php');require('corefunctions.php');
$result16 = sqlite_query($handle, "SELECT * FROM pages WHERE id=".$_GET['id']."") or die("SQLite query error: code 02<br>".sqlite_error_string(sqlite_last_error($handle)));
	while($past = sqlite_fetch_object($result16)) {
		$pasttitle = $past->title;
		$pastpage = $past->page;
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
	<title>A Chung Story</title>
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
	 <?php 
	 if(isset($_POST['publish'])) {
	 	
	 	$title1 = $_POST['title'];
	 	$title = stripslashes($title1);
	 	$text1 = $_POST['text'];
	 	$text = stripslashes($text1);
	 	sqlite_query($handle, "UPDATE pages SET title='".$title."' WHERE id='".$id."'") or die("SQLite query error: code 02<br>".sqlite_error_string(sqlite_last_error($handle)));
	 	sqlite_query($handle, "UPDATE pages SET page='".$text."' WHERE id='".$id."'") or die("SQLite query error: code 02<br>".sqlite_error_string(sqlite_last_error($handle)));
      	echo "<p>Your page has been edited. Thank you.</p>";
      }
	 if($_SESSION['uservip'] == "0" or !(isset($_SESSION['uservip']))) { echo'Hey, you shouldn\'t even be in here! <a href="javascript:history.go(-2)">Go back to where you came from.</a>'; }
	 if($_SESSION['uservip'] == "1") {	 		 	
	 echo '
	 	 <h2>Editing: '.$pasttitle.'</h2><br />
  <form action="" method="post">
    <table>
      <tr><td>Title</td><td><input name="title" type="text" maxlength="39" value="'.$pasttitle.'" /></td></tr>
      <tr><td>Page:</td><td><textarea name="text" cols="30" rows="10">'.$pastpage.'</textarea></td></tr>
      <tr><td colspan="2"><input name="publish" type="submit" value="Publish"/></td></tr>
    </table>
  </form>'; } ?>
	</div>
</div>
</body>
</html>
