<?php
/***********************************************

	LightBlog 0.9
	SQLite blogging platform

	admin/settings.php

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

if(get_bloginfo('comment_moderation') == 'none') {
	$cmno = 'checked="checked"';
}
elseif(get_bloginfo('comment_moderation') == 'approval') {
	$cmapvl = 'checked="checked"';
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Settings - <?php bloginfo('title') ?></title>
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('url') ?>admin/style/style.css" />
	<!--[if lte IE 7]><style type="text/css">html.jqueryslidemenu { height: 1%; }</style><![endif]-->
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.js"></script>
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.SlideMenu.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			$('.rounded').corner();
			$('.roundedt').corner("round top 10px");
			$('.roundedb').corner("round bottom 10px");
		});
		$(function() {
			$('form').submit(function() {
				var inputs = [];
				$(':input', this).each(function() {
					if($(this).is('input:radio') && $(this).is(':not(:checked)')) {
						void(0);
					}
					else {
						inputs.push(this.name + '=' + escape(this.value));
					}
				})
				$('.inform').remove();
				$('input[type=submit]').attr('disabled','disabled').after('<' + 'img src="style/loading.gif" alt="" class="loader" style="margin-left:5px;" />');
				jQuery.ajax({
					data: inputs.join('&'),
					type: "POST",
					url: "<?php bloginfo('url') ?>Sources/ProcessAJAX.php",
					timeout: 2000,
					error: function() {
							$('.loader').remove();
							$('input[type=submit]').removeAttr('disabled').after('<' + 'span style="color:red;margin-left:5px;" class="inform">Failed to submit.<\/' + 'span>');
					},
					success: function(json) {
						var r = jQuery.parseJSON(json);
						if(r.result == 'success') {
							$('.loader').remove();
							$('input[type=submit]').removeAttr('disabled').after('<' + 'span style="color:green;margin-left:5px;" class="inform">Changes saved.<\/' + 'span>');
						}
						else {
							$('.loader').remove();
							$('input[type=submit]').removeAttr('disabled').after('<' + 'span style="color:red;margin-left:5px;" class="inform">' + r.response + '<\/' + 'span>');
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
			<h2 class="title"><img class="textmid" src="style/settings.png" alt="" />Settings</h2>
			<div class="settings">
				<p style="margin-bottom:10px;">You can find all available settings on your blog here.</p>

				<form action="" method="post" style="margin-bottom:5px;">
					<p class="label"><label for="title">Blog title</label></p>
					<p style="margin-top:-5px;margin-bottom:5px;">
						<input type="text" name="changetitle" id="title" value="<?php bloginfo('title') ?>" />
					</p>

					<p class="label"><label for="url">Blog URL</label></p>
					<p style="margin-top:-5px;margin-bottom:5px;">
						<input type="text" name="changeurl" id="url" value="<?php bloginfo('url') ?>" />
					</p>

					<p class="label"><label>Comment moderation</label></p>
					<p>
						<input type="radio" name="commentmoderation" id="cm-no" value="none" <?php echo @$cmno; ?> />None
					</p>
					<p>
						<input type="radio" name="commentmoderation" id="cm-apvl" value="approval" <?php echo @$cmapvl; ?> />Approval required
					</p>

					<p><input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>" /></p>

					<p><input type="submit" name="changesettings" value="Save" /></p>
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
