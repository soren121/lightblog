			<div id="content">
				<!-- Start the loop -->
				<?php if($result05->numRows() > 0): while($page = $result05->fetchObject()): ?>
				<div class="postbox">
					<h4 class="postnamealt">
						<?php echo unescapeString($page->title); ?>
					</h4>
					<p class="post"><?php echo unescapeString($page->page); ?></p>
					<div class="postdata">
						<span class="postdata">
							<img src="<?php bloginfo('url') ?>themes/<?php bloginfo('theme') ?>/style/date.png" alt="" />
							<?php echo date('F j, Y', $page->date); ?>
						</span>
						<span class="postdata">
							<img src="<?php bloginfo('url') ?>themes/<?php bloginfo('theme') ?>/style/user.png" alt="" />
							<?php echo $page->author; ?>
						</span>
					</div>
				</div>
				<!-- End the loop -->			
				<?php endwhile; endif; ?>