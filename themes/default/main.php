<?php include('head.php')?>
<?php include('sidebar.php')?>
			<div id="content">
				<!-- Start the loop -->
				<?php $posts = new PostLoop(); $posts->obtain_posts($page, 8); while($posts->has_posts()): ?>
				<div class="postbox">
					<h4 class="postname">
						<a class="postname" href="<?php $posts->permalink() ?>"><?php $posts->title() ?></a>
					</h4>
					<p class="post"><?php $posts->post() ?></p>
					<div class="postdata">
						<span class="postdata">
							<img src="<?php bloginfo('themeurl') ?>/style/date.png" alt="" />
							<?php echo $posts->date() ?>
						</span>
						<span class="postdata">
							<img src="<?php bloginfo('themeurl') ?>/style/user.png" alt="" />
							<?php echo $posts->author() ?>
						</span>
						<span class="postdata">
							<img src="<?php bloginfo('themeurl') ?>/style/comment.png" alt="" />
							<a href="<?php $posts->permalink() ?>"><?php $posts->commentNum() ?> Comments</a>				
						</span>
					</div>
				</div>
				<!-- End the loop -->
				<?php endwhile; ?>
				<div class="pagination">
					<?php simplePagination('post', $file, $page); ?>
				</div>
		      
			   <div class="clear"></div>
			</div>
<?php include('footer.php')?>