<?php session_start();

/*********************************************

	LightBlog 0.9
	SQLite blogging platform

	admin/profile.php

	©2008-2012 The LightBlog Team. All
	rights reserved. Released under the
	GNU General Public License 3. For
	all licensing information, please
	see the LICENSE.txt document
	included in this distribution.

*********************************************/

// Require config file
require('../config.php');
require(ABSPATH .'/Sources/Core.php');
require(ABSPATH .'/Sources/Admin.php');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>User Profile - <?php bloginfo('title') ?></title>
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('url') ?>admin/style/style.css" />
	<!--[if lte IE 7]><style type="text/css">html.jqueryslidemenu { height: 1%; }</style><![endif]-->
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.js"></script>
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.SlideMenu.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$('input:checkbox').click(function() {
	 			if($(this).is(':checked')) {
	   				$(this).next().removeAttr('disabled');
	 			} else {
			   		$(this).next().attr('disabled', 'disabled');
	 			}
   			});
		});
		$(function() {
			$('form').submit(function() {
				var inputs = [];
				$(':input', this).each(function() {
					if($(this).is(':checkbox') && $(this).is(':not(:checked)')) {
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
						$('#editprofilesubmit').removeAttr('disabled').after('<' + 'span style="color:red;margin-left:5px;" class="inform">Failed to submit.</' + 'span>');
					},
					success: function(json) {
						$('.loader').remove();
						var r = jQuery.parseJSON(json);
						if(r.result == 'success') {
							$('#editprofilesubmit').removeAttr('disabled').after('<' + 'span style="color:green;margin-left:5px;" class="inform">Success! Changes will appear on next login.</' + 'span>');
						}
						else {
							$('#editprofilesubmit').removeAttr('disabled').after('<' + 'span style="color:red;margin-left:5px;" class="inform">Fatal error; ' + r.response + '</' + 'span>');
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
			<?php if(permissions(1)): ?>
			<h2 class="title"><img class="textmid" src="style/users.png" alt="" />User Profile</h2>
			<div class="settings">
				<p style="margin-bottom:10px;">You can edit your profile here. For each item you want to edit, you need to check the checkbox.</p>

				<form action="" method="post" style="margin-bottom:5px;margin-left:25px;">
					<p class="label"><label for="password">Password</label></p>
					<p style="margin-top:-5px;">
						<input type="checkbox" name="pw-ck" value="1" />
						<input type="password" name="password" id="password" value="" disabled="disabled" />
					</p>

					<p class="label"><label for="email">Email</label></p>
					<p style="margin-top:-5px;">
						<input type="checkbox" name="em-ck" value="1" />
						<input type="text" name="email" id="email" value="<?php userinfo('email') ?>" disabled="disabled" />
					</p>

					<p class="label"><label for="displayname">Display Name</label></p>
					<p style="margin-top:-5px;">
						<input type="checkbox" name="dn-ck" value="1" />
						<input type="text" name="displayname" id="displayname" value="<?php userinfo('displayname') ?>" disabled="disabled" />
					</p>

					<p class="label" style="margin-top:20px;">For security reasons, please type your current password in here.</p>
					<p style="margin-top:0px;">
						<input type="password" name="vpassword" id="vpassword" value="" />
					</p>

					<p><input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>" /></p>

					<p><input type="submit" value="Save Changes" name="editprofilesubmit" id="editprofilesubmit" /></p>
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
