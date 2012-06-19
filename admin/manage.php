<?php session_start();

/*********************************************

	LightBlog 0.9
	SQLite blogging platform

	admin/manage.php

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

$_GET['page'] = !empty($_GET['page']) ? (int)$_GET['page'] : 1;

if((int)$_GET['type'] == 1) { $type = 'posts'; }
elseif((int)$_GET['type'] == 2) { $type = 'pages'; }
elseif((int)$_GET['type'] == 3) { $type = 'categories'; }

$pagination = advancedPagination($type, $_SERVER['PHP_SELF'].'?type='.(int)$_GET['type'], $_GET['page']);

if(isset($_GET['page']) && $_GET['page'] > 1) {
		$result = $dbh->query("SELECT * FROM ".$type." ORDER BY id desc LIMIT ".(($_GET['page'] - 1) * 8).",8") or die(sqlite_error_string($dbh->lastError));
}
else {
	$result = $dbh->query("SELECT * FROM ".$type." ORDER BY id desc LIMIT 0,8") or die(sqlite_error_string($dbh->lastError));
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Manage <?php echo ucwords($type) ?> - <?php bloginfo('title') ?></title>
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('url') ?>admin/style/style.css" />
	<!--[if lte IE 7]><style type="text/css">html.jqueryslidemenu { height: 1%; }</style></script><![endif]-->
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.js"></script>
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.SlideMenu.js"></script>
	<script type="text/javascript">
		function deleteItem(id,title) {
			var answer = confirm("Really delete <?php echo ($type == 'categories' ? 'category' : substr($type, 0, -1)) ?> \"" + title + "\"?");
			if(answer) {
				jQuery.ajax({
					data: "delete=true&csrf_token=<?php echo $_SESSION['csrf_token']; ?>&type=<?php echo $type ?>&id=" + id,
					type: "POST",
					url: "<?php bloginfo('url') ?>Sources/ProcessAJAX.php",
					timeout: 3000,
					error: function() {
						$('#notifybox').text('Failed to delete <?php echo ($type == 'categories' ? 'category' : substr($type, 0, -1)) ?>.').css("background","#E36868").css("border-color","#a40000").slideDown("normal");
					},
					success: function(r) {
						if(r.result == 'success') {
							var tr = '#tr' + id;
							$(tr).hide();
						}
						else {
							$('#notifybox').text('Failed to delete <?php echo ($type == 'categories' ? 'category' : substr($type, 0, -1)) ?>; ' + r.response).css("background","#E36868").css("border-color","#a40000").slideDown("normal");
						}
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
			<?php if(permissions(1)): if(!isset($type)): ?>
			<p>The type of content to manage was not specified. You must have taken a bad link. Please
			use the navigation bar above to choose the correct type.</p>
			<!-- They were, so continue -->
			<?php else: ?>
			<h2 class="title"><img class="textmid" src="style/manage.png" alt="" />Manage <?php echo ucwords($type) ?></h2>
			<div id="notifybox" style="margin:3px 0 -3px 5px;width:588px;"></div>
			<?php if($result->numRows() > 0): ?>
			<table class="managelist">
				<!-- Add table headings -->
				<tr>
					<th class="managelist">Title</th>
					<?php if($type == 'categories'): ?>
						<th class="managelist">Info</th>
					<?php else: ?>
						<th class="managelist">Author</th>
						<th class="managelist">Date</th>
						<th class="managelist">Published</th>
					<?php endif; ?>
					<th class="managelist">Edit</th>
					<th class="managelist">Delete</th>
				</tr>
				<!-- Start row loop -->
				<?php while($row = $result->fetchObject()): ?>
				<tr id="tr<?php echo $row->id ?>">
					<?php if($type == 'categories'): ?>
						<td style="width:150px;"><?php echo $row->fullname ?></td>
						<td><?php echo implode(' ', array_slice(explode(' ', $row->info), 0, 8)); ?></td>
					<?php else: ?>
						<td style="width:160px;">
						<?php if($type != 'categories'): ?>
							<a href="<?php echo get_bloginfo('url').'?'.substr($type, 0, -1).'='.$row->id.'">'.$row->title; ?></a>
						<?php else: echo $row->title; endif; ?>
						</td>
						<td><?php echo $row->author ?></td>
						<td><?php echo date('n/j/Y', $row->date) ?></td>
						<td class="c">
							<?php if($row->published == 1): ?>
								<img src="style/check.png" alt="Published" />
							<?php else: ?>
								<img src="style/cross.png" alt="Not published" />
							<?php endif; ?>
						</td>
					<?php endif; ?>
					<?php if(($type !== 'categories') && (permissions(1) && get_userinfo('displayname') == $row->author) || (permissions(2))): ?>
						<td class="c"><a href="edit.php?type=<?php echo (int)$_GET['type'] ?>&amp;id=<?php echo $row->id ?>"><img src="style/edit.png" alt="Edit" style="border:0;" /></a></td>
						<td class="c"><img src="style/delete.png" alt="Delete" onclick="deleteItem(<?php echo $row->id.', \''.addcslashes(($type == 'categories') ? $row->fullname : $row->title, '\'').'\'' ?>);" style="cursor:pointer;" /></td>
					<?php else: ?>
						<td class="c"><img src="style/edit-d.png" alt="" title="You aren't allowed to edit this <?php echo substr($type, 0, -1); ?>." /></td>
						<td class="c"><img src="style/delete-d.png" alt="" title="You aren't allowed to delete this <?php echo substr($type, 0, -1); ?>." /></td>
					<?php endif; ?>
				</tr>
				<?php endwhile; ?>
				<!-- End row loop -->
			</table>
			<?php echo $pagination; ?>
			<!-- None exist error message -->
			<?php else: ?>
			<p>Sorry, no <?php echo $type ?> exist to manage.</p>
			<!-- End all ifs -->
			<?php endif; endif; endif; ?>
		</div>
		<div id="footer" class="roundedb">
			Powered by LightBlog <?php LightyVersion() ?>
	    </div>
	</div>
</body>
</html>
