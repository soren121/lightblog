<?php include('head.php')?>
<?php include('sidebar.php')?>
			<div id="content">
				<!-- Start the loop -->
				<?php $pages = new PageLoop(); $pages->obtain_pages(0, 1); while($pages->has_pages()): ?>
				<div class="postbox">
					<h4 class="postnamealt">
						<?php $pages->title() ?>
					</h4>
					<p class="post"><?php $pages->page() ?></p>
				</div>
				<!-- End the loop -->			
				<?php endwhile; ?>
				<div class="clear"></div>
			</div>
<?php include('footer.php')?>