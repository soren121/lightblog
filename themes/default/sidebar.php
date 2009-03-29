	<div id="sidebar">
	<ul>
	<li><a href="index.php">Home</a></li>
	<?php 
	if(isset($_SESSION['username'])): ?>
	<li><a href="admin/dashboard.php">Dashboard</a></li>
	<li><a href="admin/login.php?logout=yes">Logout</a></li>
	<?php else: ?>
	<li><a href="admin/login.php">Login</a></li>
	<li><a href="admin/register.php">Register</a></li>
	<?php endif; ?>
	<li><a class="rss" href="<?php echo bloginfo('url');?>feed.php">RSS Feed<img class="rssi" src="themes/<?php echo $themeName; ?>/style/rss.png" alt="" /></a></li>
	</ul>
	<?php		
		if ($result10->numRows() > 0) {
			// start structure
			echo "<br /><h4>Pages</h4><ul>";
			// start page loop
			while($page = $result10->fetchObject()) {
				// output title
				echo "<li><a href=\"page.php?id=".$page->id."\">".$page->title."</a></li>";
			}
			// end page structure
			echo "</ul>";
		}
		else { echo " "; } ?>	
	</div>
