<?php session_start();define("Light", true);require('config.php');require('admin/corefunctions.php');
$result03 = sqlite_query($handle, "SELECT * FROM posts WHERE id=".$_GET['id']." ORDER BY id desc") or die("SQLite query error: code 03<br>".sqlite_error_string(sqlite_last_error($handle)));
$result04 = sqlite_query($handle, "SELECT * FROM comments WHERE post_id=".$_GET['id']." ORDER BY id desc") or die("SQLite query error: code 04<br>".sqlite_error_string(sqlite_last_error($handle)));
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
	<script type="text/javascript" src="admin/includes/bbcode-editor.js"></script>
	<script type="text/javascript" src="admin/includes/jquery.js"></script>
	<script type="text/javascript" src="admin/includes/jquery-ui.js"></script>
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
	if (sqlite_num_rows($result03) > 0) {
		// start post loop
		while($post = sqlite_fetch_object($result03)) {
			// start post structure
			echo "<div class=\"postbox\">";
			// output title
			echo "<h2 class=\"post-title\">".$post->title."</h2>";
			// output content
			echo "<p class=\"post\">".$post->post."</p><br />";
			// end post structure
			echo "</div>";
			// this code is repeated for every post in your database
		}
	}
	
	// get comments
	// if there are no comments:
	if(sqlite_num_rows($result04) == 0) {
		echo "<p>No comments have been made on this post yet.</p>";
	}
	
	// if comments exist, display them
	else { 
		while($comments = sqlite_fetch_array($result04)) {
	    $grav_default="http://use.perl.org/images/pix.gif";
	    $grav_url = "http://www.gravatar.com/avatar.php?gravatar_id=".md5($comments['email'])."&amp;default=".urlencode($grav_default)."&amp;size=30";
		echo "<div class=\"comment\">
			  <img src=\"".$grav_url."\" style=\"float:right;margin-right: 20px;\" alt=\"Gravatar\" />
		      <b><i>".$comments['username']."</i></b> says:<br />
		      <p class=\"com-content\">".$comments['text']."</p>
		      </div>";
		}
	}
	
	// check if comment has been POSTed
	if(isset($_POST['comment_submit'])) {					 	
	 	$com_name = $_POST['username'];
	 	$com_email = $_POST['email'];
	 	$com_website = $_POST['website'];
	 	$com_text = $_POST['text'];
	 	sqlite_query($handle, "INSERT INTO comments (post_id,username,email,website,text) VALUES('".$_GET['id']."','".$com_name."','".$com_email."','".$com_website."','".$com_text."')") or die("SQLite query error: code 02<br>".sqlite_error_string(sqlite_last_error($handle)));
		echo "<p>Your comment has been submitted. Thank you.</p>";
      }
      
    // get comment FORM
	echo '
	<h4 class="commentform-title">Post a comment</h4><br />
	<form action="" method="post">
    			<table>
      				<tr><td>Name:</td><td><input name="username" type="text" maxlength="28" /></td></tr>
      				<tr><td>Email:</td><td><input name="email" type="text"/></td></tr>
      				<tr><td>Website:</td><td><input name="website" type="text"/></td></tr>
      				<tr><td>Post:</td><td><script type="text/javascript">Init(\'text\',30,10,\'\',\'out\'); $(document).ready(function(){ $("#text").resizable(); });</script></td></tr>';
      				echo '<tr><td colspan="2"><input name="comment_submit" type="submit" value="Submit"/></td></tr>
    			</table>
  				</form>';

	// SQLite queries done, closing database
	sqlite_close($handle);
	
	?>
	</div>
</div>
</body>
</html>
