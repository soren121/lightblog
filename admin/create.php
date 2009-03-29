<?php session_start();

/*********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	admin/create.php
	
	©2009 soren121. All rights reserved.
	Released under the GNU General
	Public License. For all licensing
	information, please see the
	LICENSE.txt document included in this
	distribution.

*********************************************/

// Open config if not open
require('../config.php');
require(ABSPATH .'/Sources/Core.php');

// Open database if not open
$dbh = new SQLiteDatabase( DBH );

// Request categories from database
$result07 = $dbh->query("SELECT * FROM categories ORDER BY id DESC") or die(sqlite_error_string($dbh->lastError));

// Create post or page
if(isset($_POST['publish'])) {
	// grab data from form and escape the text
	$title = sqlite_escape_string($_POST['title']);
	$text = sqlite_escape_string($_POST['text']);
	$date = time();
	$author = $_SESSION['realname'];
	$category = $_POST['category'];
	// insert post data
	if($_GET['type'] == "post") {
	 	$dbh->query("INSERT INTO posts (title,post,date,author,catid) VALUES('".$title."','".$text."','".$date."','".$author."','".$category."')") or die(sqlite_error_string($dbh->lastError));
      	echo "Your post has been submitted. Thank you.";
	}
	// insert page data
	elseif($_GET['type'] == "page") {
		$dbh->query("INSERT INTO pages (title,page) VALUES('".$title."','".$text."')") or die(sqlite_error_string($dbh->lastError));
      	echo "Your page has been submitted. Thank you.";
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title><?php echo bloginfo('title'); ?></title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="generator" content="Geany 0.13/soren121" />
	<link rel="stylesheet" href="style/style.css" type="text/css" media="screen" />
	<!--[if IE]>
	<link rel="stylesheet" href="style/iefix.css" type="text/css" media="screen" />
	<![endif]-->
	<script type="text/javascript" src="<?php echo bloginfo('url') ?>Sources/nicedit.js"></script> 
	<script type="text/javascript">
	bkLib.onDomLoaded(function(){new nicEditor({iconsPath:'<?php echo bloginfo('url') ?>Sources/nicEditorIcons.gif',xhtml:true}).panelInstance('wysiwyg');});			
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
	<?php if($_SESSION['role'] <= 0 or !(isset($_SESSION['role']))): ?>
	Hey, you shouldn't even be in here! <a href="javascript:history.go(-2)">Go back to where you came from.</a>
	<?php else: ?>
	<h2>Create a <?php echo $_GET['type'] ?></h2><br />
	<form action="" method="post" id="create">
		<table>
			<tr><td>Title</td><td><input name="title" type="text" maxlength="39" /></td></tr>
			<tr><td>Message:</td><td><textarea rows="10" cols="45" name="text" id="wysiwyg"></textarea></td></tr>
			<tr><td colspan="2"><input name="publish" type="submit" value="Publish"/></td></tr>
		</table>
	</form>
	<?php endif; ?>
	</div>
</div>
</body>
</html>
