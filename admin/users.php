<?php session_start();

/*********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	admin/users.php
	
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

# Functions to find the start and limit for a query based on the page number
function findStart($input) { $input = $input - 1; return $input * 8; }

if($_GET['page'] > 1) {
	$result = $dbh->query("SELECT * FROM users ORDER BY id desc LIMIT ".findStart($_GET['page']).",8") or die(sqlite_error_string($dbh->lastError));
}
else {
	$result = $dbh->query("SELECT * FROM users ORDER BY id desc LIMIT 0,8") or die(sqlite_error_string($dbh->lastError));
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Manage Users - <?php bloginfo('title') ?></title>
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
		
		function banUser(id,user) {
			var answer = confirm("Really ban user \"" + user + "\"?");
			if(answer) {
				jQuery.ajax({
					data: "ban_user=true&amp;id=" + id,
					type: "POST",
					url: "<?php bloginfo('url') ?>Sources/ProcessAJAX.php",
					timeout: 3000,
					error: function() {
						alert("Failed to ban user " + user + ".");
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
			<h2 class="title"><img class="textmid" src="style/users.png" alt="" />Manage Users</h2>
			<!-- Check if any posts/pages exist -->
			<?php if($result->numRows() > 0): ?>
			<table class="managelist">
				<!-- Add table headings -->
				<tr>
					<th class="managelist">ID</th>
					<th class="managelist">Username</th>
					<th class="managelist">Email</th>
					<th class="managelist">Display Name</th>
					<th class="managelist">IP Address</th>
				</tr>		
				<!-- Start row loop -->
				<?php while($user = $result->fetchObject()): ?>	
				<tr id="tr<?php echo $user->id ?>">
					<td><?php echo $user->id ?></td>
					<td><?php echo $user->username ?></td>
					<td><?php echo $user->email ?></td>
					<td><?php echo $user->displayname ?></td>
					<td><?php echo $user->ip ?></td>
				</tr>
				<?php endwhile; ?>
				<!-- End row loop -->
			</table>
			<?php echo advancedPagination('user', $_SERVER['PHP_SELF'], (int)$_GET['page']); ?>
			<?php endif; ?>
		</div>
		<div id="footer" class="roundedb">		
			Powered by LightBlog <?php LightyVersion() ?>    
	    </div>
	</div>
</body>
</html>
