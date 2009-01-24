	<div id="content">
	<?php
	while($page2 = sqlite_fetch_object($result05)) {
		// start post structure
		echo "<div class=\"pagebox\">";
		// output title
		echo "<h2 class=\"post-title\">".$page2->title."</h2><br />";
		// output content
		echo "<p class=\"post\">".$page2->page."</p><br /><br />";
		// end post structure
		echo "</div>";
		// this code is repeated for every post in your database
	}

	// SQLite queries done, closing database
	sqlite_close($handle);
	
	?>
	</div>