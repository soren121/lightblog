<?php session_start();

/*********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	admin/edit.php
	
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

$result07 = $dbh->query("SELECT * FROM categories ORDER BY id DESC") or die(sqlite_error_string($dbh->lastError));
$result08 = $dbh->query("SELECT * FROM ".$_GET['type']."s WHERE id=".$_GET['id']."") or die(sqlite_error_string($dbh->lastError));
while($past = $result08->fetch_object) {
	$pasttitle = $past->title;
	$pastpost = $past->post;
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title><?php bloginfo('title') ?></title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="generator" content="Geany 0.13/soren121" />
	<link rel="stylesheet" href="style/style.css" type="text/css" media="screen" />
	<!--[if IE]>
	<link rel="stylesheet" href="style/iefix.css" type="text/css" media="screen" />
	<![endif]-->
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/nicEdit.js"></script> 
	<script type="text/javascript">bkLib.onDomLoaded(function(){new nicEditor({iconsPath:'<?php bloginfo('url') ?>Sources/nicEditorIcons.gif',xhtml:true}).panelInstance('wysiwyg');});</script>
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
			$title = sqlite_escape_string($_POST['title']);
			$text = sqlite_escape_string($_POST['text']);
			// submit the changes to the database
			$dbh->query("UPDATE ".sqlite_escape_string($_GET['type'])."s SET title=\"".$title."\" , ".sqlite_escape_string($_GET['type'])."=\"".$text."\" WHERE id='".sqlite_escape_string($_GET['id'])."'") or die(sqlite_error_string($dbh->lastError));
			// update the textarea with the new changes
			$pasttitle = $title;
			$pastpost = $text;
			echo "<p>Your post has been edited. Thank you.</p>";
		}
		if($_SESSION['role'] <= 0 or !(isset($_SESSION['role']))): ?>
		Hey, you shouldn't even be in here! <a href="javascript:history.go(-2)">Go back to where you came from.</a>
		<?php else: ?>
			<h2>Editing <?php echo $pasttitle ?></h2><br />
			<form action="" method="post">
				<table>
					<tr><td>Title</td><td><input name="title" type="text" maxlength="39" value="<?php echo $pasttitle ?>" /></td></tr>
					<tr><td>Message:</td><td><textarea rows="10" cols="45" name="text" id="wysiwyg"><?php echo $pastpost ?></textarea></td></tr>
					<tr><td colspan="2"><input name="publish" type="submit" value="Save"/></td></tr>
				</table>
			</form>
		<?php endif; ?>
	</div>
</div>
</body>
</html>
