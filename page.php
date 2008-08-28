<?php session_start();define("Light", true);require('config.php');require('admin/corefunctions.php');
$result05 = sqlite_query($handle, "SELECT * FROM pages WHERE id=".$_GET['id']." ORDER BY id desc") or die("SQLite query error: code 05<br>".sqlite_error_string(sqlite_last_error($handle)));
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
			<img class="headerimg" src="style/title.png" alt="<?php echo $site_name; ?>" />
		</div>
	</div>
	<?php include('sidebar.php'); ?>
	<div id="content">
	<?php
	while($page2 = sqlite_fetch_object($result05)) {
		// start post structure
		echo "<div class=\"pagebox\">";
		// output title
		echo "<h2 class=\"post-title\">".$page2->title."</h2><br />";
		// output content
		echo "<p class=\"post\">".$page2->page."</p><br /><br />";
		// end post structure
		echo "</div>";
		// this code is repeated for every post in your database
	}

	// SQLite queries done, closing database
	sqlite_close($handle);
	
	?>
	</div>
</div>
</body>
</html>
