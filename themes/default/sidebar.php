	<div id="sidebar">
	<ul>
	<li><a href="index.php">Home</a></li>
	<?php 
	if(isset($_SESSION['uservip'])) {
		echo '<li><a href="admin/dashboard.php">Dashboard</a></li>
		<li><a href="admin/login.php?logout=yes">Logout</a></li>'; 
	}
	else { 
		echo '<li><a href="admin/login.php">Login</a></li>
	<li><a href="admin/register.php">Register</a></li>'; } ?>
	<li><a class="rss" href="<?php echo $site_url; ?>rss.php">RSS Feed<img class="rssi" src="themes/<?php echo $themeName; ?>/style/rss.png" alt="" /></a></li>
	</ul>
	<?php
	
		// fetch pages from database
		$result10 = sqlite_query($handle, "SELECT * FROM pages ORDER BY id desc") or die("SQLite query error: code 02<br>".sqlite_error_string(sqlite_last_error($handle)));
		
		if (sqlite_num_rows($result10) > 0) {
			// start structure
			echo "<br /><h4>Pages</h4><ul>";
		// start page loop
		while($page = sqlite_fetch_object($result10)) {
			// output title
			echo "<li><a href=\"page.php?id=".$page->id."\">".$page->title."</a></li>";
		}
			// end post structure
			echo "</ul>";
			// this code is repeated for every post in your database
		}	
		else { echo " "; } ?>	
	</div>
