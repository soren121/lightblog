<?php
/***********************************************

	LightBlog 0.9
	SQLite blogging platform

	admin/appearance.php

	©2008-2012 The LightBlog Team. All
	rights reserved. Released under the
	GNU General Public License 3. For
	all licensing information, please
	see the LICENSE.txt document
	included in this distribution.

***********************************************/

// Require config file
require('../Sources/Core.php');
require(ABSPATH .'/Sources/Admin.php');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Design - <?php bloginfo('title') ?></title>
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('url') ?>admin/style/style.css" />
	<!--[if lte IE 7]><style type="text/css">html.jqueryslidemenu { height: 1%; }</style><![endif]-->
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.js"></script>
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.SlideMenu.js"></script>
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.InputHint.js"></script>
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/nicEdit.js"></script>
	<script type="text/javascript">
		$(function() {
			$('#themeform').submit(function() {
				var inputs = [];
				$(':input', this).each(function() {
					inputs.push(this.name + '=' + escape(this.value));
				})
				$('.inform').remove();
				$('#themesubmit').attr('disabled','disabled').after('<' + 'img src="style/loading.gif" alt="" class="loader" style="margin-left:5px;" />');
				jQuery.ajax({
					data: inputs.join('&'),
					type: "POST",
					url: this.getAttribute('action'),
					timeout: 2000,
					error: function() {
						$('.loader').remove();
						$('#themesubmit').removeAttr('disabled').after('<' + 'span style="color:red;margin-left:5px;" class="inform">Failed to submit.</' + 'span>');
					},
					success: function(json) {
						$('.loader').remove();
						var r = jQuery.parseJSON(json);
						if(r.result == 'success') {
							$('#themesubmit').removeAttr('disabled').after('<' + 'span style="color:green;margin-left:5px;" class="inform">Theme changed.</' + 'span>');
						}
						else {
							$('#themesubmit').removeAttr('disabled').after('<' + 'span style="color:red;margin-left:5px;" class="inform">' + r.response + '</' + 'span>');
						}
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
			<?php if(permissions(3)): ?>
			<h2 class="title"><img class="textmid" src="style/design.png" alt="" />Choose Your Theme</h2>
			<div class="settings">
				<p>Themes are used to change the appearance of your blog. For more themes or information on how to
					create your own, visit the LightBlog website.</p>
				<form action="<?php bloginfo('url') ?>Sources/ProcessAJAX.php" method="post" id="themeform">
					<p class="label"><label for="changetheme">Available themes</label></p>
					<p><select name="changetheme" id="changetheme">
							<?php list_themes() ?>
					</select></p>
					<p><input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>" /></p>
					<p><input type="submit" name="themesubmit" id="themesubmit" value="Save" /></p>
				</form>
			</div>
			<?php endif; ?>
		</div>
		<div id="footer" class="roundedb">
			Powered by LightBlog <?php LightyVersion() ?>
	    </div>
	</div>
</body>
</html>
