<?php session_start();

/*********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	admin/manage.php
	
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

# Functions to find the start for a query based on the page number
function findStart($input) { $input = $input - 1; return $input * 8; }

if(isset($_GET['page']) && $_GET['page'] > 1) {
		$result = $dbh->query("SELECT * FROM ".$type."s ORDER BY id asc LIMIT ".findStart($_GET['page']).",8") or die(sqlite_error_string($dbh->lastError));
}
else {
	$result = $dbh->query("SELECT * FROM ".$type."s ORDER BY id asc LIMIT 0,8") or die(sqlite_error_string($dbh->lastError));
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Manage <?php echo ucwords($type) ?>s - <?php bloginfo('title') ?></title>
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('url') ?>admin/style/style.css" />
	<!--[if lte IE 7]><style type="text/css">html.jqueryslidemenu { height: 1%; }</style></script><![endif]-->
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.js"></script>
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.SlideMenu.js"></script>
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.Corners.js"></script>
	<script type="text/javascript">			
		$(document).ready(function(){ 
			$('.rounded').corner(); 
			$('.roundedt').corner("round top 10px"); 
			$('.roundedb').corner("round bottom 10px");
		});
		
		function deleteItem(id,title) {
			var answer = confirm("Really delete <?php echo $type ?> \"" + title + "\"?");
			if(answer) {
				jQuery.ajax({
					data: "delete=true&type=<?php echo $type ?>&id=" + id,
					type: "POST",
					url: "<?php bloginfo('url') ?>Sources/ProcessAJAX.php",
					timeout: 3000,
					error: function() {
						alert("Failed to delete <?php echo $type ?>.");
					},
					success: function(r) {
						var tr = '#tr' + id;
						$(tr).hide();
					}
				})
			}
		}
		function bulkChange() {
			action = $('#bulk_dropdown').val();
			checkednum = $("input[name='bulk[]']:checkbox:checked").length;
			if(checkednum > 0) {
				var answer = confirm("Really " + action + " these <?php echo $type; ?>s?");
				if(answer) {
				
				}
			}
		}
	</script>
</head>

<body>
	<div id="wrapper">
		<div id="header" class="roundedt">
			<a href="<?php bloginfo('url') ?>"><?php bloginfo('title') ?></a>	 
		</div>
		<?php include('menu.php'); ?>
		<div id="content">
			<!-- Check if parameters were set -->
			<?php if(permissions(2)): if(!isset($type)): ?>
			<p>The type of content to manage was not specified. You must have taken a bad link. Please
			use the navigation bar above to choose the correct type.</p>
			<!-- They were, so continue -->
			<?php else: ?>
			<h2 class="title"><img class="textmid" src="style/manage.png" alt="" />Manage <?php echo ucwords($type) ?>s</h2>
			<!-- Check if any posts/pages exist -->
			<?php if($result->numRows() > 0): ?>
			<table class="managelist">
				<!-- Add table headings -->
				<tr>
					<th class="managelist">ID</th>
					<th class="managelist">Title</th>
					<th class="managelist">Author</th>
					<th class="managelist">Date</th>
					<th class="managelist">Edit</th>
					<th class="managelist">Delete</th>
				</tr>		
				<!-- Start row loop -->
				<?php while($post = $result->fetchObject()): ?>	
				<tr id="tr<?php echo $post->id ?>">
					<td><?php echo $post->id ?></td>
					<td><?php echo $post->title ?></td>
					<td><?php echo $post->author ?></td>
					<td><?php echo date('n/j/Y', $post->date) ?></td>
					<td class="c"><a href="edit.php?type=<?php echo (int)$_GET['type'] ?>&amp;id=<?php echo $post->id ?>"><img src="style/edit.png" alt="Edit" style="border:0;" /></a></td>
					<td class="c"><img src="style/delete.png" alt="Delete" onclick="deleteItem(<?php echo $post->id.', \''.$post->title.'\'' ?>);" style="cursor:pointer;" /></td>
				</tr>
				<?php endwhile; ?>
				<!-- End row loop -->
			</table>
			<?php echo advancedPagination($type, $_SERVER['PHP_SELF'].'?type='.(int)$_GET['type'], (int)$_GET['page']); ?>
			<!-- None exist error message -->
			<?php else: ?>
			<p>Sorry, no <?php echo $type ?>s exist to manage.</p>
			<!-- End all ifs -->
			<?php endif; endif; endif; ?>
		</div>
		<div id="footer" class="roundedb">		
			Powered by LightBlog <?php LightyVersion() ?>    
	    </div>
	</div>
</body>
</html>