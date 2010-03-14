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
					</div>
				</div>			
				<?php endwhile; else: ?>
				<p>Sorry, no posts are available for display.</p>
				<?php endif; ?>
				
				<h4 class="commenthead"><?php grammarFix(commentNum($pid), 'Comment', 'Comments') ?></h4><br />
				<!-- Start comment loop -->
				<?php $com = new CommentLoop(); $com->obtain_comments($pid); while($com->has_comments()): ?>
				<div class="comment <?php alternateColor('c1','c2') ?>" id="comment-<?php $com->id() ?>">
					<img class="comment_gravatar" src="<?php $com->gravatar() ?>" alt="" />
					<a class="comment_name" href="<?php $com->website() ?>"><?php $com->name() ?></a>
					<span class="comment_says"> says:</span><br />
					<span class="comment_date"><?php $com->date('F j, Y \a\t g:i A') ?></span><br />
					<p class="comment_text"><?php $com->comment() ?></p>
				</div>
				<!-- End comment loop -->
				<?php endwhile; ?>
				
				<h4 class="commentform-title">Post a comment</h4><br />
				<div id="notifybox"></div>
				<form action="<?php bloginfo('url') ?>Sources/ProcessBrowser.php" method="post" id="commentform">
					<p><input name="comment_name" type="text" id="cfname" maxlength="35" />
					<label for="cfname"><small>Name (required)</small></label></p>
					<p><input name="comment_email" type="text" id="cfemail" maxlength="255" />
					<label for="cfemail"><small>Email (required)</small></label></p>
					<p><input name="comment_website" type="text" id="cfwebsite" maxlength="255" />
					<label for="cfwebsite"><small>Website</small></label></p>
					<p><textarea cols="41" rows="10" name="comment_text" id="wysiwyg"></textarea></p>
					<p><input name="comment_submit" type="submit" value="Submit" id="cfsubmit" /></p>
					<p><input name="comment_pid" type="hidden" value="<?php echo $pid; ?>" /></p>
				</form>
				<div class="clear"></div>
			</div>
<?php include('footer.php')?>
