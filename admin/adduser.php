<?php session_start();

/***********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	admin/adduser.php
	
	©2008-2012 The LightBlog Team. All 
	rights reserved. Released under the 
	GNU General Public License 3. For 
	all licensing information, please 
	see the LICENSE.txt document 
	included in this distribution.

***********************************************/

// Require config file
require('../config.php');
require(ABSPATH .'/Sources/Core.php');
require(ABSPATH .'/Sources/Admin.php');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Add User - <?php bloginfo('title') ?></title>
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('url') ?>admin/style/style.css" />
	<!--[if lte IE 7]><style type="text/css">html.jqueryslidemenu { height: 1%; }</style><![endif]-->
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.js"></script>
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.SlideMenu.js"></script>
	<script type="text/javascript">
		$(function() {
			$('form').submit(function() {
				var inputs = [];
				$(':input', this).each(function() {
					inputs.push(this.name + '=' + escape(this.value));
				})
				$('.inform').remove();
				$('input[type=submit]').attr('disabled','disabled').after('<' + 'img src="style/loading.gif" alt="" class="loader" style="margin-left:5px;" /\>');
				jQuery.ajax({
					data: inputs.join('&'),
					type: "POST",
					url: "<?php bloginfo('url') ?>Sources/ProcessAJAX.php",
					timeout: 3000,
					error: function() {
							$('.loader').remove();
							$('input[type=submit]').removeAttr('disabled').after('<span style="color:red;margin-left:5px;" class="inform">Failed to submit.<\/' + 'span>');
					},
					success: function(json) {
						var r = jQuery.parseJSON(json);
						if(r.result == 'success') {
							$('.loader').remove();
							$('input[type=submit]').removeAttr('disabled').after('<span style="color:green;margin-left:5px;" class="inform">' + r.response +'<\/' + 'span>');
						}
						else {
							$('.loader').remove();
							$('input[type=submit]').removeAttr('disabled').after('<span style="color:red;margin-left:5px;" class="inform">Failed to submit; ' + r.response +'<\/' + 'span>');						
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
			<h2 class="title"><img class="textmid" src="style/users.png" alt="" />Add User</h2>
			<div class="settings">
				<p style="margin-bottom:10px;">You can add extra users to your blog here.</p>
				
				<form action="" method="post" style="margin-bottom:5px;margin-left:25px;">
					<p class="label"><label for="username">Username</label></p>
					<p style="margin-top:-5px;">
						<input type="text" name="username" id="username" value="" />
					</p>
					
					<p class="label"><label for="password">Password</label></p>
					<p style="margin-top:-5px;">
						<input type="password" name="password" id="password" value="" />
					</p>
					
					<p class="label"><label for="vpassword">Verify Password</label></p>
					<p style="margin-top:-5px;">
						<input type="password" name="vpassword" id="vpassword" value="" />
					</p>
					
					<p class="label"><label for="email">Email</label></p>
					<p style="margin-top:-5px;">
						<input type="text" name="email" id="email" value="" />
					</p>
					
					<p class="label"><label for="displayname">Display Name</label></p>
					<p style="margin-top:-5px;">
						<input type="text" name="displayname" id="displayname" value="" />
					</p>
					
					<p class="label"><label for="role">Role</label></p>
					<p>
						<select name="role" id="role">
							<option value="1">Standard User</option>
							<option value="2">Moderator</option>
							<option value="3">Administrator</option>
						</select>
					</p>
					
					<p><input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>" /></p>
					
					<p><input type="submit" value="Add User" name="addusersubmit" /></p>
				</form>
			</div>
		</div>
		<div id="footer" class="roundedb">		
			Powered by LightBlog <?php LightyVersion() ?>    
	    </div>
	</div>
</body>
</html>
