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

$rolequery = $dbh->query("SELECT role_name FROM roles WHERE role_id=".user()->role());
$role = $rolequery->fetch(PDO::FETCH_OBJ);
$role = $role->role_name;

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
				"Category" => "create-category.php"
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
				"Your Profile" => "profile.php?id=".user()->id(),
				"Add User" => "adduser.php"
			)
		),
		"Settings" => array(
			"link" => "settings.php",
			"children" => false
		),
		"Maintenance" => array(
			"link" => "error-log.php",
			"children" => array(
				"Backup and Optimize" => "backup.php",
				"Error Log" => "error-log.php"
			)
		),
	);

	foreach($menu as $topname => $attr)
	{
		$select = '';
		if(is_array($attr['children']) && in_array($selected, $attr['children']) || !is_array($attr['children']) && $attr['link'] == $selected)
		{
			$select = 'selected open';
		}
		echo '<li class="'.$select.'">';
		echo '<img src="style/new/'.strtolower($topname).'.png" class="nav-icon" alt="" />';
		if($attr['children'] === false)
		{
			echo '<a href="'.$attr['link'].'" class="nav-link single '.$select.'">'. l($topname).'</a>';
			echo '</li>';
		}
		else
		{
			echo '<a href="'.$attr['link'].'" class="nav-link">'.l($topname).'</a>';
			echo '<a href="#" class="nav-toggle"><img src="style/new/';
			if($select != '')
			{
				echo 'minus.png" alt="-" /></a>';
			}
			else
			{
				echo 'plus.png" alt="+" /></a>';
			}
			echo '<ul class="submenu '.$select.'">';
			foreach($attr['children'] as $name => $link)
			{
				echo '<li><a href="'.$link.'" class="nav-link ';
				if($link == $selected)
				{
					echo 'selected';
				}
				echo '">'.l($name).'</a></li>';
			}
			echo '</ul></li>';
		}
		$select = null;
	}
}


?>

		<div id="navigation">
			<div id="user">
				<img id="gravatar" src="<?php echo gravatar() ?>" title="<?php echo l('Your Gravatar, from gravatar.com'); ?>" alt="Gravatar" />
				<div>
					<strong><?php echo user()->displayName() ?></strong><br />
					<span><?php echo $role ?></span><br />
					<a href="profile.php?id=<?php echo user()->id() ?>"><?php echo l('Your Profile'); ?></a> | <a href="login.php?logout"><?php echo l('Logout'); ?></a>
				</div>
				<div class="clear"></div>
			</div>
			<ul id="menu">
				<?php buildMenu((!isset($selected) ? $selected = basename($_SERVER['REQUEST_URI']) : $selected)) ?>
			</ul>
			<div id="footer">
				<p><?php echo l('Powered by LightBlog %s', LightyVersion('r')); ?></p>
			</div>
		</div>
	</div>

	<script type="text/javascript">
	//<![CDATA[
		$('.submenu:not(".selected")').hide();
		$('ul#menu > li > a.nav-link:not(.single)').width(160);
		$('.nav-toggle').show().click(function()
		{
			if(!$(this).closest('li').is('.open'))
			{
				$('.submenu:not(:hidden)').slideUp('fast').prev('.nav-toggle').children('img').attr('src', 'style/new/plus.png');
				$('ul#menu > li.open').removeClass('open');
				$(this).children('img').attr('src', 'style/new/minus.png').parent().next('ul').slideDown('fast').parent().addClass('open');
			}
			else
			{
				$(this).children('img').attr('src', 'style/new/plus.png').parent().next('ul').slideUp('fast').parent().removeClass('open');
			}
		});
	//]]>
	</script>
</body>
</html>