<?php session_start();

/*********************************************

	LightBlog 0.9
	SQLite blogging platform

	admin/comments.php

	©2009-2010 The LightBlog Team. All 
	rights reserved. Released under the 
	GNU General Public License 3. For 
	all licensing information, please 
	see the LICENSE.txt document 
	included in this distribution.

*********************************************/

// Require config file
require('../config.php');
require(ABSPATH .'/Sources/Core.php');

if(isset($_GET['page']) && $_GET['page'] > 1) {
		$result = $dbh->query("SELECT * FROM comments ORDER BY id desc LIMIT ".((($_GET['page']) - 1) * 8).",8") or die(sqlite_error_string($dbh->lastError));
}
else {
	$result = $dbh->query("SELECT * FROM comments ORDER BY id desc LIMIT 0,8") or die(sqlite_error_string($dbh->lastError));
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Manage Comments - <?php bloginfo('title') ?></title>
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
			$('.nApproved').hover(
				function() {
					if(window.ajaxr == 'yes') {
						void(0);
					}
					else {
						$(this).css('cursor', 'pointer').empty().html('<span style="color:green;">Approve?</span>');	
					}	
				},
				function() {
					if(typeof(window.ajaxr) == 'undefined') {
						$(this).empty().html('<img src="style/cross.png" alt="Not approved" />');
					}
				}
			);	
			$('.nApproved').click(function() {
				$(this).empty().html('<img src="style/loading.gif" alt="" class="loader" />');
				var id = $(this).parent().attr('id').substr(2);
				jQuery.ajax({
					data: "approvecomment=true&id=" + id,
					type: "POST",
					url: "<?php bloginfo('url') ?>Sources/ProcessAJAX.php",
					timeout: 3000,
					error: function() {
						alert("Failed to approve comment.");
						$('.nApproved').empty().html('<img src="style/cross.png" alt="Not approved" />');
					},
					success: function() {
						$('.nApproved').removeClass('nApproved').addClass('approved').css('cursor', 'default').empty().html('<img src="style/check.png" alt="Approved" />');
						window.ajaxr = 'yes';
					}
				});
			});
		});

		function deleteItem(id) {
			var answer = confirm("Really delete comment #" + id + "?");
			if(answer) {
				jQuery.ajax({
					data: "delete=true&type=comments&id=" + id,
					type: "POST",
					url: "<?php bloginfo('url') ?>Sources/ProcessAJAX.php",
					timeout: 3000,
					error: function() {
						alert("Failed to delete comments.");
					},
					success: function(r) {
						var tr = '#tr' + id;
						$(tr).hide();
					}
				})
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
			<?php if(permissions(2)): ?>
			<h2 class="title"><img class="textmid" src="style/manage.png" alt="" />Manage Comments</h2>
			<!-- Check if any posts/pages exist -->
			<?php if($result->numRows() > 0): ?>
			<table class="managelist">
				<!-- Add table headings -->
				<tr>
					<th class="managelist">Author</th>
					<th class="managelist">Excerpt</th>
					<th class="managelist">Date</th>
					<th class="managelist">Approved</th>
					<th class="managelist">Delete</th>
				</tr>		
				<!-- Start row loop -->
				<?php while($row = $result->fetchObject()): ?>	
				<tr id="tr<?php echo $row->id ?>">
					<td style="white-space:nowrap;"><img src="http://www.gravatar.com/avatar.php?gravatar_id=<?php echo md5($row->email) ?>&amp;size=24" style="vertical-align:middle;margin-right:5px;" /><?php echo $row->name ?></td>
					<td><?php echo implode(' ', array_slice(explode(' ', $row->text), 0, 6)); ?></td>
					<td><?php echo date('n/j/Y \a\t h:i a', $row->date) ?></td>
					<?php if($row->published == 1): ?>
						<td class="c approved"><img src="style/check.png" alt="Approved" /></td>
					<?php else: ?>
						<td class="c nApproved"><img src="style/cross.png" alt="Not approved" /></td>
					<?php endif; ?>
					<td class="c"><img src="style/delete.png" alt="Delete" onclick="deleteItem(<?php echo $row->id ?>);" style="cursor:pointer;" /></td>
				</tr>
				<?php endwhile; ?>
				<!-- End row loop -->
			</table>
			<?php echo advancedPagination('comments', ($_SERVER['HTTPS'] == 'on' ? 'https' : 'http')."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'], (int)$_GET['page']); ?>
			<!-- None exist error message -->
			<?php else: ?>
			<p>Sorry, there are no comments to manage.</p>
			<!-- End all ifs -->
			<?php endif; endif; ?>
		</div>
		<div id="footer" class="roundedb">		
			Powered by LightBlog <?php LightyVersion() ?>    
		</div>
	</div>
</body>
</html>
