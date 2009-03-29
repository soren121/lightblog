<?php session_start(); 

/*********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	install.php
	
	©2009 soren121. All rights reserved.
	Released under the GNU General
	Public License. For all licensing
	information, please see the
	LICENSE.txt document included in this
	distribution.

*********************************************/

// Check directory permissions


// Generate a random string of specified length
function randomString($length) {
	// start with a blank password
	$password = "";
	// define possible characters
	$possible = "0123456789bcdfghjkmnpqrstvwxyz";   
	// set up a counter
	$i = 0;    
	// add random characters to $password until $length is reached
	while ($i < $length) { 
		// pick a random character from the possible ones
		$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);   
		// we don't want this character if it's already in the password
		if (!strstr($password, $char)) { 
			$password .= $char;
			$i++;
		}
	}
	// done!
	return $password;
}

// Find and return current URL
function curDirURL() {
	$site_url = explode('/', $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
	unset($site_url[count($site_url)-1]);
	$site_url = implode('/', $site_url);
	$site_url = 'http://'.$site_url.'/';
	return $site_url;
}

if(isset($_REQUEST['dbsubmit'])) {
	// Create full database path
	$dbpath = $_REQUEST['dblocation']."\\".randomString(mt_rand(9, 16)).".db"; }
	// Create database file
	fclose(fopen($dbpath, 'w')) or die("Cannot create database. Check your permissions.");
	// Open database
	$dbh = new SQLiteDatabase($dbpath);
	// Write data to database
	$sqlh = fopen("install.sql", 'r');
	$sql = fread($sqlh, filesize("install.sql"));
	fclose($sqlh);
	$dbh->queryExec($sql) or die("Cannot write to database. Check your permissions.");
	// Create config file
	fclose(fopen(dirname(__FILE__)."\config.php", 'w')) or die("Cannot create configuration file. Check your permissions.");
	// Read example file
	$exconfigfile = "config-example.php";
	$exconfig = fread(fopen($exconfigfile, 'r'), filesize($exconfigfile));
	// Create config file
	$configdata = str_replace("absolute path to database here", $dbpath, $exconfig);
	$config = fopen("config.php", 'w') or die("Cannot write to configuration file. Check your permissions.");
	fwrite($config, $configdata);
	// Close file handles
	fclose($config);
	// Unset variables
	unset($dbpath, $dbh, $sqlfile, $sql, $sqlh, $exconfig, $exconfigfile, $config, $configdata);
	// Prevent the rest of the page from loading
	die();
}

if(isset($_REQUEST['isubmit'])) {
	// Open configuration file
	require('config.php');
	// Generate salt
	$salt = substr(md5(uniqid(rand(), true)), 0, 9);
	// Set variables for easy manipulation
	$username = sqlite_escape_string($_REQUEST['iusername']);
	$password = sqlite_escape_string(md5($salt.$_REQUEST['ipassword']));
	$email = sqlite_escape_string($_REQUEST['iemail']);
	$displayname = sqlite_escape_string($_REQUEST['iname']);
	// Get the user's real IP, if possible
	if (getenv(HTTP_X_FORWARDED_FOR)) { $ip = getenv(HTTP_X_FORWARDED_FOR); } 
	else { $ip = getenv(REMOTE_ADDR); }
	// Open connection to database
	$dbh = new SQLiteDatabase( DBH );
	// Add blog title to database
	$dbh->query("INSERT INTO core VALUES('title', '".$_REQUEST['ititle']."');");
	// Add blog directory URL to database
	$dbh->query("INSERT INTO core VALUES('url', '".curDirURL()."');");
	// Add user to database
	$dbh->query("INSERT INTO users (username,password,email,displayname,role,ip,salt) VALUES('".$username."', '".$password."', '".$email."', '".$displayname."', 1, '".$ip."', '".$salt."');");
	// Unset variables
	unset($username, $password, $email, $displayname, $ip, $dbh);
	// Prevent the rest of the page from loading
	die();
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>LightBlog 0.9 Installer</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="generator" content="Geany 0.13/soren121" />
	<script type="text/javascript" src="Sources/jquery.js"></script>
	<script type="text/javascript" src="Sources/yetii.js"></script>
	<style type="text/css">
	body {
		text-align: center;
		background: #eee;
	}
	#content {
		margin: 0 auto;
		height: 95%;
		width: 580px;
		margin-top: 2.5%;
		margin-bottom: 2.5%;
		background: #fff;
		color: #777;
		font-family: Sans;
		padding: 5px;
		padding-bottom: 20px;
		position: relative;
	}
	.corner {
		position: absolute;
		width: 10px;
		height: 10px;
		background: url('admin/style/corners.gif') no-repeat;
		font-size: 0%;
	}
	.cornerBoxInner { padding: 10px; }
	.TL { top: 0; left: 0; background-position: 0 0; }
	.TR { top: 0; right: 0; background-position: -10px 0; }
	.BL { bottom: 0; left: 0; background-position: 0 -10px; }
	.BR { bottom: 0; right: 0; background-position: -10px -10px; }
	#tab1 table {
		margin-left: auto;
		margin-right: auto;
		width: 420px;
		border-color: #ccc;
		border-width: 0 0 1px 1px;
		border-style: solid;
		border-collapse: collapse;
	}
	#tab1 td {
		padding: 3px;
		border-color: #ccc;
		border-width: 1px 1px 0 0;
		border-style: solid;
		border-collapse: collapse;
	}
	</style>
</head>

<body>
	<div id="content">
		<div class="corner TL"></div>
		<div class="corner TR"></div>
		<div class="corner BL"></div>
		<div class="corner BR"></div>
		<div class="cornerBoxInner" id="tab-container-1">
			<ul id="tab-container-1-nav" style="display: none;">
				<li><a href="#tab1"></a></li>
				<li><a href="#tab2"></a></li>
				<li><a href="#tab3"></a></li>
				<li><a href="#tab4"></a></li>
			</ul>
			<h2>LightBlog Installer</h2>
			<div class="tab" id="tab1">
				<h4>Welcome to the LightBlog installer.<br />First, we must check to see if your server meets the requirements to run LightBlog.</h4>
				<table class="vercheck">
				<tr><td>PHP Version</td>
					<td><?php echo phpversion(); ?></td>
					<?php if(floatval(phpversion()) >= "5.1") { echo '<td style="background:#6CCC0D;">OK</td>'; } else { echo '<td style="background:#CC2626;">Unsatisfactory</td>';$error1 = true; } ?>
				</tr>
				<tr><td>SQLite</td>
					<td><?php if(extension_loaded('sqlite') == false){ echo 'Disabled';$error2 = true; } else {echo sqlite_libversion(); }?></td>
					<?php if(floatval(sqlite_libversion()) >= "2.8"){ echo '<td style="background:#6CCC0D;">OK</td>'; } else {echo '<td style="background:#CC2626;">Unsatisfactory</td>'; }?>
				</tr>
				<tr><td>cURL</td>
					<td><?php if(extension_loaded('curl') == false){ echo 'Disabled';$error3 = true; } else {echo 'Enabled'; }?></td>
					<?php if(extension_loaded('curl') == false){ echo '<td style="background:#CC2626;">Unsatisfactory</td>'; } else {echo '<td style="background:#6CCC0D;">OK</td>'; }?>
				</tr>
				<?php $GDArray = gd_info(); $gdver = ereg_replace('[[:alpha:][:space:]()]+', '', $GDArray['GD Version']); ?>
				<tr><td>GD</td>
					<td><?php if(extension_loaded('gd') == false){ echo 'Disabled';$error4 = true; } else { echo $gdver; }?></td>
					<?php if(floatval($gdver) >= "2.0") { echo '<td style="background:#6CCC0D;">OK</td>'; } else { echo '<td style="background:#CC2626;">Unsatisfactory</td>'; }?>
				</tr>
				</table>
				<?php if($error1 or $error2 or $error3 or $error4 == true): ?>
					<h4 style="color:red;">Your server does not meet the minimum requirements. Please rectify the issues listed above and try again.</h4>
					<button disabled="disabled" class="continue" onclick="tabber1.show(2); return false;">Continue</button>	
				<?php else: ?>
					<br />
					<button class="continue" onclick="tabber1.show(2); return false;">Continue</button>
				<?php endif; ?>
			</div>
			<div class="tab" id="tab2">
			    <script type="text/javascript" charset="utf-8">
				$(function() {
					$('#form-tab2').submit(function() {
						var inputs = [];
						$(':input', this).each(function() {
							inputs.push(this.name + '=' + escape(this.value));
						})
						$('#form-tab2').empty().html('<' + 'img src="admin/style/loading.gif" alt="" />');
						jQuery.ajax({
							data: inputs.join('&'),
							url: this.getAttribute('action'),
							timeout: 2000,
							error: function() {
								$('#form-tab2').empty(); 
								console.log("Failed to submit");
								alert("Failed to submit.");
							},
							success: function(r) {
								$('#form-tab2').empty(); 
								tabber1.show(3); return false;
							}
						})
						return false;
					})
				})
				</script>
				<h4>Choose a location to place your SQLite database in.</h4>
				<form action="<?php echo basename(__FILE__); ?>" method="get" id="form-tab2">
					<p><input type="text" name="dblocation" value="<?php echo dirname(__FILE__); ?>" style="width:400px;" /></p>
					<p><input type="submit" name="dbsubmit" value="Create Database" /></p>
					<p class="loading"></p>
				</form>
			</div>
			<div class="tab" id="tab3">
			    <script type="text/javascript" charset="utf-8">
				$(function() {
					$('#form-tab3').submit(function() {
						var inputs = [];
						$(':input', this).each(function() {
							inputs.push(this.name + '=' + escape(this.value));
						})
						$('#form-tab3').empty().html('<' + 'img src="admin/style/loading.gif" alt="" />');
						jQuery.ajax({
							data: inputs.join('&'),
							url: this.getAttribute('action'),
							timeout: 2000,
							error: function() {
								$('#form-tab3').empty(); 
								console.log("Failed to submit.");
								alert("Failed to submit.");
							},
							success: function(r) {
								$('#form-tab2').empty(); 
								tabber1.show(4); return false;
							}
						})
						return false;
					})
				})
				</script>
				<h4>Please fill in all fields to complete setup.</h4>
				<form action="<?php echo basename(__FILE__); ?>" method="get" id="form-tab3">
					<table class="iform" style="border:0;margin-left:auto;margin-right:auto;">
						<tr>
							<td>Blog Title:</td>
							<td><p><input type="text" name="ititle" style="width: 200px;" /></p></td>
						</tr>
						<tr>
							<td>Admin Username:</td>
							<td><p><input type="text" name="iusername" style="width: 200px;" /></p></td>
						</tr>
						<tr>
							<td>Admin Password:</td>
							<td><p><input type="password" name="ipassword" style="width: 200px;" /></p></td>
						</tr>
						<tr>
							<td>Admin Email:</td>
							<td><p><input type="text" name="iemail" style="width: 200px;" /></p></td>
						</tr>
						<tr>
							<td>Display Name:</td>
							<td><p><input type="text" name="iname" style="width: 200px;" /></p></td>
						</tr>
					</table>
					<p><input type="submit" name="isubmit" value="Complete Installation" /></p>
				</form>
			</div>
			<div class="tab" id="tab4">
			<h4>Congratulations! LightBlog is installed.</h4>
			<a href="index.php?install=true">Click here to go to the front page.</a>
			</div>
		</div>
	</div>
	<script type="text/javascript">var tabber1 = new Yetii({ id: 'tab-container-1' });</script>
</body>
</html>