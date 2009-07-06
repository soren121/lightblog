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
require(ABSPATH .'/Sources/Admin.php');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Dashboard - <?php bloginfo('title') ?></title>
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
			<h2 class="title">Welcome <?php userFetch('displayname') ?>!</h2>
			<div>
				<div style="float:left;width:50%;">
					<div class="db_box rounded">
						<h4>Recent Posts</h4>
						<ul>
						<?php
							$rpresult = $dbh->query("SELECT * FROM posts ORDER BY id desc LIMIT 5");
							while($rp = $rpresult->fetch(SQLITE_ASSOC)): ?>
								<li><a href="<?php bloginfo('url')?>post.php?id=<?php echo $rp['id']; ?>"><?php echo $rp['title']?></a></li>
							<?php endwhile; ?>
						</ul>
					</div>
					<div class="db_box rounded">
						<h4>Recent Comments</h4>
						<ul>
						<?php
							$rcresult = $dbh->query("SELECT * FROM comments ORDER BY id desc LIMIT 5");
							while($rp = $rcresult->fetch(SQLITE_ASSOC)): $rcpresult = $dbh->query("SELECT title FROM posts WHERE id=".(int)$rp['id']) ?>
								<li><a href="<?php bloginfo('url')?>post.php?id=<?php echo $rp['pid']; ?>#comment-<?php echo $rp['id']; ?>"><?php echo $rp['name']?> on <?php echo $rcpresult->fetchSingle(); ?></a></li>
							<?php endwhile; ?>
						</ul>
					</div>
				</div>
				<div style="float:right;width:50%;">
					<div class="db_box rounded">
						<h4>Quick Links</h4>
						<ul>
							<li><a href="create.php?type=1">Create a post</a></li>
							<li><a href="create.php?type=2">Create a page</a></li>
							<li><a href="manage.php?type=1">Manage posts</a></li>
							<li><a href="design.php">Change your theme</a></li>
						</ul>
					</div>
					<div class="db_box rounded">
						<h4>LightBlog News</h4>
						<ul>
						<?php
							include(ABSPATH .'/Sources/FeedReader.php');
							$reader = new Reader('http://lightblognews.wordpress.com/feed/', ABSPATH .'/Sources/feedcache');
							$items = $reader->return_items(5);
							foreach($items as $news): ?>
								<li><a href="<?php echo $news['link'] ?>"><?php echo $news['title'] ?></a></li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<div id="footer" class="roundedb">		
			Powered by LightBlog <?php LightyVersion() ?>    
	    </div>
	</div>
</body>
</html>
