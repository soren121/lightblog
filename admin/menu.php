<?php

/*********************************************

	LightBlog 0.9
	SQLite blogging platform

	admin/menu.php

	©2008-2012 The LightBlog Team. All
	rights reserved. Released under the
	GNU General Public License 3. For
	all licensing information, please
	see the LICENSE.txt document
	included in this distribution.

*********************************************/

?>

		<div id="navigation" class="jqueryslidemenu">
			<ul>
				<li><a href="dashboard.php">Dashboard</a></li>
				<?php if(permissions(2)): ?>
					<li><a href="javascript:void(0)">Create</a>
						<ul>
							<li><a href="create.php?type=1">Post</a></li>
							<li><a href="create.php?type=2">Page</a></li>
							<li><a href="create.php?type=3">Category</a></li>
						</ul>
					</li>
				<?php endif; if(permissions(1) && !permissions(2)): ?>
					<li><a href="create.php?type=1">Create Post</a></li>
				<?php endif; if(permissions(2)): ?>
					<li><a href="javascript:void(0)">Manage</a>
						<ul>
							<li><a href="manage.php?type=1">Post</a></li>
							<li><a href="manage.php?type=2">Page</a></li>
							<li><a href="manage.php?type=3">Category</a></li>
							<li><a href="comments.php">Comments</a></li>
						</ul>
					</li>
				<?php endif; if(permissions(1) && !permissions(2)): ?>
					<li><a href="manage.php?type=1">Manage Post</a></li>
				<?php endif; if(permissions(3)): ?>
					<li><a href="design.php">Design</a></li>
				<?php endif; if(permissions(1) && !permissions(2)): ?>
					<li><a href="profile.php">Your Profile</a></li>
				<?php endif; if(permissions(2)): ?>
					<li><a href="users.php">Users</a>
						<ul>
							<li><a href="users.php">Manage Users</a></li>
							<li><a href="profile.php">Your Profile</a></li>
							<?php if(permissions(3)): ?>
								<li><a href="adduser.php">Add User</a></li>
							<?php endif; ?>
						</ul>
					</li>
				<?php endif; if(permissions(3)): ?>
					<li><a href="settings.php">Settings</a></li>
					<li><a href="maintenance.php">Maintenance</a>
						<ul>
							<li><a href="maintenance.php?area=errors">Error Log</a></li>
						</ul>
					</li>
				<?php endif; ?>
				<li><a href="login.php?logout=yes">Logout</a></li>
			</ul>
		</div>