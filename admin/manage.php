<?php session_start();

/*********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	admin/manage.php
	
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

if((int)$_GET['type'] == 1) { $type = 'post'; }
elseif((int)$_GET['type'] == 2) { $type = 'page'; }

$result = $dbh->query("SELECT * FROM ".$type."s ORDER BY id desc") or die(sqlite_error_string($dbh->lastError));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Manage <?php echo ucwords($type) ?>s - <?php bloginfo('title') ?></title>
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
			<!-- Check if parameters were set -->
			<?php if(!isset($type)): ?>
			<p>The type of content to manage was not specified. You must have taken a bad link. Please
			use the navigation bar above to choose the correct type.</p>
			<!-- They were, so continue -->
			<?php else: ?>
			<h2 class="title"><img class="textmid" src="style/manage.png" alt="" />Manage <?php echo ucwords($type) ?>s</h2>
			<!-- Check if any posts/pages exist -->
			<?php if($result->numRows() > 0): ?>
			<table class="managelist">
				<!-- Add table headings -->
				<tr class="managelist">
					<th class="managelist">ID</th>
					<th class="managelist">Title</th>
					<th class="managelist">Author</th>
					<th class="managelist">Date</th>
					<th class="managelist">Edit</th>
					<th class="managelist">Delete</th>
				</tr>
				<!-- Start row loop -->
				<?php while($post = $result->fetchObject()): ?>
				<tr class="managelist">
					<td class="managelist"><?php echo $post->id ?></td>
					<td class="managelist"><?php echo $post->title ?></td>
					<td class="managelist"><?php echo $post->author ?></td>
					<td class="managelist"><?php echo date('n/j/Y', $post->date) ?></td>
					<td class="managelist c"><a href="edit.php?type=<?php echo (int)$_GET['type'] ?>&amp;id=<?php echo $post->id ?>"><img src="style/edit.png" alt="Edit" style="border:0;" /></a></td>
					<td class="managelist c"><img src="style/delete.png" alt="Delete" /></td>
				</tr>
				<?php endwhile; ?>
				<!-- End row loop -->
			</table>
			<!-- None exist error message -->
			<?php else: ?>
			<p>Sorry, no <?php echo $type ?>s exist to manage.</p>
			<!-- End both ifs -->
			<?php endif; endif; ?>
		</div>
		<div id="footer" class="roundedb">		
			Powered by LightBlog <?php LightyVersion() ?>    
	    </div>
	</div>
</body>
</html>