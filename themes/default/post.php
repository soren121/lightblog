			<div id="content">
				<!-- Start the loop -->
				<?php if($result03->numRows() > 0): while($post = $result03->fetchObject()): $commentnum = (int)$post->id; ?>
				<div class="postbox">
					<h4 class="postnamealt">
						<?php echo unescapeString($post->title); ?>
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
					</div>
				</div>
				<!-- End the loop -->			
				<?php endwhile; endif; ?>
				
				<h4 class="commenthead"><?php commentNum($commentnum) ?> Comments</h4><br />
				<!-- Start comment loop -->
				<?php if($result04->numRows() > 0): while($com = $result04->fetchObject()): ?>
				<div class="comment <?php alternateColor('c1','c2') ?>">
					<img class="comment_gravatar" src="<?php fetchGravatar($com->email) ?>" alt="" />
					<?php if($com->website !== null) : ?>
					<a class="comment_name" href="<?php echo $com->website ?>"><?php echo $com->name ?></a>
					<?php else: ?>
					<span class="comment_name"><?php echo $com->name ?></span>
					<?php endif; ?>
					<span class="comment_says"> says:</span><br />
					<span class="comment_date"><?php echo date('F j, Y \a\t g:i A', $com->date) ?></span><br />
					<p class="comment_text"><?php echo unescapeString($com->text) ?></p>
				</div>
				<!-- End comment loop -->
				<?php endwhile; endif; ?>
				
				<h4 class="commentform-title">Post a comment</h4><br />
				<div id="notifybox"></div>
				<form action="<?php bloginfo('url') ?>Sources/ProcessBrowser.php" method="post" id="commentform">
					<p><input name="comment_name" type="text" id="cname" maxlength="35" />
					<label for="cfname"><small>Name (required)</small></label></p>
					<p><input name="comment_email" type="text" id="cfemail" maxlength="255" />
					<label for="cfemail"><small>Email (required)</small></label></p>
					<p><input name="comment_website" type="text" id="cfwebsite" maxlength="255" />
					<label for="cfwebsite"><small>Website</small></label></p>
					<p><textarea cols="41" rows="10" name="comment_text" id="wysiwyg"></textarea></p>
					<p><input name="comment_submit" type="submit" value="Submit" id="cfsubmit" /></p>
					<p><input name="comment_pid" type="hidden" value="<?php echo (int)$_GET['id']; ?>" /></p>
				</form>
				<div class="clear"></div>
			</div>