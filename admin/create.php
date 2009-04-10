<?php session_start();

/*********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	admin/create.php
	
	�2009 soren121. All rights reserved.
	Released under the GNU General
	Public License. For all licensing
	information, please see the
	LICENSE.txt document included in this
	distribution.

*********************************************/

// Require config file
require('../config.php');
require(ABSPATH .'/Sources/Core.php');

if((int)$_GET['type'] == 1) {
	$type = 'post';
}
elseif((int)$_GET['type'] == 2) {
	$type = 'page';
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Create <?php echo ucwords($type) ?> - <?php bloginfo('title') ?></title>
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
			$('#create').submit(function() {
				var inputs = [];
				$(':input', this).each(function() {
					inputs.push(this.name + '=' + escape(this.value));
				})
				$('#create').empty().html('<' + 'img src="<?php bloginfo('url') ?>admin/style/loading.gif" alt="" />');
				jQuery.ajax({
					data: inputs.join('&'),
					type: "POST",
					url: this.getAttribute('action'),
					timeout: 2000,
					error: function() {
						$('#create').empty(); 
						console.log("Failed to submit");
						alert("Failed to submit.");
					},
					success: function(r) {
						$('#create').empty().append('Post/page created.');
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
			<?php bloginfo('title') ?>	 
		</div>
        <div id="navigation" class="jqueryslidemenu">
			<ul>
				<li><a href="#">Dashboard</a></li>
				<li><a href="#">Create</a>
					<ul>
						<li><a href="create.php?type=1">Post</a></li>
						<li><a href="create.php?type=2">Page</a></li>
					</ul>
				</li>
				<li><a href="#">Manage</a>
					<ul>
						<li><a href="manage.php?type=1">Post</a></li>
						<li><a href="manage.php?type=2">Page</a></li>
					</ul>
				</li>
				<li><a href="design.php">Design</a></li>
				<li><a href="profile.php">Users</a>
					<ul>
						<li><a href="users.php">Manage Users</a></li>
						<li><a href="profile.php">Your Profile</a></li>
					</ul>
				</li>
				<?php if(userFetch('role', 'r') >= 1): ?>
				<li><a href="settings.php">Settings</a></li>
				<?php endif; ?>
				<li><a href="login.php?logout=yes">Logout</a></li>
			</ul>
		</div>
		<div id="content">
			<?php if(!isset($type)): ?>
			<p>The type of content to add was not specified. You must have taken a bad link. Please
			use the navigation bar above to choose the correct type.</p>
			<?php else: ?>
			<h2 class="title">
				<img class="textmid" src="style/create.png" alt="" />Add New <?php echo ucwords($type) ?>
			</h2> 
			<form action="<?php bloginfo('url') ?>Sources/ProcessAJAX.php" method="post" id="create">
				<p><input class="hint textfield" name="title" type="text" title="Title" /></p>
				<textarea name="text" id="wysiwyg"></textarea>
				<p><input type="hidden" name="type" value="<?php echo $type ?>" /></p>
				<p><input name="publish" type="submit" value="Publish"/></p>
			</form>
			<?php endif; ?>
		</div>
		<div id="footer" class="roundedb">		
			Powered by LightBlog <?php LightyVersion() ?>    
	    </div>
	</div>
</body>
</html>
