<?php include('head.php')?>
<?php include('sidebar.php')?>
			<div id="content">
				<!-- Start the loop -->
				<?php if($result05->numRows() > 0): while($page = $result05->fetchObject()): ?>
				<div class="postbox">
					<h4 class="postnamealt">
						<?php echo unescapeString($page->title); ?>
					</h4>
					<p class="post"><?php echo unescapeString($page->page); ?></p>
				</div>
				<!-- End the loop -->			
				<?php endwhile; endif; ?>
				<div class="clear"></div>
			</div>
<?php include('footer.php')?>