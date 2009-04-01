	<div id="content">
	<?php
	// run blog post query
	if($result01->numRows() > 0) {
		// start post loop
		while($post = $result01->fetchObject()) {
			// timestamp for date
			$timestamp = $post->date;
			// query to pull comment number
			$result02 = $dbh->query("SELECT * FROM comments WHERE post_id=".$post->id."") or die(sqlite_error_string($dbh->lastError));
			// start post structure
			echo "<div class=\"postbox\">";
			// output title
			echo "<h2 class=\"post-title\"><a class=\"post-title\" href=\"post.php?id=".$post->id."\">".undoMagicString($post->title)."</a></h2>";
			// output comment link, author name & date
			echo "<img src=\"themes/".$themeName."/style/date.png\" alt=\"Date\" /><span class=\"date\">".date("F j, Y", $timestamp)."</span>";
			echo "<img src=\"themes/".$themeName."/style/user.png\" alt=\"Written by\" /><span class=\"author\">".$post->author."</span>";
			echo "<img src=\"themes/".$themeName."/style/comment.png\" alt=\"Comments\" /><a href=\"post.php?id=".$post->id."\" title=\"Post a comment or read them!\"><span class=\"commentnum\">".$result02->numRows()." Comments</span></a>";
			// output content
			echo "<p class=\"post\">".undoMagicString($post->post)."</p><br />";
			// end post structure
			echo "</div>";
			// this code is repeated for every post in your database
		}
	}
	else { echo "No posts, sorry."; }	
	?>
	</div>