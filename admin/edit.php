<?php session_start();define("Light", true);require('../config.php');require('corefunctions.php');
$result07 = sqlite_query($handle, "SELECT * FROM categories ORDER BY id DESC") or die("SQLite query error: code 07<br>".sqlite_error_string(sqlite_last_error($handle)));
$result08 = sqlite_query($handle, "SELECT * FROM ".$_GET['type']."s WHERE id=".$_GET['id']."") or die("SQLite query error: code 08<br>".sqlite_error_string(sqlite_last_error($handle)));// grab the post/page from the database so the user can edit it
	while($past = sqlite_fetch_object($result08)) {
		$pasttitle = $past->title;
		$pastpost = $past->post;
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
	<script type="text/javascript" src="includes/nicEdit.js"></script> 
	<script type="text/javascript">
	bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });
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
	 	// give the POSTed text variables and clean 'em!
		require_once('bbcodelib.php');
	 	$title = sqlite_escape_string($_POST['title']);
	 	$text = sqlite_escape_string($_POST['text']);
	 	// submit the changes to the database
	 	sqlite_query($handle, "UPDATE ".$_GET['type']."s SET title=\"".$title."\" , ".$_GET['type']."=\"".$text."\" WHERE id='".$_GET['id']."'") or die("SQLite query error: code 02<br>".sqlite_error_string(sqlite_last_error($handle)));
	 	// update the textarea with the new changes
	 	$pasttitle = $title;
	 	$pastpost = $text;
      	echo "<p>Your post has been edited. Thank you.</p>";
      }
	 if($_SESSION['uservip'] == "0" or !(isset($_SESSION['uservip']))) { echo'Hey, you shouldn\'t even be in here! <a href="javascript:history.go(-2)">Go back to where you came from.</a>'; }
	 if($_SESSION['uservip'] == "1") {	 		 	
	 echo '
	 	 <h2>Editing "'.$pasttitle.'"</h2><br />
  <form action="" method="post">
    <table>
      <tr><td>Title</td><td><input name="title" type="text" maxlength="39" value="'.$pasttitle.'" /></td></tr>
      <tr><td>Message:</td><td><textarea rows="10" cols="45" name="text">'.$pastpost.'</textarea></td></tr>
	  <tr><td colspan="2"><input name="publish" type="submit" value="Save"/></td></tr>
    </table>
  </form>'; } ?>
	</div>
</div>
</body>
</html>
