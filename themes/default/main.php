<?php /***************************************

	LightBlog 0.9
	SQLite blogging platform

	themes/default/main.php

	©2008-2012 The LightBlog Team. All 
	rights reserved. Released under the 
	GNU General Public License 3. For 
	all licensing information, please 
	see the LICENSE.txt document 
	included in this distribution.

******************************************/ ?>

<?php include('head.php')?>
<?php include('sidebar.php')?>
			<div id="content">
				<?php $posts = new PostLoop(); $posts->obtain_posts($page, 8); if($posts->has_posts()): while($posts->loop()): ?>
				<div class="postbox">
					<h4 class="postname">
						<a class="postname" href="<?php $posts->permalink() ?>"><?php $posts->title() ?></a>
					</h4>
					<div class="post"><?php $posts->content('Read More &#187;') ?></div>
					<div class="postdata">
						<span class="postdata">
							<img src="<?php bloginfo('themeurl') ?>/style/date.png" alt="" />
							<?php $posts->date() ?>
						</span>
						<span class="postdata">
							<img src="<?php bloginfo('themeurl') ?>/style/user.png" alt="" />
							<?php $posts->author() ?>
						</span>
						<span class="postdata">
							<img src="<?php bloginfo('themeurl') ?>/style/comment.png" alt="" />
							<a href="<?php $posts->permalink() ?>#commentform"><?php grammarFix($posts->commentNum(), 'Comment', 'Comments') ?></a>				
						</span><br />
						<span class="postdata">
							<img src="<?php bloginfo('themeurl') ?>/style/category.png" alt="" />
							<?php $posts->category() ?>
						</span>
					</div>
				</div>
				<?php endwhile; $posts->pagination(); else: ?>
				<p>Sorry, no posts matched your criteria.</p>
				<?php endif; ?>
		      
			   <div class="clear"></div>
			</div>
<?php include('footer.php')?>
