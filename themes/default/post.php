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
	    $grav_default="http://use.perl.org/images/pix.gif";
	    $grav_url = "http://www.gravatar.com/avatar.php?gravatar_id=".md5($comments['email'])."&amp;default=".urlencode($grav_default)."&amp;size=30";
		echo "<div class=\"comment\">
			  <img src=\"".$grav_url."\" style=\"float:right;margin-right: 20px;\" alt=\"Gravatar\" />
		      <b><i>".$comments['username']."</i></b> says:<br />
		      <p class=\"com-content\">".$comments['text']."</p>
		      </div>";
		}
	}
	
	// check if comment has been POSTed
	if(isset($_POST['comment_submit'])) {					 	
	 	$com_name = $_POST['username'];
	 	$com_email = $_POST['email'];
	 	$com_website = $_POST['website'];
	 	$com_text = $_POST['text'];
	 	$dbh->query("INSERT INTO comments (post_id,username,email,website,text) VALUES('".$_GET['id']."','".$com_name."','".$com_email."','".$com_website."','".$com_text."')")  or die(sqlite_error_string($dbh->lastError));
		echo "<p>Your comment has been submitted. Thank you.</p>";
    }
  
	?>
	
	<script type="text/javascript" src="Sources/nicEdit.js"></script>
	<script type="text/javascript">bkLib.onDomLoaded(function(){new nicEditor({iconsPath:'Sources/nicEditorIcons.gif',xhtml:true}).panelInstance('wysiwyg');});</script>
	<h4 class="commentform-title">Post a comment</h4><br />
	<form action="" method="post">
    	<table>
      		<tr><td>Name:</td><td><input name="username" type="text" /></td></tr>
      		<tr><td>Email:</td><td><input name="email" type="text"/></td></tr>
      		<tr><td>Website:</td><td><input name="website" type="text"/></td></tr>
      		<tr><td>Post:</td><td><textarea cols="41" rows="10" name="text" id="wysiwyg"></textarea></td></tr>
      		<tr><td colspan="2"><input name="comment_submit" type="submit" value="Submit"/></td></tr>
    	</table>
  	</form>
	</div>