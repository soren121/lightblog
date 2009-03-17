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
require_once('../config.php');

// Open database if not open
$dbh = sqlite_popen( DBH );

// Request categories from database
$result07 = sqlite_query($dbh, "SELECT * FROM categories ORDER BY id DESC") or die("SQLite query error: code 07<br>".sqlite_error_string(sqlite_last_error($dbh)));

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
	<script type="text/javascript" src="nicedit.js"></script> 
	<script type="text/javascript">
	bkLib.onDomLoaded(function(){new nicEditor({iconsPath:'style/nicEditorIcons.gif',xhtml:true}).panelInstance('wysiwyg');});			
	$(function() {
		$('#create').submit(function() {
			var inputs = [];
			$(':input', this).each(function() {
				inputs.push(this.name + '=' + escape(this.value));
			})
			$('#create').empty().html('<' + 'img src="style/loading.gif" alt="" />');
			jQuery.ajax({
				data: inputs.join('&'),
				url: this.getAttribute('action'),
				timeout: 2000,
				error: function() {
				console.log("Failed to submit.");
				alert("Failed to submit.");
				},
				success: function(r) {
					alert('Post/page created.');
				}
			})
			return false;
		})
	})
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
	 // check if user is logged in and stop loading the page
	 if($_SESSION['uservip'] == "0" or !(isset($_SESSION['uservip']))) { echo'Hey, you shouldn\'t even be in here! <a href="javascript:history.go(-2)">Go back to where you came from.</a>'; }
	if($_SESSION['uservip'] == "1") {	
		while($cat = sqlite_fetch_object($result07)) {
			echo '<h2>Create a '.$_GET['type'].'</h2><br />
				  <form action="'.echo bloginfo('url').'\Sources\ajax.php" method="get" id="create">
						<table>
							<tr><td>Title</td><td><input name="title" type="text" maxlength="39" /></td></tr>
							<tr><td>Message:</td><td><textarea rows="10" cols="45" name="text" id="wysiwyg"></textarea></td></tr>
							<tr><td colspan="2"><input name="publish" type="submit" value="Publish"/></td></tr>
						</table>
				  </form>'; 
		}
	}
	
	// Queries done, close database
	sqlite_close($dbh);
  ?>
	</div>
</div>
</body>
</html>
