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

# Query for past content
$result = $dbh->query("SELECT * FROM ".($type == 'category' ? 'categorie' : $type)."s WHERE id=".(int)$_GET['id']) or die(sqlite_error_string($dbh->lastError));

# Get past data and set it
while($past = $result->fetchObject()) {
	if($type !== 'category') {
		$title = $past->title;
		$author = $past->author;
		$s_category = (int)$past->category;
		if($past->published == 1) {
			$ps_checked = 'checked="checked"';
		}
		if($past->comments == 1) {
			$cs_checked = 'checked="checked"';
		}
	}
	if($type == 'post') {
		$text = $past->post;
	}
	elseif($type == 'page') {
		$text = $past->page;
	}
	elseif($type == 'category') {
		$title = $past->fullname;
		$text  = $past->info;
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Edit <?php echo ucwords($type) ?> - <?php bloginfo('title') ?></title>
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('url') ?>admin/style/style.css" />
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('url') ?>Sources/CLEditor/jQuery.CLEditor.css" />
	<!--[if lte IE 7]><style type="text/css">html.jqueryslidemenu { height: 1%; }</style><![endif]-->
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.js"></script>
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.SlideMenu.js"></script>
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/CLEditor/jQuery.CLEditor.js"></script> 
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/CLEditor/jQuery.CLEditor.XHTML.js"></script> 
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/CLEditor/jQuery.CLEditor.AdvancedTable.js"></script>
	<script type="text/javascript">	
		$(document).ready(function(){
			$('#wysiwyg').cleditor({width: 465, height: 300});
		});
		$(function() {
			$('#edit').submit(function() {
				var inputs = [];
				$('.ef', this).each(function() {
					if($(this).is(':checkbox') && $(this).is(':not(:checked)')) {
						void(0);
					}
					else {
						inputs.push(this.name + '=' + escape(this.value));
					}
				})
				jQuery.ajax({
					data: inputs.join('&'),
					type: "POST",
					url: this.getAttribute('action'),
					timeout: 2000,
					error: function() {
						$('#notifybox').text('Failed to submit <?php echo $type; ?>.').css("background","#E36868").css("border-color","#a40000").slideDown("normal");
					},
					success: function(json) {
						var r = jQuery.parseJSON(json);
						if(r.result == 'success') {
							if(r.showlink == true) {
								$('#notifybox').html('<?php echo ucwords($type) ?> edited. | <' + 'a href="' + r.response + '">View <?php echo $type ?></' + 'a>').slideDown("normal");
							}
							else {
								$('#notifybox').html('<?php echo ucwords($type) ?> edited.').slideDown("normal");
							}
							$('#title').val('');
							$('#wysiwyg').cleditor()[0].clear();
						}
						else {
							$('#notifybox').text('Failed to submit <?php echo $type; ?>; ' + r.response).css("background","#E36868").css("border-color","#a40000").slideDown("normal");
						}
					}
				})
				return false;
			})
		});
	</script>
</head>

<body>
	<div id="wrapper">
		<div id="header" class="roundedt">
			<a href="<?php bloginfo('url') ?>"><?php bloginfo('title') ?></a>	 
		</div>
		<?php include('menu.php'); ?>
		<div id="content">
			<?php if($type !== 'category' &&  permissions(2) || $type !== 'category' &&  permissions(1) && $author === userFetch('displayname','r') || $type === 'category' && permissions(2)): if(!isset($type)): ?>
			<p>The type of content to add was not specified. You must have taken a bad link. Please
			use the navigation bar above to choose the correct type.</p>
			<?php else: ?>
			<h2 class="title"><img class="textmid" src="style/manage.png" alt="" />Edit <?php echo ucwords($type) ?></h2>
			<div id="notifybox"></div>
			<form action="<?php bloginfo('url') ?>Sources/ProcessAJAX.php" method="post" id="edit">
				<div style="float:left;width:480px;margin-top:3px;">
					<label class="tfl" for="title">Title</label>
					<input class="textfield ef" name="title" type="text" id="title" title="Title" value="<?php echo stripslashes($title) ?>" />
					<label class="tfl" for="wysiwyg"><?php echo $type == 'category' ? 'Info' : 'Body'; ?></label>
					<textarea class="ef" rows="12" cols="36" name="text" id="wysiwyg"><?php echo stripslashes($text) ?></textarea>
					<input class="ef" type="hidden" name="type" value="<?php echo $type ?>" />
					<input class="ef" type="hidden" name="id" value="<?php echo (int)$_GET['id'] ?>" />
					<input class="ef" type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>" />
				</div>
				<div class="settings" style="float:left;width:170px;margin:19px 0 10px;padding:15px;">
					<?php if($type == 'post'): ?>
						<label for="category">Category:</label><br />
						<select class="ef" id="category" name="category">
							<?php list_categories($s_category) ?>
						</select><br /><br />
						<label for="comments">Comments on?</label>
						<input class="ef" type="checkbox" name="comments" id="comments" <?php echo @$cs_checked; ?> value="1" /><br />
					<?php endif; if($type != 'category'): ?>
						<label for="published">Published?</label>
						<input class="ef" type="checkbox" name="published" id="published" <?php echo @$ps_checked; ?> value="1" /><br /><br />
					<?php endif; ?>
					<input class="ef submit" name="edit" type="submit" value="Save" />
				</div>
				<div style="clear:both;"></div>
			</form>
			<?php endif; endif; ?>
		</div>
		<div id="footer" class="roundedb">		
			Powered by LightBlog <?php LightyVersion() ?>    
	    </div>
	</div>
</body>
</html>
