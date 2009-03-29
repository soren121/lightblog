<?php session_start();

/*********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	admin/dashboard.php
	
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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title><?php echo bloginfo('title') ?></title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="generator" content="Geany 0.13/soren121" />
	<link rel="stylesheet" href="style/style.css" type="text/css" media="screen" />
	<!--[if IE]>
	<link rel="stylesheet" href="style/iefix.css" type="text/css" media="screen" />
	<![endif]-->
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
	<h2>Welcome to the dashboard!</h2>
	<h4 class="adqh4">What would you like to do?</h4>
	<ul>
	 <?php if($_SESSION['role'] >= 1): ?>	 	
	<li><a href="create.php?type=post">Create a post</a></li>
	<li><a href="create.php?type=page">Create a page</a></li>
	<li><a href="manage.php?type=post">Manage posts</a></li>
	<li><a href="manage.php?type=page">Manage pages</a></li>
	<?php endif; ?>
	<li><a href="profile.php">Check your profile</a></li>
	</ul>
	</div>
</div>
</body>
</html>
