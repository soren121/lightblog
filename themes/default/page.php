	<div id="content">
	<?php
	while($page2 = $result05->fetchObject()) {
		// start page structure
		echo "<div class=\"pagebox\">";
		// output title
		echo "<h2 class=\"post-title\">".undoMagicString($page2->title)."</h2><br />";
		// output content
		echo "<p class=\"post\">".undoMagicString($page2->page)."</p><br /><br />";
		// end post structure
		echo "</div>";
	}	
	?>
	</div>