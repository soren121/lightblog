	<div id="sidebar">
	<ul>
	<li><a href="index.php">Home</a></li>
	<?php if(isset($_SESSION['username'])): ?>
	<li><a href="admin/dashboard.php">Dashboard</a></li>
	<li><a href="admin/login.php?logout=yes">Logout</a></li>
	<?php else: ?>
	<li><a href="admin/login.php">Login</a></li>
	<li><a href="admin/register.php">Register</a></li>
	<?php endif; ?>
	<li><a class="rss" href="<?php bloginfo('url')?>feed.php">RSS Feed<img class="rssi" src="themes/<?php bloginfo('theme')?>/style/rss.png" alt="" /></a></li>
	<li><a class="rss" href="<?php bloginfo('url')?>feed.php?type=atom">Atom Feed<img class="rssi" src="themes/<?php bloginfo('theme')?>/style/atom.png" alt="" /></a></li>
	</ul>
	<?php if ($result10->numRows() > 0) {
			echo "<br /><h4>Pages</h4><ul>";
			while($page = $result10->fetchObject()) {
				echo "<li><a href=\"page.php?id=".$page->id."\">".$page->title."</a></li>";
			}
			echo "</ul>";
	} ?>	
	</div>
