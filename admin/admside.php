	<div id="sidebar">
	<table style="border: 0;"><tr><td><?php echo'<img src="'.$gravatar.'" alt="Gravatar" />';?></td>
	<td><?php echo'<span class="username">'.$_SESSION['username'].'</span>'; ?><br />
	<?php if($_SESSION['uservip'] == "1") { echo'<span class="admin">Admin</span>'; } else { echo'<span class="normal">Normal</span>'; }?></td></tr></table>
	<ul>
	<li><a href="../index.php">Home</a></li>
	<li><a href="dashboard.php">Dashboard</a></li>
	<?php if($_SESSION['uservip'] == "1") { echo'
	<li><a href="create.php?type=post">Create a post</a></li>
	<li><a href="create.php?type=page">Create a page</a></li>
	<li><a href="manage.php?type=post">Manage posts</a></li>
	<li><a href="manage.php?type=page">Manage pages</a></li>'; } else { echo ''; } ?>
	<li><a href="profile.php">Profile</a></li>
	<li><a href="login.php?logout=yes">Logout</a></li>
	</ul>
	</div>
