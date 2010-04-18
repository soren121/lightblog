<?php session_start();

/*********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	admin/dashboard.php
	
	©2009-2010 The LightBlog Team. All 
	rights reserved. Released under the 
	GNU General Public License 3. For 
	all licensing information, please 
	see the LICENSE.txt document 
	included in this distribution.

*********************************************/

// Require config file
require('../config.php');
require(ABSPATH .'/Sources/Core.php');

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
	<!--[if lt IE 7]>  <div style='border: 1px solid #F7941D; background: #FEEFDA; text-align: center; clear: both; height: 75px; position: relative;'>    <div style='position: absolute; right: 3px; top: 3px; font-family: courier new; font-weight: bold;'><a href='#' onclick='javascript:this.parentNode.parentNode.style.display="none"; return false;'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-cornerx.jpg' style='border: none;' alt='Close this notice'/></a></div>    <div style='width: 640px; margin: 0 auto; text-align: left; padding: 0; overflow: hidden; color: black;'>      <div style='width: 75px; float: left;'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-warning.jpg' alt='Warning!'/></div>      <div style='width: 275px; float: left; font-family: Arial, sans-serif;'>        <div style='font-size: 14px; font-weight: bold; margin-top: 12px;'>You are using an outdated browser</div>        <div style='font-size: 12px; margin-top: 6px; line-height: 12px;'>For a better experience using this site, please upgrade to a modern web browser.</div>      </div>      <div style='width: 75px; float: left;'><a href='http://www.firefox.com' target='_blank'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-firefox.jpg' style='border: none;' alt='Get Firefox 3.5'/></a></div>      <div style='width: 75px; float: left;'><a href='http://www.browserforthebetter.com/download.html' target='_blank'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-ie8.jpg' style='border: none;' alt='Get Internet Explorer 8'/></a></div>      <div style='width: 73px; float: left;'><a href='http://www.apple.com/safari/download/' target='_blank'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-safari.jpg' style='border: none;' alt='Get Safari 4'/></a></div>      <div style='float: left;'><a href='http://www.google.com/chrome' target='_blank'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-chrome.jpg' style='border: none;' alt='Get Google Chrome'/></a></div>    </div>  </div>  <![endif]-->
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
							if($rpresult->numRows() == 0): ?>
								No posts to display.
							<?php else: while($rp = $rpresult->fetch(SQLITE_ASSOC)): ?>
								<li><a href="<?php bloginfo('url')?>?post=<?php echo $rp['id']; ?>"><?php echo $rp['title']?></a></li>
							<?php endwhile; endif; ?>
						</ul>
					</div>
					<div class="db_box rounded">
						<h4>Recent Comments</h4>
						<ul>
						<?php
							$rcresult = $dbh->query("SELECT * FROM comments ORDER BY id desc LIMIT 5");
							if($rcresult->numRows() == 0): ?>
								No comments to display.
							<?php else: while($rp = $rcresult->fetch(SQLITE_ASSOC)): $rcpresult = $dbh->query("SELECT title FROM posts WHERE id=".(int)$rp['pid']) ?>
								<li><a href="<?php bloginfo('url')?>?post=<?php echo $rp['pid']; ?>#comment-<?php echo $rp['id']; ?>"><?php echo $rp['name']?></a> on <a href="<?php bloginfo('url')?>?post=<?php echo $rp['pid']; ?>"><?php echo $rcpresult->fetchSingle(); ?></a></li>
							<?php endwhile; endif; ?>
						</ul>
					</div>
				</div>
				<div style="float:right;width:50%;">
					<div class="db_box rounded">
						<h4>Quick Links</h4>
						<ul>
							<li><a href="create.php?type=1">Create a post</a></li>
							<?php if(permissions(2)):  ?>
								<li><a href="create.php?type=2">Create a page</a></li>
								<li><a href="manage.php?type=1">Manage posts</a></li>
							<?php endif; ?>
							<li><a href="profile.php">Edit your profile</a></li>
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
