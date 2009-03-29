	<div id="sidebar">
	<table style="border: 0;">
	<tr>
		<td><img src="<?php fetchGravatar($_SESSION['email'], 60) ?>" alt="Gravatar" /></td>
		<td><span class="username"><?php echo $_SESSION['username'] ?></span><br />
		<?php if($_SESSION['role'] >= 1): ?> 
		<span class="admin">Admin</span>
		<?php else: ?>
		<span class="normal">Normal</span>
		<?php endif; ?>
		</td>
	</tr>
	</table>
	<ul>
	<li><a href="../index.php">Home</a></li>
	<li><a href="dashboard.php">Dashboard</a></li>
	<?php if($_SESSION['role'] >= 1): ?>
	<li><a href="create.php?type=post">Create a post</a></li>
	<li><a href="create.php?type=page">Create a page</a></li>
	<li><a href="manage.php?type=post">Manage posts</a></li>
	<li><a href="manage.php?type=page">Manage pages</a></li>
	<?php endif; ?>
	<li><a href="profile.php">Profile</a></li>
	<li><a href="login.php?logout=yes">Logout</a></li>
	</ul>
	</div>
