			<div id="content">
				<!-- Start the loop -->
				<?php if($result01->numRows() > 0): while($post = $result01->fetchObject()): ?>
				<div class="postbox">
					<h4 class="postname">
						<a class="postname" href="<?php bloginfo('url') ?>post.php?id=<?php echo (int)$post->id; ?>"><?php echo unescapeString($post->title); ?></a>
					</h4>
					<p class="post"><?php echo unescapeString($post->post); ?></p>
					<div class="postdata">
						<span class="postdata">
							<img src="<?php bloginfo('url') ?>themes/<?php bloginfo('theme') ?>/style/date.png" alt="" />
							<?php echo date('F j, Y', $post->date); ?>
						</span>
						<span class="postdata">
							<img src="<?php bloginfo('url') ?>themes/<?php bloginfo('theme') ?>/style/user.png" alt="" />
							<?php echo $post->author; ?>
						</span>
						<span class="postdata">
							<img src="<?php bloginfo('url') ?>themes/<?php bloginfo('theme') ?>/style/comment.png" alt="" />
							<a href="<?php bloginfo('url') ?>post.php?id=<?php echo (int)$post->id; ?>">
							<?php if(commentNum((int)$post->id, 1) == 1):
								  commentNum((int)$post->id) ?> Comment</a>
							<?php else:
								  commentNum((int)$post->id) ?> Comments</a>
							<?php endif; ?>							
						</span>
					</div>
				</div>
				<!-- End the loop -->
				<?php endwhile; endif; ?>
				<div class="pagination">
					<?php echo simplePagination('post', "index.php", (int)$_GET['page']); ?>
				</div>
		      
			   <div class="clear"></div>
			</div>