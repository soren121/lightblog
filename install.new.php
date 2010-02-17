<?php

/*********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	install.php
	
	©2009-2010 The LightBlog Team. All 
	rights reserved. Released under the 
	GNU General Public License 3. For 
	all licensing information, please 
	see the LICENSE.txt document 
	included in this distribution.

*********************************************/

// Shutdown Magic Quotes automatically
// Highly inefficient, but there isn't much we can do about it
// PHP 6 can't come fast enough (Magic Quotes is gone in PHP6)
if(get_magic_quotes_gpc()) {
	function stripslashes_gpc(&$value) {
		$value = stripslashes($value);
	}
	array_walk_recursive($_GET, 'stripslashes_gpc');
    array_walk_recursive($_POST, 'stripslashes_gpc');
    array_walk_recursive($_COOKIE, 'stripslashes_gpc');
    array_walk_recursive($_REQUEST, 'stripslashes_gpc');
}

// mt_rand replacement
if(!function_exists(mt_rand)) {
	function mt_rand($min, $max) {
		return rand($min, $max);
	}
}

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

// Generate a random string of specified length
function randomString($length) {
	// start with a blank string
	$string = "";
	// define possible characters
	$possible = "0123456789bcdfghjkmnpqrstvwxyz_.-";   
	// set up a counter
	$i = 0;    
	// add random characters to $password until $length is reached
	while($i < $length) { 
		// pick a random character from the possible ones
		$char = substr($possible, mt_rand(0, strlen($possible) - 1));   
		// we don't want this character if it's already in the string
		if(!strstr($string, $char)) { 
			$string .= $char;
			$i++;
		}
	}
	// done!
	return $string;
}

// Will process after Step 1
if(isset($_POST['reqmet'])) {
	// Set new installer page
	$page = 'dbsetup';
	menuClass(2, 1);
}

function dbsetup() {
	// Create database path
	$dbpath = realpath($_POST['dbpath'].'/'.randomString(rand(9, 16)).'.db');
	if(!is_dir(realpath($_POST['dbpath']))) {
		return 'Database path is not a folder.';
	}
	else {
		if(!is_writable(realpath($_POST['dbpath']))) {
			return 'Database path is not writable. Please chmod it to 666 and try again.';
		}
	}
	// Open, read, and close SQL file
	if(is_readable('install.sql')) {
		$sqlh = fopen('install.sql', 'r');
		$sql = fread($sqlh, filesize('install.sql'));
		fclose($sqlh);
	}
	else {
		// Attempt to make it readable
		if(!chmod('install.sql', 0644)) {
			return 'Failed to open install.sql. Please chmod it to 644 and try again.';
		}
	}
	// Create, write to and close database
	$dbh = new SQLiteDatabase($dbpath);
	$dbh->queryExec($sql);
	// Open, read, and close example config file
	$excfgh = fopen('config-example.php', 'r');
	$excfg = fread($excfgh, filesize('config-example.php'));
	fclose($excfgh);
	// Prepare config file data
	$excfg = str_replace("absolute path to database here", $dbpath, $excfg);
	// Create, write to, and close config file
	$cfgh = fopen('config.php', 'w');
	fwrite($cfgh, $excfg);
	fclose($cfgh);
}

// Will process after Step 2
if(isset($_POST['dbsetup'])) {
	$return = dbsetup();
	if(!$return == null) {
		$error = $return;
	}
	else {
		// Set new installer page
		$page = "blogsetup";
		menuClass(3, 1);
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>LightBlog Installer</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<style type="text/css">
		body {
			background: #EDEDED;
			font-family: "Trebuchet MS", "Verdana", sans;
		}
		#wrapper {
			width: 870px;
			min-height: 500px;
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
			min-height: 465px;
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
			min-height: 455px;
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
		form {
			margin: 20px 0 0 25px;
		}
		label {
			font-size: .9em;
			font-weight: bold;
		}
		input[type=submit], button {
			padding: 3px 10px 3px 10px;
		}
		div.clear {
			clear: both;
		}
	</style>
</head>

<body>
	<div id="wrapper">
		<div id="header">
			<h3>LightBlog Installer</h3>
		</div>
		<div id="sidebar">
			<ul>
				<li class="<?php menuClass(1); ?>">Step 1: Start</li>
				<li class="<?php menuClass(2); ?>">Step 2: Database Setup</li>
				<li class="<?php menuClass(3); ?>">Step 3: Blog Setup</li>
				<li class="<?php menuClass(4); ?>">Step 4: Finish</li>
			</ul>
			<div class="clear"></div>
		</div>
		<div id="content">
			<?php if(!isset($page) || $page == null): ?>
				<h2>Welcome to the LightBlog installer!</h2>
				<p>Thanks for choosing LightBlog! Here, we're going to test your<br />server to make sure it can support LightBlog.</p>
				<table>
					<tr>
						<th>Component</th>
						<th>Version</th>
						<th>Status</th>
					</tr>
					<tr>
						<td>PHP</td>
						<td><?php echo phpversion(); ?></td>
						<?php if(floatval(phpversion()) >= "5.1"): ?>
							<td>OK</td>
						<?php else: ?>
							<td>Too old (5+ required)</td>
							<?php $disable = 'disabled="disabled"'; ?>
						<?php endif; ?>
					</tr>
					<tr>
						<td>SQLite</td>
						<?php if(extension_loaded('sqlite')): ?>
							<td><?php echo sqlite_libversion(); ?></td>
							<?php if(floatval(sqlite_libversion()) >= "2.8"): ?>
								<td>OK</td>
							<?php else: ?>
								<td>Too old (2.8+ required)</td>
								<?php $disable = 'disabled="disabled"'; ?>
							<?php endif; ?>
						<?php else: ?>
							<td>&nbsp;</td>
							<td>Disabled (need 2.8+ enabled)</td>
							<?php $disable = 'disabled="disabled"'; ?>
						<?php endif; ?>
					</tr>
				</table>
				<form action="<?php echo basename($_SERVER['SCRIPT_FILENAME']); ?>" method="post">
					<div>
						<input type="submit" name="reqmet" value="Continue" onclick="setTimeout('this.disabled=true', 250);" <?php echo $disable; ?> />
					</div>			
				</form>
			<?php endif; if($page == 'dbsetup'): ?>
				<h2>Database setup</h2>
				<p>Now we're going to setup your SQLite database. This<br />
				database will hold all of your blog's information, including<br />
				password hashes and other sensitive data. We recommend you<br />
				place the database outside of your web root if possible, or<br />
				place it in a non-public-readable folder.</p>				
				<form action="<?php echo basename($_SERVER['SCRIPT_FILENAME']); ?>" method="post">
					<label for="dbpath">Database Path</label><br />
					<div>
						<input type="text" name="dbpath" id="dbpath" value="<?php echo dirname($_SERVER['SCRIPT_FILENAME']); ?>" style="width:350px;" />
						<input type="submit" name="dbsetup" value="Continue" onclick="setTimeout('this.disabled=true', 250);" />
					</div>		
				</form>
			<?php endif; ?>
			<div class="clear"></div>
		</div>
		<div class="clear"></div>
	</div>
</body>
</html>
