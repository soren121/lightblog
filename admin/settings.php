<?php session_start();

/*********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	admin/settings.php
	
	�2009 soren121. All rights reserved.
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
	<title>Settings - <?php bloginfo('title') ?></title>
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('url') ?>admin/style/style.css" />
	<!--[if lte IE 7]><style type="text/css">html.jqueryslidemenu { height: 1%; }</style><![endif]-->
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.js"></script>
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.SlideMenu.js"></script>
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.Corners.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){ 
			$('.rounded').corner(); 
			$('.roundedt').corner("round top 10px"); 
			$('.roundedb').corner("round bottom 10px");
		});
		$(function() {
			$('#titleform').submit(function() {
				var inputs = [];
				$(':input', this).each(function() {
					inputs.push(this.name + '=' + escape(this.value));
				})
				$('.inform').remove();
				$('#titlesubmit').attr('disabled','disabled').after('<' + 'img src="style/loadingsmall.gif" alt="" class="loader" style="margin-left:5px;" />');
				jQuery.ajax({
					data: inputs.join('&'),
					type: "POST",
					url: "<?php bloginfo('url') ?>Sources/ProcessAJAX.php",
					timeout: 2000,
					error: function() {
						console.log("Failed to submit");
						alert("Failed to submit.");
					},
					success: function(r) {
						$('.loader').remove();
						$('#titlesubmit').removeAttr('disabled').after('<' + 'span style="color:green;margin-left:5px;" class="inform">Title changed.<\/' + 'span>');
					}
				})
				return false;
			})
		});
		$(function() {
			$('#urlform').submit(function() {
				var inputs = [];
				$(':input', this).each(function() {
					inputs.push(this.name + '=' + escape(this.value));
				})
				$('.inform').remove();
				$('#urlsubmit').attr('disabled','disabled').after('<' + 'img src="style/loadingsmall.gif" alt="" class="loader" style="margin-left:5px;" />');
				jQuery.ajax({
					data: inputs.join('&'),
					type: "POST",
					url: "<?php bloginfo('url') ?>Sources/ProcessAJAX.php",
					timeout: 2000,
					error: function() {
						console.log("Failed to submit");
						alert("Failed to submit.");
					},
					success: function(r) {
						$('.loader').remove();
						$('#urlsubmit').removeAttr('disabled').after('<' + 'span style="color:green;margin-left:5px;" class="inform">URL changed.<\/' + 'span>');
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
			<h2 class="title"><img class="textmid" src="style/settings.png" alt="" />Change Your Blog Settings</h2>
			<div class="settings">
				<p style="margin-bottom:10px;">You can find core settings like the title of your blog here.</p>
				
				<form action="" method="post" id="titleform" style="margin-bottom:5px;">
					<p class="label"><label for="title">Blog title</label></p>
					<p style="margin-top:-5px;">
						<input type="text" name="title" id="title" value="<?php bloginfo('title') ?>" />
						<input type="submit" name="titlesubmit" id="titlesubmit" value="Save" />
					</p>
				</form>
				
				<form action="" method="post" id="urlform" style="margin-bottom:5px;">
					<p class="label"><label for="url">Blog URL</label></p>
					<p style="margin-top:-5px;">
						<input type="text" name="url" id="url" value="<?php bloginfo('url') ?>" />
						<input type="submit" name="urlsubmit" id="urlsubmit" value="Save" />
					</p>
				</form>
			</div>
		</div>
		<div id="footer" class="roundedb">		
			Powered by LightBlog <?php LightyVersion() ?>    
	    </div>
	</div>
</body>
</html>
