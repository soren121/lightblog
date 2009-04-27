<?php session_start();

/*********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	admin/design.php
	
	©2009 soren121. All rights reserved.
	Released under the GNU General
	Public License. For all licensing
	information, please see the
	LICENSE.txt document included in this
	distribution.

*********************************************/

// Require config file
require('../config.php');
require(ABSPATH .'/Sources/Core.php');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Design - <?php bloginfo('title') ?></title>
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('url') ?>admin/style/style.css" />
	<!--[if lte IE 7]><style type="text/css">html.jqueryslidemenu { height: 1%; }</style><![endif]-->
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.js"></script>
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.SlideMenu.js"></script>
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.Corners.js"></script>
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.InputHint.js"></script>
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/nicEdit.js"></script> 
	<script type="text/javascript">	
		$(document).ready(function(){
			$('.rounded').corner(); 
			$('.roundedt').corner("round top 10px"); 
			$('.roundedb').corner("round bottom 10px");
		});
		$(function() {
			$('#themeform').submit(function() {
				var inputs = [];
				$(':input', this).each(function() {
					inputs.push(this.name + '=' + escape(this.value));
				})
				jQuery.ajax({
					data: inputs.join('&'),
					type: "POST",
					url: this.getAttribute('action'),
					timeout: 2000,
					error: function() {
						console.log("Failed to submit");
						alert("Failed to submit.");
					},
					success: function(r) {
						alert('Theme changed.');
					}
				})
				return false;
			})
		});
	</script>
</head>

<body>
	<div id="wrapper">
		<div id="header" class="roundedt">
			<a href="<?php bloginfo('url') ?>"><?php bloginfo('title') ?></a>	 
		</div>
		<?php include('menu.php'); ?>
		<div id="content">
			<h2 class="title"><img class="textmid" src="style/design.png" alt="" />Choose Your Theme</h2>
			<div class="settings">
				<form action="<?php bloginfo('url') ?>Sources/ProcessAJAX.php" method="post" id="themeform">
					<p><label for="changetheme">Theme:</label>
					<select name="changetheme" id="changetheme" />
						<?php $dir = dirlist(ABSPATH .'/themes'); foreach($dir as $k => $v): ?>
							<option value="<?php echo $v ?>"><?php echo $v ?></option>
						<?php endforeach; ?>
					</select>
					<input type="submit" name="themesubmit" id="themesubmit" value="Change Theme" /></p>
				</form>
			</div>
		</div>
		<div id="footer" class="roundedb">		
			Powered by LightBlog <?php LightyVersion() ?>    
	    </div>
	</div>
</body>
</html>