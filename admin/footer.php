<?php

/*********************************************

	LightBlog 0.9
	SQLite blogging platform

	admin/footer.php

	©2008-2012 The LightBlog Team. All
	rights reserved. Released under the
	GNU General Public License 3. For
	all licensing information, please
	see the LICENSE.txt document
	included in this distribution.

*********************************************/

$rolequery = $dbh->query("SELECT role FROM roles WHERE id=".get_userinfo('role')) or die(sqlite_error_string($dbh->lastError));
			
function buildMenu($selected)
{
	$menu = array(
		"Dashboard" => array(
			"link" => "dashboard.php",
			"children" => false
		),
		"Create" => array(
			"link" => "create.php?type=1",
			"children" => array(
				"Post" => "create.php?type=1",
				"Page" => "create.php?type=2",
				"Category" => "create.php?type=3"
			)
		),
		"Manage" => array(
			"link" => "manage.php?type=1",
			"children" => array(
				"Post" => "manage.php?type=1",
				"Page" => "manage.php?type=2",
				"Comments" => "comments.php",
				"Categories" => "manage.php?type=3"
			)
		),
		"Appearance" => array(
			"link" => "appearance.php",
			"children" => false
		),
		"Users" => array(
			"link" => "users.php",
			"children" => array(
				"Manage Users" => "users.php",
				"Your Profile" => "profile.php",
				"Add User" => "adduser.php"
			)
		),
		"Settings" => array(
			"link" => "settings.php",
			"children" => array(
				"General" => "settings.php",
				"Comments" => "settings-comments.php",
				"Maintenance" => "maintenance.php"
			)
		)
	);
	
	foreach($menu as $topname => $attr)
	{
		if(is_array($attr['children']) && in_array($selected, $attr['children']))
		{
			$select = true;
		}
		else
		{
			$select = false;
		}
		if($select == true || $selected == $attr['link'])
		{
			echo '<li class="selected">';
		}
		else
		{
			echo '<li>';
		}
		echo '<img src="style/new/'.strtolower($topname).'.png" class="nav-icon" alt="" />';
		if($attr['children'] === false)
		{
			echo '<a href="'.$attr['link'].'" class="nav-link single">'.$topname.'</a>';
			echo '</li>';
		}
		else
		{
			echo '<a href="'.$attr['link'].'" class="nav-link">'.$topname.'</a>';
			if($select == true)
			{
				echo '<a href="#" class="nav-toggle"><img src="style/new/minus.png" alt="-" /></a>';
				echo '<ul class="submenu selected">';
			}
			else
			{
				echo '<a href="#" class="nav-toggle"><img src="style/new/plus.png" alt="+" /></a>';
				echo '<ul class="submenu">';
			}
			foreach($attr['children'] as $name => $link)
			{
				if($link == $selected)
				{
					echo '<li><a href="'.$link.'" class="nav-link selected">'.$name.'</a></li>';
				}
				else
				{
					echo '<li><a href="'.$link.'" class="nav-link">'.$name.'</a></li>';
				}
			}
			echo '</ul></li>';
		}
		$select = null;
	}
}			
				

?>

		<div id="navigation">
			<div id="user">
				<img id="gravatar" src="<?php echo gravatar() ?>" title="Your Gravatar, from gravatar.com" alt="Gravatar" />
				<div>
					<strong><?php userinfo('displayname') ?></strong><br />
					<span><?php echo $rolequery->fetchSingle() ?></span><br />
					<a href="profile.php">Your Profile</a> | <a href="login.php?logout">Logout</a>
				</div>
				<div class="clear"></div>
			</div>
			<ul id="menu">
				<?php buildMenu($selected) ?>
			</ul>
			<div id="footer">
				<p>Powered by LightBlog <?php LightyVersion() ?></p>
			</div>	
		</div>
	</div>

	<script type="text/javascript">
		$('.submenu:not(".selected")').hide();
		$('.nav-toggle').click(function()
		{
			if(!$(this).closest('li').is('.selected'))
			{
				$('.submenu:not(:hidden)').slideUp('fast').prev('.nav-toggle').children('img').attr('src', 'style/new/plus.png');
				$('ul#menu > li.selected').removeClass('selected');
				$(this).children('img').attr('src', 'style/new/minus.png').parent().next('ul').slideDown('fast').parent().addClass('selected');
			}
			else
			{
				$(this).children('img').attr('src', 'style/new/plus.png').parent().next('ul').slideUp('fast').parent().removeClass('selected');
			}
		});
	</script>
</body>
</html>