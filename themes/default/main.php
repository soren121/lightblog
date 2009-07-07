<?php include('head.php')?>
<?php include('sidebar.php')?>
			<div id="content">
				<!-- Start the loop -->
				<?php $posts = new PostLoop(); $posts->obtain_posts(0, 8); while($posts->has_posts()): ?>
				<div class="postbox">
					<h4 class="postname">
						<a class="postname" href="<?php echo $posts->permalink() ?>"><?php echo $posts->title() ?></a>
					</h4>
					<p class="post"><?php echo $posts->post() ?></p>
					<div class="postdata">
						<span class="postdata">
							<img src="<?php bloginfo('url') ?>themes/<?php bloginfo('theme') ?>/style/date.png" alt="" />
							<?php echo $posts->date() ?>
						</span>
						<span class="postdata">
							<img src="<?php bloginfo('url') ?>themes/<?php bloginfo('theme') ?>/style/user.png" alt="" />
							<?php echo $posts->author() ?>
						</span>
						<span class="postdata">
							<img src="<?php bloginfo('url') ?>themes/<?php bloginfo('theme') ?>/style/comment.png" alt="" />
							<a href="<?php echo $posts->permalink() ?>">					
						</span>
					</div>
				</div>
				<!-- End the loop -->
				<?php endwhile; ?>
				<div class="pagination">
					<?php simplePagination('post', "index.php", (int)$_GET['page']); ?>
				</div>
		      
			   <div class="clear"></div>
			</div>
<?php include('footer.php')?>