 <?php 

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
		$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);   
		// we don't want this character if it's already in the string
		if(!strstr($string, $char)) { 
			$string .= $char;
			$i++;
		}
	}
	// done!
	return $string;
}

// Find and return current URL
function curDirURL() {
	$site_url = explode('/', $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
	unset($site_url[count($site_url)-1]);
	$site_url = implode('/', $site_url);
	$site_url = 'http://'.$site_url.'/';
	return $site_url;
}

// Function to undo Magic Quotes in strings
function undoMagicString($str) {
	if(function_exists('magic_quotes_gpc') && magic_quotes_gpc() == 1) {
		return stripslashes($str);
	}
	else {
		return $str;
	}
}

// Function to get a real IP
function get_ip() {
	// Look for an IP address
	if(!empty($_SERVER['REMOTE_ADDR'])) {
		$client_ip = $_SERVER['REMOTE_ADDR'];
	}
	// Look for proxies
	if($_SERVER['HTTP_CLIENT_IP']) {
		$proxy_ip = $_SERVER['HTTP_CLIENT_IP'];
	}
	elseif($_SERVER['HTTP_X_FORWARDED_FOR']) {
		$proxy_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	// Look for a real IP underneath a proxy
	if($proxy_ip) {
		if(preg_match("/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $proxy_ip, $ip_list)) {
				$private_ip = array(
					'/^0\./',
					'/^127\.0\.0\.1/',
					'/^192\.168\..*/',
					'/^172\.16\..*/',
					'/^10.\.*/',
					'/^224.\.*/',
					'/^240.\.*/');
				// A generic private IP is useless to us, so don't use those
				$client_ip = preg_replace($private_ip, $client_ip, $ip_list[1]);
		}
	}
	// Fix a strange localhost IP problem
	if($client_ip == '::1') {
		$client_ip = '127.0.0.1';
	}
	// Return what we think the IP is
	return $client_ip;
}

if(isset($_POST['dbsubmit'])) {
	// Create full database path
	$dbpath = undoMagicString($_POST['dblocation'])."/".randomString(mt_rand(9, 16)).".db";
	// Create database file
	fclose(fopen($dbpath, 'w')) or die("Cannot create database. Check your permissions.");
	// Check database permissions
	if(fileperms($dbpath) < 0755) { chmod($dbpath, 0755) or die('Couldn\'t change permissions.' ); }
	// Open database
	$dbh = new SQLiteDatabase($dbpath);
	// Write data to database
	$sqlh = fopen("install.sql", 'r');
	$sql = fread($sqlh, filesize("install.sql"));
	fclose($sqlh);
	$dbh->queryExec($sql) or die("Cannot write to database. Check your permissions.");
	// Create config file
	fclose(fopen(dirname(__FILE__)."/"."config.php", 'w')) or die("Cannot create configuration file. Check your permissions.");
	// Read example file
	$exconfigfile = "config-example.php";
	$exconfig = fread(fopen($exconfigfile, 'r'), filesize($exconfigfile));
	// Create config file
	$configdata = str_replace("absolute path to database here", $dbpath, $exconfig);
	$config = fopen("config.php", 'w') or die("Cannot write to configuration file. Check your permissions.");
	// Write config file
	fwrite($config, $configdata);
	// Close file handles
	fclose($config);
	// Unset variables
	unset($dbpath, $dbh, $sqlfile, $sql, $sqlh, $exconfig, $exconfigfile, $config, $configdata);
	// Respond
	echo "OK";
	// Prevent the rest of the page from loading
	die();
}

if(isset($_POST['isubmit'])) {
	// Open configuration file
	require('config.php');
	// Generate salt
	$salt = substr(md5(uniqid(rand(), true)), 0, 9);
	// Set variables for easy manipulation
	$username = sqlite_escape_string($_POST['iusername']);
	$password = sqlite_escape_string(sha1($salt.$_POST['ipassword']));
	$email = sqlite_escape_string($_POST['iemail']);
	$displayname = sqlite_escape_string($_POST['iname']);
	// Open connection to database
	$dbh = new SQLiteDatabase( DBH );
	// Add blog title to database
	$dbh->query("INSERT INTO core VALUES('title', '".sqlite_escape_string($_POST['ititle'])."');") or die("Cannot write to database. Check your permissions.");
	// Add blog directory URL to database
	$dbh->query("INSERT INTO core VALUES('url', '".curDirURL()."');") or die("Cannot write to database. Check your permissions.");
	// Add user to database
	$dbh->query("INSERT INTO users (username,password,email,displayname,role,ip,salt) VALUES('$username','$password','$email','$displayname',3,'".get_ip()."','$salt');") or die("Cannot write to database. Check your permissions.");
	// Unset variables
	unset($username, $password, $email, $displayname, $ip, $dbh);
	// Respond
	echo "OK";
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
	<script type="text/javascript" src="Sources/jQuery.js"></script>
	<script type="text/javascript" src="Sources/jQuery.Corners.js"></script>
	<script type="text/javascript" src="Sources/jQuery.MiniPages.js"></script>
	<script type="text/javascript">	
		$(document).ready(function(){ $('.rounded').corner(); $("#content").minipages(); });
	</script>
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
	<!--[if lt IE 7]>  <div style='border: 1px solid #F7941D; background: #FEEFDA; text-align: center; clear: both; height: 75px; position: relative;'>    <div style='position: absolute; right: 3px; top: 3px; font-family: courier new; font-weight: bold;'><a href='#' onclick='javascript:this.parentNode.parentNode.style.display="none"; return false;'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-cornerx.jpg' style='border: none;' alt='Close this notice'/></a></div>    <div style='width: 640px; margin: 0 auto; text-align: left; padding: 0; overflow: hidden; color: black;'>      <div style='width: 75px; float: left;'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-warning.jpg' alt='Warning!'/></div>      <div style='width: 275px; float: left; font-family: Arial, sans-serif;'>        <div style='font-size: 14px; font-weight: bold; margin-top: 12px;'>You are using an outdated browser</div>        <div style='font-size: 12px; margin-top: 6px; line-height: 12px;'>For a better experience using this site, please upgrade to a modern web browser.</div>      </div>      <div style='width: 75px; float: left;'><a href='http://www.firefox.com' target='_blank'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-firefox.jpg' style='border: none;' alt='Get Firefox 3.5'/></a></div>      <div style='width: 75px; float: left;'><a href='http://www.browserforthebetter.com/download.html' target='_blank'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-ie8.jpg' style='border: none;' alt='Get Internet Explorer 8'/></a></div>      <div style='width: 73px; float: left;'><a href='http://www.apple.com/safari/download/' target='_blank'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-safari.jpg' style='border: none;' alt='Get Safari 4'/></a></div>      <div style='float: left;'><a href='http://www.google.com/chrome' target='_blank'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-chrome.jpg' style='border: none;' alt='Get Google Chrome'/></a></div>    </div>  </div>  <![endif]-->
</head>

<body>
	<div class="rounded" id="content">
		<h2>LightBlog Installer</h2>
		<div class="tab" id="tab1">
			<?php $error1=''; $error2=''; ?>
			<h4>Welcome to the LightBlog installer.<br />First, we must check to see if your server meets the requirements to run LightBlog.</h4>
			<table class="vercheck">
				<tr><td>PHP Version</td>
					<td><?php echo phpversion(); ?></td>
					<?php if(floatval(phpversion()) >= "5.1") { echo '<td style="background:#6CCC0D;color:#fff;">OK</td>'; } else { echo '<td style="background:#CC2626;color:#fff;">Unsatisfactory</td>';$error1 = true; } ?>
				</tr>
				<tr><td>SQLite</td>
					<td><?php if(extension_loaded('sqlite') == false){ echo 'Disabled';$error2 = true; } else {echo sqlite_libversion(); }?></td>
					<?php if(floatval(sqlite_libversion()) >= "2.8"){ echo '<td style="background:#6CCC0D;color:#fff;">OK</td>'; } else {echo '<td style="background:#CC2626;color:#fff;">Unsatisfactory</td>'; }?>
				</tr>
			</table>
			<?php if($error1 or $error2 == true): ?>
				<h4 style="color:red;">Your server does not meet the minimum requirements. Please rectify the issues listed above and try again.</h4>
				<button disabled="disabled" class="continue" onclick="tabber1.show(2); return false;">Continue</button>	
			<?php else: ?>
				<br />
				<button class="continue" onclick="jQuery().minipageShow(2); return false;">Continue</button>
			<?php endif; ?>
		</div>
		<div class="tab" id="tab2">
			<script type="text/javascript">
			$(function() {
			$('#form-tab2').submit(function() {
				var inputs = [];
				$(':input', this).each(function() {
					inputs.push(this.name + '=' + escape(this.value));
				})
				$('#form-tab2').empty().html('<' + 'img src="admin/style/loading.gif" alt="" />');
				jQuery.ajax({
					data: inputs.join('&'),
					type: "POST",
					url: this.getAttribute('action'),
					timeout: 2000,
					error: function() {
						$('#form-tab2').empty(); 
						console.log("Failed to submit");
						alert("Failed to submit.");
					},
					success: function(r) {
							alert(r); 
							jQuery().minipageShow(3); return false;
					}
				})
				return false;
			})
			});
			</script>
			<h4>Choose a location to place your SQLite database in.</h4>
			<p>Be sure that the directory you are placing in the database in has the correct permissions.<br />
			If you are unsure if it's correct, <strong>chmod the directory to 755 or higher</strong>, or rwxr-xr-x.</p>
			<form action="<?php echo basename(__FILE__); ?>" method="get" id="form-tab2">
				<p><input type="text" class="dbl" name="dblocation" value="<?php echo dirname(__FILE__); ?>" style="width:400px;" /></p>
				<p><input type="submit" name="dbsubmit" value="Create Database" /></p>
			</form>
		</div>
		<div class="tab" id="tab3">
			<script type="text/javascript">
			$(function() {
			$('#form-tab3').submit(function() {
				var inputs = [];
				$(':input', this).each(function() {
					inputs.push(this.name + '=' + escape(this.value));
				})
				$('#form-tab3').empty().html('<' + 'img src="admin/style/loading.gif" alt="" />');
				jQuery.ajax({
					data: inputs.join('&'),
					type: "POST",
					url: this.getAttribute('action'),
					timeout: 2000,
					error: function() {
						$('#form-tab3').empty(); 
						console.log("Failed to submit.");
						alert("Failed to submit.");
					},
					success: function(r) {
							alert(r);
							jQuery().minipageShow(4); return false;
					}
				})
				return false;
			})
			});
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
</body>
</html>