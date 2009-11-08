<?php

/*********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	admin/menu.php
	
	©2009 soren121. All rights reserved.
	Released under the GNU General
	Public License. For all licensing
	information, please see the
	LICENSE.txt document included in this
	distribution.

*********************************************/

?>      
		
		<div id="navigation" class="jqueryslidemenu">
			<ul>
				<li><a href="dashboard.php">Dashboard</a></li>
				<li><a href="javascript:void(0)">Create</a>
					<ul>
						<li><a href="create.php?type=1">Post</a></li>
						<li><a href="create.php?type=2">Page</a></li>
					</ul>
				</li>
				<li><a href="javascript:void(0)">Manage</a>
					<ul>
						<li><a href="manage.php?type=1">Post</a></li>
						<li><a href="manage.php?type=2">Page</a></li>
					</ul>
				</li>
				<li><a href="design.php">Design</a></li>
				<li><a href="users.php">Users</a>
					<ul>
						<li><a href="users.php">Manage Users</a></li>
						<li><a href="profile.php">Your Profile</a></li>
						<li><a href="adduser.php">Add User</a></li>
					</ul>
				</li>
				<?php if(userFetch('role', 'r') >= 1): ?>
				<li><a href="settings.php">Settings</a>
					<ul>
						<li><a href="settings.php">General Settings</a></li>
					</ul>
				</li>
				<?php endif; ?>
				<li><a href="login.php?logout=yes">Logout</a></li>
			</ul>
		</div>