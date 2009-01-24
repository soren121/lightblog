<?php session_start();define("Light", true);require('../config.php');require('corefunctions.php');
	while($past = sqlite_fetch_object($result08)) {
		$pasttitle = $past->title;
		$pastpost = $past->post;
	}
	
	if(isset($_POST['delete'])) {
		sqlite_query($handle, "DELETE FROM ".$_GET['type']."s WHERE id='".$_GET['id']."'") or die("SQLite query error: code 02<br>".sqlite_error_string(sqlite_last_error($handle)));
		if($_GET['type'] == "post") {
		sqlite_query($handle, "DELETE FROM comments WHERE post_id='".$_GET['id']."'") or die("SQLite query error: code 02<br>".sqlite_error_string(sqlite_last_error($handle)));
		}
		header('Location: manage.php?type='.$_GET['type'].'');
	}
	
	if(isset($_POST['goback'])) {
		header('Location: manage.php?type='.$_GET['type'].'');
	}	
?>
<!--	LightBlog v0.9.0
		Copyright 2009 soren121. Some Rights Reserved.
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
	<h2>Delete <?php echo $_GET['type']." \"".$pasttitle."\"?"; ?></h2><br />
	 <?php if($_SESSION['uservip'] == "0" or !(isset($_SESSION['uservip']))) { echo'Hey, you shouldn\'t even be in here! <a href="javascript:history.go(-2)">Go back to where you came from.</a>'; }
	 if($_SESSION['uservip'] == "1") {	 	
		echo'<p>Are you sure you want to delete this '.$_GET['type'].'?</p><br />
		  <form action="" method="post">
      		<tr><td colspan="2"><input name="delete" type="submit" value="Delete it!"/></td>
      		<td colspan="2"><input name="goback" type="submit" value="No!"/></td>
      		</tr>
    		</table>
  		  </form><br />'; } ?>
	</div>
</div>
</body>
</html>
