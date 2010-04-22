			<div id="sidebar">
				<div>
					<h4>Pages</h4>
					<ul>
						<?php list_pages() ?>
					</ul>
				</div>
				
				<div>
					<h4>Categories</h4>
					<ul>
						<?php list_categories() ?>
					</ul>
				</div>
				
				<div>
					<h4>Archives</h4>
					<ul>
						<?php list_archives() ?>
					</ul>
				</div>
				
				<div>
					<h4>Meta</h4>
					<ul>
						<li><a href="<?php bloginfo('url') ?>admin/">Site Admin</a></li>
						<li><a href="http://lightblog.googlecode.com/" rel="nofollow">LightBlog Home</a></li>
					</ul>
				</div>
				
				<div>
					<h4>Feeds</h4>
					<ul>
						<li><img src="<?php bloginfo('themeurl') ?>/style/rss.png" alt="" class="feed" /><a href="<?php bloginfo('url') ?>feed.php">RSS Feed</a></li>
						<li><img src="<?php bloginfo('themeurl') ?>/style/atom.png" alt="" class="feed" /><a href="<?php bloginfo('url') ?>feed.php?type=atom">Atom Feed</a></li>
					</ul>
				</div>
				<div class="clear"></div>
			</div>   
