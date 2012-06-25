<?php /***************************************

	LightBlog 0.9
	SQLite blogging platform

	themes/default/post.php

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
				<?php $posts = new PostLoop(); $posts->obtain_post(); if($posts->has_posts(true) > 0): while($posts->has_posts()): ?>
				<div class="postbox">
					<h4 class="postnamealt">
						<?php $posts->title() ?>
					</h4>
					<div class="post"><?php $posts->content() ?></div>
					<div class="postdata">
						<span class="postdata">
							<img src="<?php bloginfo('themeurl') ?>/style/date.png" alt="" />
							<?php $posts->date('F j, Y') ?>
						</span>
						<span class="postdata">
							<img src="<?php bloginfo('themeurl') ?>/style/user.png" alt="" />
							<?php $posts->author() ?>
						</span>
						<span class="postdata">
							<img src="<?php bloginfo('themeurl') ?>/style/category.png" alt="" />
							<?php $posts->category() ?>
						</span>
					</div>
				</div>
				<?php endwhile; else: ?>
				<p>Sorry, no posts are available for display.</p>
				<?php endif; ?>

				<!-- Check if comments are enabled -->
				<?php $comment = new CommentLoop(); if($comment->allowed()): ?>
				<h4 class="commenthead"><?php grammarFix($comment->count(), 'Comment', 'Comments') ?></h4><br />
				<!-- Start comment loop -->
				<?php while($comment->has_comments()): ?>
					<?php $comment->list_comments() ?>
				<?php endwhile; ?>
				<!-- End comment loop -->

				<h4 class="commentform-title">Post a comment</h4><br />
				<?php $comment->messageHook('<div id="notifybox">') ?>
				<form action="#commentform" method="post" id="commentform">
					<?php if(user()->is_logged()): ?>
					<p>You are commenting as <strong><?php echo user()->name(); ?></strong>.</p>
					<?php else: ?>
					<p><input name="commenter_name" type="text" id="cfname" maxlength="100" value="<?php $comment->commenter_name(); ?>" />
					<label for="cfname"><small>Name (required)</small></label></p>
					<p><input name="commenter_email" type="text" id="cfemail" maxlength="255" value="<?php $comment->commenter_email(); ?>" />
					<label for="cfemail"><small>Email (required)</small></label></p>
					<p><input name="commenter_website" type="text" id="cfwebsite" maxlength="255" value="<?php $comment->commenter_website(); ?>" />
					<label for="cfwebsite"><small>Website</small></label></p>
					<?php endif; ?>
					<p><textarea cols="41" rows="10" name="comment_text" id="wysiwyg"></textarea></p>
					<?php $comment->formHook(); if(user()->is_guest()): ?>
					<p style="float: left;"><label title="Remember your name, email address and website"><input name="remember_me" value="1" type="checkbox" style="width: 15px !important;" checked="checked" /> Remember me</label></p>
					<?php endif; ?>
					<p style="float: right;"><input name="comment_submit" type="submit" value="Submit" id="cfsubmit" /></p>
					<div style="clear: both;">
					</div>
				</form>
				<?php else: ?>
				<!-- If comments are disabled, this message is shown -->
				<p>Comments have been disabled on this post.</p>
				<!-- End message -->
				<?php endif; ?>
				<div class="clear"></div>
			</div>
<?php include('footer.php')?>
