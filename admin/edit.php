<?php session_start();

/*********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	admin/edit.php
	
	©2009 soren121. All rights reserved.
	Released under the GNU General
	Public License. For all licensing
	information, please see the
	LICENSE.txt document included in this
	distribution.

*********************************************/

// Require config file
require('../config.php');
require(ABSPATH .'/Sources/Core.php');
require(ABSPATH .'/Sources/Admin.php');

if((int)$_GET['type'] == 1) { $type = 'post'; }
elseif((int)$_GET['type'] == 2) { $type = 'page'; }

# Query for post
$result = $dbh->query("SELECT * FROM ".$type."s WHERE id=".(int)$_GET['id']) or die(sqlite_error_string($dbh->lastError));

# Get post data and set it
while($past = $result->fetchObject()) {
	$title = $past->title;
	if($type == 'post') { $text = $past->post; }
	elseif($type == 'page') { $text = $past->page; }
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Edit <?php echo ucwords($type) ?> - <?php bloginfo('title') ?></title>
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('url') ?>admin/style/style.css" />
	<!--[if lte IE 7]><style type="text/css">html.jqueryslidemenu { height: 1%; }</style><![endif]-->
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.js"></script>
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.SlideMenu.js"></script>
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.Corners.js"></script>
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.InputHint.js"></script>
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/nicEdit.js"></script> 
	<script type="text/javascript">	
		$(document).ready(function(){
			$('.rounded').corner(); 
			$('.roundedt').corner("round top 10px"); 
			$('.roundedb').corner("round bottom 10px");
			$('.hint').hint();
			new nicEditor({iconsPath:'<?php bloginfo('url') ?>Sources/nicEditorIcons.gif',xhtml:true}).panelInstance('wysiwyg');
		});
		$(function() {
			$('#edit').submit(function() {
				var inputs = [];
				var wysiwygtext = nicEditors.findEditor('wysiwyg').getContent();
				$('.ef', this).each(function() {
					inputs.push(this.name + '=' + escape(this.value));
				})
				$('#wysiwyg', this).each(function() {
					inputs.push(this.name + '=' + unescape(wysiwygtext));
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
						var out = r.replace(/<.*?>/g, '');
						if(out.match("Powered by 110MB Hosting")) {
							var out2 = out.replace("Powered by 110MB Hosting", '');
						}
						$('#notifybox').html('<?php echo ucwords($type) ?> edited. | <' + 'a href="' + out2 + '">View <?php echo $type ?></' + 'a>').css("background", "#CFEBF7").slideDown("normal");
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
			<?php if(!isset($type)): ?>
			<p>The type of content to add was not specified. You must have taken a bad link. Please
			use the navigation bar above to choose the correct type.</p>
			<?php else: ?>
			<h2 class="title"><img class="textmid" src="style/manage.png" alt="" />Edit <?php echo ucwords($type) ?></h2>
			<div id="notifybox"></div>
			<form action="<?php bloginfo('url') ?>Sources/ProcessAJAX.php" method="post" id="edit">
				<p><input class="hint textfield ef" name="title" type="text" title="Title" value="<?php echo unescapeString($title) ?>" /></p>
				<p><textarea rows="12" cols="36" name="text" id="wysiwyg"><?php echo unescapeString($text) ?></textarea></p>
				<p><input class="ef" type="hidden" name="type" value="<?php echo $type ?>" /></p>
				<p><input class="ef" type="hidden" name="id" value="<?php echo (int)$_GET['id'] ?>" /></p>
				<p><input class="ef submit" name="edit" type="submit" value="Save" /></p>
			</form>
			<?php endif; ?>
		</div>
		<div id="footer" class="roundedb">		
			Powered by LightBlog <?php LightyVersion() ?>    
	    </div>
	</div>
</body>
</html>
