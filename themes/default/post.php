<?php include('head.php')?>
<?php include('sidebar.php')?>
			<div id="content">
				<?php $posts = new PostLoop(); $posts->obtain_post($pid); if($posts->has_posts()): while($posts->loop()): ?>
				<div class="postbox">
					<h4 class="postnamealt">
						<?php $posts->title() ?>
					</h4>
					<p class="post"><?php $posts->content() ?></p>
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
				<?php $com = new CommentLoop(); if($com->comments_open()): ?>
				<h4 class="commenthead"><?php grammarFix(commentNum(null), 'Comment', 'Comments') ?></h4><br />				
				<!-- Start comment loop -->
				<?php $com->obtain_comments(); while($com->has_comments()): ?>
					<?php $com->list_comments() ?>
				<?php endwhile; ?>
				<!-- End comment loop -->
				
				<h4 class="commentform-title">Post a comment</h4><br />
				<?php $com->messageHook('<div id="notifybox">') ?>
				<form action="<?php bloginfo('url') ?>Sources/ProcessBrowser.php" method="post" id="commentform">
					<p><input name="comment_name" type="text" id="cfname" maxlength="35" />
					<label for="cfname"><small>Name (required)</small></label></p>
					<p><input name="comment_email" type="text" id="cfemail" maxlength="255" />
					<label for="cfemail"><small>Email (required)</small></label></p>
					<p><input name="comment_website" type="text" id="cfwebsite" maxlength="255" />
					<label for="cfwebsite"><small>Website</small></label></p>
					<p><textarea cols="41" rows="10" name="comment_text" id="wysiwyg"></textarea></p>
					<p><input name="comment_pid" type="hidden" value="<?php echo $GLOBALS['pid']; ?>" /></p>
					<p><input name="comment_submit" type="submit" value="Submit" id="cfsubmit" /></p>
				</form>
				<?php else: ?>
				<!-- If comments are disabled, this message is shown -->
				<p>Comments have been disabled on this post.</p>
				<!-- End message -->
				<?php endif; ?>
				<div class="clear"></div>
			</div>
<?php include('footer.php')?>
