<?php session_start();

/*********************************************

	LightBlog 0.9
	SQLite blogging platform

	admin/users.php

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

# Functions to find the start for a query based on the page number
function findStart($input) { $input = $input - 1; return $input * 8; }

if(isset($_GET['page']) && $_GET['page'] > 1) {
		$result = $dbh->query("SELECT * FROM users ORDER BY id asc LIMIT ".findStart($_GET['page']).",8") or die(sqlite_error_string($dbh->lastError));
}
else {
	$result = $dbh->query("SELECT * FROM users ORDER BY id asc LIMIT 0,8") or die(sqlite_error_string($dbh->lastError));
}

$role_array = array(1 => 'Standard', 2 => 'Moderator', 3 => 'Administrator');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Manage Users - <?php bloginfo('title') ?></title>
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('url') ?>admin/style/style.css" />
	<!--[if lte IE 7]><style type="text/css">html.jqueryslidemenu { height: 1%; }</style><![endif]-->
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.js"></script>
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.SlideMenu.js"></script>
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.Corners.js"></script>
	<script type="text/javascript">			
		$(document).ready(function(){ 
			$('.rounded').corner(); 
			$('.roundedt').corner("round top 10px"); 
			$('.roundedb').corner("round bottom 10px");
		});

		function deleteUser(id,user) {
			var answer = confirm("Really delete user \"" + user + "\"?");
			if(answer) {
				jQuery.ajax({
					data: "deleteusersubmit=true&id=" + id,
					type: "POST",
					url: "<?php bloginfo('url') ?>Sources/ProcessAJAX.php",
					timeout: 3000,
					error: function() {
						alert("Failed to delete user " + user + ".");
					},
					success: function(r) {
						alert(r);
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
			<?php if(permissions(2)): ?>
			<h2 class="title"><img class="textmid" src="style/users.png" alt="" />Manage Users</h2>
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
					<?php if(userFetch('username', 'r') !== $user->username): ?>
						<td class="c"><img src="style/delete-user.png" onclick="deleteUser('<?php echo $user->id ?>', '<?php echo $user->username ?>');" alt="Delete User" style="cursor:pointer;" /></td>
					<?php else: ?>
						<td>&nbsp;</td>
					<?php endif; ?>
				</tr>
				<?php endwhile; ?>
				<!-- End row loop -->
			</table>
			<?php echo advancedPagination('users', $_SERVER['PHP_SELF'], (int)$_GET['page']); ?>
			<?php endif; endif; ?>
		</div>
		<div id="footer" class="roundedb">		
			Powered by LightBlog <?php LightyVersion() ?>    
		</div>
	</div>
</body>
</html>
