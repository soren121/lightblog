<?php /***************************************

	LightBlog 0.9
	SQLite blogging platform

	themes/default/page.php

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
				<!-- Start the loop -->
				<?php $pages = new PageLoop(); $pages->obtain_page(); while($pages->has_pages()): ?>
					<div class="postbox">
						<h4 class="postnamealt">
							<?php $pages->title() ?>
						</h4>
						<div class="post"><?php $pages->content() ?></div>
					</div>
					<!-- End the loop -->			
				<?php endwhile; ?>
				<div class="clear"></div>
			</div>
<?php include('footer.php')?>