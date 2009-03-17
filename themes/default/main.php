	<div id="content">
	<?php
	// run blog post query
	if (sqlite_num_rows($result01) > 0) {
		// start post loop
		while($post = sqlite_fetch_object($result01)) {
			// timestamp for date
			$timestamp = $post->date;
			// start post structure
			echo "<div class=\"postbox\">";
			// output title
			echo "<h2 class=\"post-title\"><a class=\"post-title\" href=\"post.php?id=".$post->id."\">".$post->title."</a></h2>";
			// output comment link, author name & date
			echo "<img src=\"themes/".$themeName."/style/date.png\" alt=\"Date\" /><span class=\"date\">".date("F j, Y", $timestamp)."</span>";
			echo "<img src=\"themes/".$themeName."/style/user.png\" alt=\"Written by\" /><span class=\"author\">".$post->author."</span>";
			$result02 = sqlite_query($handle, "SELECT * FROM comments WHERE post_id=".$post->id."") or die("SQLite query error: code 02<br>".sqlite_error_string(sqlite_last_error($handle)));
			echo "<img src=\"themes/".$themeName."/style/comment.png\" alt=\"Comments\" /><a href=\"post.php?id=".$post->id."\" title=\"Post a comment or read them!\"><span class=\"commentnum\">".sqlite_num_rows($result02)." Comments</span></a>";
			// output content
			echo "<p class=\"post\">".$post->post."</p><br />";
			// end post structure
			echo "</div>";
			// this code is repeated for every post in your database
		}
	}
	else { echo "No posts, sorry."; }
	
	?>
	</div>