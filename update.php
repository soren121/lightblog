<?php

/*********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	update.php
	
	©2009-2010 The LightBlog Team. All 
	rights reserved. Released under the 
	GNU General Public License 3. For 
	all licensing information, please 
	see the LICENSE.txt document 
	included in this distribution.

*********************************************/

// Shutdown Magic Quotes automatically
// Highly inefficient, but there isn't much we can do about it
if(get_magic_quotes_gpc()) {
	function stripslashes_gpc(&$value) {
		$value = stripslashes($value);
	}
	array_walk_recursive($_GET, 'stripslashes_gpc');
    array_walk_recursive($_POST, 'stripslashes_gpc');
    array_walk_recursive($_COOKIE, 'stripslashes_gpc');
    array_walk_recursive($_REQUEST, 'stripslashes_gpc');
}

// Gets directory URL
function baseurl() {
	$site_url = explode('/', $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
	unset($site_url[count($site_url)-1]);
	$site_url = implode('/', $site_url);
	$site_url = 'http://'.$site_url.'/';
	return $site_url;
}

// Operates the side menu selectors
// First options specifies page
// Second option defines if we're changing the page or just reading it
function menuClass($item, $op = 0) {
	static $cur_item = 1;
	if($op == 1) {
		$cur_item = (int)$item;
	}
	else {
		if($cur_item == $item) {
			echo "selected";
		}
		if($cur_item > $item) {
			echo "done";
		}
		if($cur_item < $item) {
			echo "notdone";
		}
	}
}

// Will process after Step 1
if(isset($_POST['reqmet'])) {
	// Set new installer page
	$page = 'dbsetup';
	menuClass(2, 1);
}

function update() {
	require('config.php');
	$dbh = new SQLiteDatabase( DBH );
	// Open, read, and close SQL file
	if(is_readable('update.sql')) {
		$sqlh = fopen('update.sql', 'r');
		$sql = fread($sqlh, filesize('update.sql'));
		fclose($sqlh);
	}
	else {
		// Attempt to make it readable
		if(!chmod('update.sql', 0644)) {
			return 'Failed to open update.sql. Please chmod it to 644 and try again.';
		}
	}
	if(!$dbh->queryExec($sql, $errormsg)) {
		return 'Failed to write to the database because: '.$errormsg.'.';
	}
}

// Will process after Step 2
if(isset($_POST['update'])) {
	$return = update();
	// Check for errors
	if(!$return == null) {
		$error = $return;
		$page = null;
	}
	else {
		// Set new installer page
		$page = "finish";
		menuClass(2, 1);
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>LightBlog Updater</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<style type="text/css">
		body {
			background: #EDEDED;
			font-family: "Trebuchet MS", "Verdana", sans;
		}
		#wrapper {
			width: 870px;
			min-height: 540px;
			margin: 20px auto;
		}
		#header {
			height: 35px;
			background: #2B5EA1;
			color: #fff;
		}
		#header h3 {
			font-size: 1.2em;
			padding: 5px 0 0 8px;
		}
		#sidebar {
			background: #D6DFEB;
			width: 239px;
			float: left;
			min-height: 505px;
			border-left: 1px dotted #5E9BEB;
			border-right: 1px dotted #5E9BEB;
			border-bottom: 1px dotted #5E9BEB;
		}
		#sidebar ul {
			list-style-type: circle;
			font-size: 0.9em;
			margin-top: 25px;
		}
		#sidebar ul li {
			margin-bottom: 3px;
		}
		#sidebar ul li.selected {
			color: #000;
		}
		#sidebar ul li.done {
			color: #9BB1CF;
		}
		#sidebar ul li.notdone {
			color: #748CAB;
		}
		#content {
			width: 608px;
			min-height: 495px;
			float: left;
			background: #fff;
			border-bottom: 1px dotted #5E9BEB;
			border-right: 1px dotted #5E9BEB;
			padding: 5px 10px;
		}
		#content h2 {
			color: #78B1EB;
			margin: 10px 0 5px 10px;
		}
		#content p {
			margin: 10px 0 0 25px;
			font-size: .98em;
		}
		#content table {
			margin: 20px 0 0 25px;
			border-right: 1px solid #CBE1F2;
			border-bottom: 1px solid #CBE1F2;
			border-collapse: collapse;
		}
		#content td, #content th {
			padding: 3px 20px 3px 7px;
			border-top: 1px solid #CBE1F2;
			border-left: 1px solid #CBE1F2;
		}
		#content th {
			background: #DCE8F2;
		}
		#content span#error {
			margin: 15px 0 0 25px;
			color: #9C0606;
			font-size: .9em;
		}
		form {
			margin: 20px 0 0 25px;
		}
		button {
			margin: 15px 0 0 25px;
		}
		label {
			font-size: .9em;
			font-weight: bold;
		}
		input[type="submit"], button {
			padding: 3px 10px 3px 10px;
		}
		div.clear {
			clear: both;
		}
	</style>
	<script type="text/javascript">
		/* vX JavaScript library by Antimatter15, inportb, and paul.wratt */
		var _=_?_:{}
		_.fx=_.A=function(v,n,c,f,u,y){u=0;(y=function(){u++<v&&c(u/v)!==0?setTimeout(y,n):(f?f():0)})()}
		_.pos=_.P=function(e,a){a={l:0,t:0,w:e.offsetWidth,h:e.offsetHeight};do{a.l+=e.offsetLeft;a.t+=e.offsetTop}while(e=e.offsetParent)return a}
		_.slide=function(d,e,o,f,i,q){q=_.P(e).h;_.A(f?f:15,i?i:10,function(a){a=(d?0:1)+(d?1:-1)*a;e.style.height=(a*q)+'px'},o)}
	</script>
</head>

<body>
	<div id="wrapper">
		<div id="header">
			<h3>LightBlog Installer</h3>
		</div>
		<div id="sidebar">
			<ul>
				<li class="<?php menuClass(1); ?>">Step 1: Update</li>
				<li class="<?php menuClass(2); ?>">Step 2: Finish</li>
			</ul>
			<div class="clear"></div>
		</div>
		<div id="content">
			<?php if(!isset($page) || $page == null): $disable = null; $page = null; ?>
				<h2>Updating your database</h2>
				<p>You will need to update your LightBlog site's database before you<br />
				   can continue to use your site. Click the Update button below to start<br />
				   the process. Please note that if you have a lot of content on your site<br />
				   it will take more time, so please be patient. DO NOT click the back button<br />
				   or close your browser before the update is complete.</p>		
				<form action="<?php echo basename($_SERVER['SCRIPT_FILENAME']); ?>" method="post">
					<div>
						<input type="submit" name="update" value="Update Database" onclick="setTimeout('this.disabled=true', 250);" />
					</div>		
				</form>
				<br /><span id="error"><?php if(!isset($error)){$error=null;}echo $error; ?></span>
			<?php endif; if($page == 'finish'): ?>
				<h2>You're done!</h2>
				<p>Click the Continue button to go on to your updated blog! :)</p>
				<button onclick="window.location='<?php echo baseurl(); ?>'">Continue</button>	
			<?php endif; ?>
			<div class="clear"></div>
		</div>
		<div class="clear"></div>
	</div>
</body>
</html>
