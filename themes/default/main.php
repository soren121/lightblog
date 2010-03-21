<?php include('head.php')?>
<?php include('sidebar.php')?>
			<div id="content">
				<?php $posts = new PostLoop(); $posts->obtain_posts($page, 8); if($posts->has_posts()): while($posts->loop()): ?>
				<div class="postbox">
					<h4 class="postname">
						<a class="postname" href="<?php $posts->permalink() ?>"><?php $posts->title() ?></a>
					</h4>
					<p class="post"><?php $posts->content('Read More &#187;') ?></p>
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
							<a href="<?php $posts->permalink() ?>#commentform"><?php echo $posts->commentNum() ?></a>				
						</span>
						<span class="postdata">
							<img src="<?php bloginfo('themeurl') ?>/style/category.png" alt="" />
							<?php $posts->category() ?>
						</span>
					</div>
				</div>
				<?php endwhile; else: ?>
				<p>Sorry, no posts matched your criteria.</p>
				<?php endif; ?>
				
				<div class="pagination">
					<?php simplePagination('post', $file, $page); ?>
				</div>
		      
			   <div class="clear"></div>
			</div>
<?php include('footer.php')?>
