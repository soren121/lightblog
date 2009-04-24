			<div id="content">
				<!-- Start the loop -->
				<?php if($result03->numRows() > 0): while($post = $result03->fetchObject()): $comments = $dbh->query("SELECT * FROM comments WHERE post_id=".(int)$post->id."") or die(sqlite_error_string($dbh->lastError)); ?>
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
						<span class="postdata">
							<img src="<?php bloginfo('url') ?>themes/<?php bloginfo('theme') ?>/style/comment.png" alt="" />
							<?php if($comments->numRows() == 1):
								  echo $comments->numRows(); ?> Comment
							<?php else:
								  echo $comments->numRows(); ?> Comments
							<?php endif; ?>							
						</span>
					</div>
				</div>
				<!-- End the loop -->			
				<?php endwhile; endif; ?>
				
				<!-- Start comment loop -->
				<?php if($result04->numRows() > 0): while($com = $result04->fetchObject()): ?>
				<div class="comment">
					<img class="comment_gravatar" src="<?php fetchGravatar($com->email,30) ?>" alt="" />
					<?php if($com->website !== null) : ?>
					<a class="comment_name" href="<?php echo $com->website ?>"><?php echo $com->name ?></a>
					<?php else: ?>
					<span class="comment_name"><?php echo $com->name ?></span>
					<?php endif; ?>
					<span class="comment_says"> says</span><br />
					<span class="comment_date"><?php echo date('F j, Y at g:i A', $com->date) ?></span><br />
					<p class="comment_text"><?php echo unescapeString($com->text) ?></p>
				</div>
				<!-- End commend loop -->
				<?php endwhile; else: ?>
				<span class="nocomments">No comments exist for this post.</span>
				<?php endif; ?>
				
				<script type="text/javascript">
				$(function() {
					$('#commentform').submit(function() {
						var inputs = [];
						$(':input', this).each(function() {
							inputs.push(this.name + '=' + escape(this.value));
						})
						jQuery.ajax({
							data: inputs.join('&'),
							type: "POST",
							url: this.getAttribute('action'),
							timeout: 2000,
							error: function() {
								$('#notifybox').text('Failed to submit <?php echo ucwords($type) ?>.').css("background","#b20000").slideDown("normal");
								console.log("Failed to submit");
								alert("Failed to submit.");
							},
							success: function(r) {
								$('#notifybox').html('<?php echo ucwords($type) ?> created. | <' + 'a href="' + r + '">View <?php echo $type ?></' + 'a>').slideDown("normal");
							}
						})
						return false;
					})
				});
				</script>	
				<h4 class="commentform-title">Post a comment</h4><br />
				<form action="<?php bloginfo('url') ?>Sources/ProcessAJAX.php" method="post" id="commentform">
					<table>
						<tr><td>Name:</td><td><input name="name" type="text" /></td></tr>
						<tr><td>Email:</td><td><input name="email" type="text"/></td></tr>
						<tr><td>Website:</td><td><input name="website" type="text"/></td></tr>
						<tr><td>Post:</td><td><textarea cols="41" rows="10" name="text"></textarea></td></tr>
						<tr><td colspan="2"><input name="comment_submit" type="submit" value="Submit"/></td></tr>
						<tr><td colspan="2"><input name="post_id" type="hidden" value="<?php echo (int)$_GET['id'] ?>"/></td></tr>
					</table>
				</form>
				<div class="clear"></div>
			</div>