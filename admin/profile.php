<?php session_start();

/*********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	admin/profile.php
	
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
require(ABSPATH .'/Sources/Admin.php');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>User Profile - <?php bloginfo('title') ?></title>
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
				<p style="margin-bottom:10px;">You can edit your profile here.</p>

				<form action="" method="post" style="margin-bottom:5px;margin-left:25px;">
					<p class="label"><label for="password">Password</label></p>
					<p style="margin-top:-5px;">
						<input type="password" name="password" id="password" value="" />
					</p>

					<p class="label"><label for="email">Email</label></p>
					<p style="margin-top:-5px;">
						<input type="text" name="email" id="email" value="<?php userFetch('email') ?>" />
					</p>

					<p class="label"><label for="displayname">Display Name</label></p>
					<p style="margin-top:-5px;">
						<input type="text" name="displayname" id="displayname" value="<?php userFetch('displayname') ?>" />
					</p>
					
					<p class="label" style="margin-top:20px;">For security reasons, please type your current password in here.</p>
					<p style="margin-top:0px;">
						<input type="text" name="vpassword" id="vpassword" value="" />
					</p>

					<p><input type="submit" value="Save Changes" name="editprofilesubmit" /></p>
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
