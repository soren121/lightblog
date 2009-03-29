	<div id="content">
	<?php
	// run blog post query
	if ($result03->numRows() > 0) {
		// start post loop
		while($post = $result03->fetchObject()) {
			// start post structure
			echo "<div class=\"postbox\">";
			// output title
			echo "<h2 class=\"post-title\">".$post->title."</h2>";
			// output content
			echo "<p class=\"post\">".$post->post."</p><br />";
			// end post structure
			echo "</div>";
			// this code is repeated for every post in your database
		}
	}
	
	// get comments
	// if there are no comments:
	if($result04->numRows() == 0) {
		echo "<p>No comments have been made on this post yet.</p>";
	}
	
	// if comments exist, display them
	else { 
		while($comments = $result04->fetch(SQLITE_ASSOC)) {
		echo "<div class=\"comment\">
			  <img src=\"".fetchGravatar($comments['email'], 30, 'r')."\" style=\"float:right;margin-right: 20px;\" alt=\"Gravatar\" />
		      <b><i>".$comments['username']."</i></b> says:<br />
		      <p class=\"com-content\">".$comments['text']."</p>
		      </div>";
		}
	}
	
	// check if comment has been POSTed
	if(isset($_POST['comment_submit'])) {
		// check if all fields are formed
		if(strlen($com_name) and strlen($com_email) and strlen($com_text) > 0) {
			$com_name = sqlite_escape_string($_POST['username']);
			$com_email = sqlite_escape_string($_POST['email']);
			$com_website = sqlite_escape_string($_POST['website']);
			$com_text = sqlite_escape_string(removeXSS($_POST['text']));
			$dbh->query("INSERT INTO comments (post_id,username,email,website,text) VALUES('".(int)$_GET['id']."','".$com_name."','".$com_email."','".$com_website."','".$com_text."')")  or die(sqlite_error_string($dbh->lastError));
			echo'
			<div class="comment">
			<img src="'.fetchGravatar($com_email, 30, 'r').'" style="float:right;margin-right:20px;" alt="Gravatar" />
			<b><i>'.$com_name.'</i></b> says:<br/>
			<p class="com-content">'.$com_text.'</p>
			</div>';
		}
		else { echo '<p style="color:red">You forgot to fill in a field. Please fill in all the fields and try again.</p>'; }
    }
  
	?>
	
	<h4 class="commentform-title">Post a comment</h4><br />
	<form action="" method="post">
    	<table>
      		<tr><td>Name:</td><td><input name="username" type="text" /></td></tr>
      		<tr><td>Email:</td><td><input name="email" type="text"/></td></tr>
      		<tr><td>Website:</td><td><input name="website" type="text"/></td></tr>
      		<tr><td>Post:</td><td><textarea cols="41" rows="10" name="text"></textarea></td></tr>
      		<tr><td colspan="2"><input name="comment_submit" type="submit" value="Submit"/></td></tr>
    	</table>
  	</form>
	</div>