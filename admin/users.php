<?php
/***********************************************

	LightBlog 0.9
	SQLite blogging platform

	admin/users.php

	©2008-2012 The LightBlog Team. All
	rights reserved. Released under the
	GNU General Public License 3. For
	all licensing information, please
	see the LICENSE.txt document
	included in this distribution.

***********************************************/

// Require config file
require('../Sources/Core.php');
require(ABSPATH .'/Sources/Admin.php');

$_GET['page'] = !empty($_GET['page']) && (int)$_GET['page'] > 1 ? (int)$_GET['page'] : 1;

if(isset($_GET['page']) && $_GET['page'] > 1) {
		$result = $dbh->query("SELECT * FROM users ORDER BY username asc LIMIT ".(($_GET['page'] - 1) * 8).",8") or die(sqlite_error_string($dbh->lastError));
}
else {
	$result = $dbh->query("SELECT * FROM users ORDER BY username asc LIMIT 0,8") or die(sqlite_error_string($dbh->lastError));
}

$role_array = array(1 => 'Standard', 2 => 'Moderator', 3 => 'Administrator');
$pagination = advancedPagination('users', $_SERVER['PHP_SELF'], $_GET['page']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Manage Users - <?php bloginfo('title') ?></title>
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('url') ?>admin/style/style.css" />
	<!--[if lte IE 7]><style type="text/css">html.jqueryslidemenu { height: 1%; }</style><![endif]-->
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.js"></script>
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.SlideMenu.js"></script>
	<script type="text/javascript">
		function deleteUser(id,user) {
			var answer = confirm("Really delete user \"" + user + "\"?");
			if(answer) {
				jQuery.ajax({
					data: "deleteusersubmit=true&csrf_token=<?php echo $_SESSION['csrf_token']; ?>&id=" + id,
					type: "POST",
					url: "<?php bloginfo('url') ?>Sources/ProcessAJAX.php",
					timeout: 3000,
					error: function() {
						$('#notifybox').text('Failed to delete user.').css("background","#E36868").css("border-color","#a40000").slideDown("normal");
					},
					success: function(json) {
						var r = jQuery.parseJSON(json);
						if(r.result == 'success') {
							var tr = '#tr' + id;
							$(tr).hide();
						}
						else {
							$('#notifybox').text('Failed to delete user; ' + r.response).css("background","#E36868").css("border-color","#a40000").slideDown("normal");
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
			<?php if(permissions(2)): ?>
			<h2 class="title"><img class="textmid" src="style/users.png" alt="" />Manage Users</h2>
			<div id="notifybox" style="margin:3px 0 -3px 5px;width:588px;"></div>
			<!-- Check if any posts/pages exist -->
			<?php if($result->numRows() > 0): ?>
			<table class="managelist">
				<!-- Add table headings -->
				<tr>
					<th class="managelist">Username</th>
					<th class="managelist">Role</th>
					<th class="managelist">Email</th>
					<th class="managelist">Display Name</th>
					<th class="managelist">IP Address</th>
					<th class="managelist">Delete</th>
				</tr>
				<!-- Start row loop -->
				<?php while($user = $result->fetchObject()): ?>
				<tr id="tr<?php echo $user->id ?>">
					<td><?php echo $user->username ?></td>
					<td><?php echo $role_array[$user->role] ?></td>
					<td><?php echo $user->email ?></td>
					<td><?php echo $user->displayname ?></td>
					<td><?php echo $user->ip ?></td>
					<?php if(user()->name() !== $user->username && user()->role() >= $user->role): ?>
						<td class="c"><img src="style/delete-user.png" onclick="deleteUser('<?php echo $user->id ?>', '<?php echo $user->username ?>');" alt="Delete User" style="cursor:pointer;" /></td>
					<?php else: ?>
						<td class="c"><img src="style/delete-user-d.png" alt="" title="You're not allowed to delete this person." /></td>
					<?php endif; ?>
				</tr>
				<?php endwhile; ?>
				<!-- End row loop -->
			</table>
			<?php echo $pagination; ?>
			<?php endif; endif; ?>
		</div>
		<div id="footer" class="roundedb">
			Powered by LightBlog <?php LightyVersion() ?>
		</div>
	</div>
</body>
</html>
