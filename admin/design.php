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
	</script>
</head>

<body>
	<div id="wrapper">
		<div id="header" class="roundedt">
			<a href="<?php bloginfo('url') ?>"><?php bloginfo('title') ?></a>	 
		</div>
        <div id="navigation" class="jqueryslidemenu">
			<ul>
				<li><a href="dashboard.php">Dashboard</a></li>
				<li><a href="javascript:void(0)">Create</a>
					<ul>
						<li><a href="create.php?type=1">Post</a></li>
						<li><a href="create.php?type=2">Page</a></li>
					</ul>
				</li>
				<li><a href="javascript:void(0)">Manage</a>
					<ul>
						<li><a href="manage.php?type=1">Post</a></li>
						<li><a href="manage.php?type=2">Page</a></li>
					</ul>
				</li>
				<li><a href="design.php">Design</a></li>
				<li><a href="javascript:void(0)">Users</a>
					<ul>
						<li><a href="users.php">Manage Users</a></li>
						<li><a href="profile.php">Your Profile</a></li>
					</ul>
				</li>
				<?php if(userFetch('role', 'r') >= 1): ?>
				<li><a href="settings.php">Settings</a></li>
				<?php endif; ?>
				<li><a href="login.php?logout=yes">Logout</a></li>
			</ul>
		</div>
		<div id="content">
			<h2 class="title"><img class="textmid" src="style/design.png" alt="" />Choose Your Theme</h2>
			<div class="settings">
			Stuff!
			</div>
		</div>
		<div id="footer" class="roundedb">		
			Powered by LightBlog <?php LightyVersion() ?>    
	    </div>
	</div>
</body>
</html>