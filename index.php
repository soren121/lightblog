<?php session_start();define("Light", true);require('config.php');require('admin/corefunctions.php');
// set post query
$result01 = sqlite_query($handle, "SELECT * FROM posts ORDER BY id desc") or die("SQLite query error: code 02<br>".sqlite_error_string(sqlite_last_error($handle)));
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
	// run blog post query
	if (sqlite_num_rows($result01) > 0) {
		// start post loop
		while($post = sqlite_fetch_object($result01)) {
			// timestamp for date
			$timestamp = $post->date;
			// result for comments link
			$result12 = sqlite_query($handle, "SELECT * FROM comments WHERE post_id=".$post->id."") or die("SQLite query error: code 02<br>".sqlite_error_string(sqlite_last_error($handle)));
			// start post structure
			echo "<div class=\"postbox\">";
			// output title
			echo "<h2 class=\"post-title\"><a class=\"post-title\" href=\"post.php?id=".$post->id."\">".$post->title."</a></h2>";
			// output comment link, author name & date
			echo "<img src=\"style/date.png\" alt=\"Date\" /><span class=\"date\">".date("F j, Y", $timestamp)."</span>";
			echo "<img src=\"style/user.png\" alt=\"Written by\" /><span class=\"author\">".$post->author."</span>";
			echo "<img src=\"style/comment.png\" alt=\"Comments\" /><a href=\"post.php?id=".$post->id."\" title=\"Post a comment or read them!\"><span class=\"commentnum\">".sqlite_num_rows($result12)." Comments</span></a>";
			// output content
			echo "<p class=\"post\">".$post->post."</p><br />";
			// end post structure
			echo "</div>";
			// this code is repeated for every post in your database
		}
	}
	else { echo "No posts, sorry."; }
	
	// SQLite queries done, closing database
	sqlite_close($handle);
	
	?>
	</div>
</div>
</body>
</html>
