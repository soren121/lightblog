<?php session_start();

/*********************************************

	LightBlog 0.9
	SQLite blogging platform

	admin/edit.php

	©2008-2012 The LightBlog Team. All
	rights reserved. Released under the
	GNU General Public License 3. For
	all licensing information, please
	see the LICENSE.txt document
	included in this distribution.

*********************************************/

// Require config file
require('../config.php');
require(ABSPATH .'/Sources/Core.php');
require(ABSPATH .'/Sources/Admin.php');

if((int)$_GET['type'] == 1) { $type = 'post'; }
elseif((int)$_GET['type'] == 2) { $type = 'page'; }
elseif((int)$_GET['type'] == 3) { $type = 'category'; }
if(isset($_GET['id'])): $id = (int)$_GET['id']; endif;

$title = "Edit ".ucwords($type);
$selected = basename($_SERVER['REQUEST_URI']);

include('head.php');

// Query for past content
$result = $dbh->query("SELECT * FROM ".($type == 'category' ? 'categorie' : $type)."s WHERE id=".(int)$_GET['id']) or die(sqlite_error_string($dbh->lastError));

while($past = $result->fetchObject()) {
	if($type !== 'category') {
		$title = stripslashes($past->title);
		$author = $past->author;
		$s_category = (int)$past->category;
		if($past->published == 1) {
			$ps_checked = 'checked="checked"';
		}
		if($past->comments == 1) {
			$cs_checked = 'checked="checked"';
		}
	}
	else {
		$title = stripslashes($past->fullname);
		$text  = stripslashes($past->info);
	}
	if($type == 'post') {
		$text = stripslashes($past->post);
	}
	elseif($type == 'page') {
		$text = stripslashes($past->page);
	}
}

?>

		<div id="contentwrapper">
			<div id="contentcolumn">
				<?php if($type !== 'category' && permissions(1) || $type === 'category' && permissions(2)): if(!isset($type)): ?>
					<p>The type of content to add was not specified. You must have taken a bad link. Please
					use the navigation bar above to choose the correct type.</p>
				<?php else: ?>
					<form action="<?php bloginfo('url') ?>Sources/ProcessAJAX.php" method="post" id="edit">
						<div>
							<label class="tfl" for="title">Title</label><br />
							<input id="title" class="textfield ef" name="title" type="text" title="Title" value="<?php echo $title ?>" /><br />
							<textarea class="ef" rows="12" cols="36" name="text" id="wysiwyg"><?php echo $text ?></textarea><br />
							<input class="ef" type="hidden" name="type" value="<?php echo $type ?>" />
							<input class="ef" type="hidden" name="id" value="<?php echo $id ?>" />
							<input class="ef" type="hidden" name="csrf_token" value="<?php userinfo('csrf_token') ?>" />
						</div>
						<div class="settings">
							<?php if($type == 'post'): ?>
								<div style="float: left;margin-right: 30px;">
									<label for="category">Category:</label>
									<select class="ef" id="category" name="category">
										<?php list_categories('option', null, $s_category) ?>
									</select>
								</div>
								<div style="float: left;">
									<p>
										<label for="comments">Comments on?</label>
										<input class="ef" type="checkbox" name="comments" id="comments" <?php echo @$cs_checked ?> value="1" />
									</p>
							<?php elseif($type != 'category'): ?>
								<div style="float: left;">
							<?php endif; if($type != 'category'): ?>
									<p>
										<label for="published">Published?</label>
										<input class="ef" type="checkbox" name="published" id="published" <?php echo @$cs_checked ?> value="1" />
									</p>
								</div>
							<?php endif; ?>
							<input class="ef submit" name="edit" type="submit" value="Save" />
							<div class="clear"></div>
						</div>
					</form>
				<?php endif; endif; ?>
			</div>
		</div>

		<link rel="stylesheet" type="text/css" href="<?php bloginfo('url') ?>Sources/CLEditor/jQuery.CLEditor.css" />
		<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/CLEditor/jQuery.CLEditor.js"></script>
		<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/CLEditor/jQuery.CLEditor.XHTML.js"></script>
		<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/CLEditor/jQuery.CLEditor.AdvancedTable.js"></script>
		<script type="text/javascript">	
			$('#wysiwyg').cleditor({
				width: '100%',
				height: '320px',
				bodyStyle: 'margin:10px; font:11pt Georgia,Times,serif; cursor:text'
			});

			$(function() {
				$('#create').submit(function() {
					$('#ajaxresponse').html('<img src="<?php bloginfo('url') ?>admin/style/new/loading.gif" alt="Saving" />');
					var inputs = [];
					$('.ef', this).each(function() {
						if($(this).is(':checkbox') && $(this).is(':not(:checked)')) {
							void(0);
						}
						else {
							inputs.push(this.name + '=' + this.value);
						}
					});

					jQuery.ajax({
						data: inputs.join('&'),
						type: "POST",
						url: $(this).attr('action'),
						timeout: 2000,
						error: function() {
							$('#ajaxresponse').html('Failed to submit <?php echo $type; ?>.');
						},
						dataType: 'json',
						success: function(r) {
							if(r.result == 'success') {
								if(r.showlink == true) {
									$('#ajaxresponse').html('<' + 'a class="view" href="' + r.response + '">View <?php echo $type ?> &raquo;</' + 'a>');
								}
								else {
									$('#ajaxresponse').html('<span class="result"><?php echo ucwords($type) ?> saved.</span>');
								}
								$('#title').val('');
								$('#wysiwyg').cleditor()[0].clear();
							}
							else {
								$('#ajaxresponse').text('<span class="result">Failed to submit <?php echo $type; ?>;<br />' + r.response + '</span>').css("color","#E36868");
							}
						}
					})
					return false;
				})
			});
		</script>

<?php include('footer.php') ?>