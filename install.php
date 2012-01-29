<?php

/*********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	install.php
	
	©2008-2012 The LightBlog Team. All 
	rights reserved. Released under the 
	GNU General Public License 3. For 
	all licensing information, please 
	see the LICENSE.txt document 
	included in this distribution.

*********************************************/

// Get some extra functions
require(dirname(__FILE__).'/Sources/FunctionReplacements.php');
require(dirname(__FILE__).'/Sources/StringFunctions.php');

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

// Operates the side menu selectors
// First parameter specifies page
// Second parameter defines if we're changing the page or just reading it
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

// Adds trailing slash if needed
function endslash($path) {
	$last_char = substr($path, strlen($path) - 1, 1);
	if($last_char != DIRECTORY_SEPARATOR) {
		$path .= DIRECTORY_SEPARATOR;
	}
	return $path;
}

// Gets directory URL
function baseurl() {
	$site_url = explode('/', $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
	unset($site_url[count($site_url)-1]);
	$site_url = implode('/', $site_url);
	$site_url = 'http://'.$site_url.'/';
	return $site_url;
}

// Will process after Step 1
if(isset($_POST['reqmet'])) {
	// Set new installer page
	$page = 'dbsetup';
	menuClass(2, 1);
}

// Processing for step 2
function dbsetup() {
	if($_POST['dbpath'] == null || $_POST['dbpath'] == '') {
		return 'No database path given.';
	}
	// Create database path
	$dbpath = endslash($_POST['dbpath']).randomString(rand(9, 16)).'.db';
	if(!is_dir($_POST['dbpath'])) {
		if(!file_exists($_POST['dbpath'])) {
			if(!@mkdir($_POST['dbpath'], 0760, true)) {
				return 'Unable to create directory. Please create it manually, chmod it to 760, and try again.';
			}
		}
	}
	else {
		if(!is_writable($_POST['dbpath'])) {
			return 'Database path is not writable. Please chmod it to 760 and try again.';
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
	// Create, write to, and close database
	if(!$dbh = new SQLiteDatabase($dbpath)) {
		return 'Failed to create the database. Please chmod its directory to 760 and try again.';
	}
	if(!$dbh->queryExec($sql, $errormsg)) {
		return 'Failed to write to the database because: '.$errormsg.'.';
	}
	unset($dbh);
	// Open, read, and close example config file
	if(is_readable('config-example.php')) {
		$excfgh = fopen('config-example.php', 'r');
		$excfg = fread($excfgh, filesize('config-example.php'));
		fclose($excfgh);
	}
	else {
		if(!chmod('config-example.php', 0644)) {
			return 'Failed to open install.sql. Please chmod it to 644 and try again.';
		}
		else {
			$excfgh = fopen('config-example.php', 'r');
			$excfg = fread($excfgh, filesize('config-example.php'));
			fclose($excfgh);
		}
	}
	// Prepare config file data
	$excfg = str_replace("absolute path to database here", $dbpath, $excfg);
	$cfgh = fopen('config.php', 'w');
	fwrite($cfgh, $excfg);
	fclose($cfgh);
}

// Will process after Step 2
if(isset($_POST['dbsetup'])) {
	$return = dbsetup();
	// Check for errors
	if(!$return == null) {
		$error = $return;
		$page = "dbsetup";
	}
	else {
		// Set new installer page
		$page = "bsetup";
		menuClass(3, 1);
	}
}

// Processing for step 3
function bsetup() {
	// Require config file
	// We need it to open the database
	require('config.php');
	// Set (and for some, clean) variables
	$username = sqlite_escape_string($_POST['bsusername']);
	$password = $_POST['bspassword'];
	$vpassword = $_POST['bsvpassword'];
	$email = sqlite_escape_string($_POST['bsemail']);
	$dname = sqlite_escape_string($_POST['bsdname']);
	$title = sqlite_escape_string($_POST['bstitle']);
	$url = sqlite_escape_string($_POST['bsurl']);
	// Correct IPv6 address when installing on localhost
	$ip = !strstr($_SERVER['REMOTE_ADDR'], "::1") ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
	// Match passwords
	if($password !== $vpassword) {
		return 'Passwords don\'t match. Please try again.';
	}
	// Open database
	$dbh = new SQLiteDatabase( DBH );
	// Generate password salt
	$salt = substr(md5(uniqid(mt_rand(), true)), 0, 9);
	// Clean remaining variables
	$password = sha1($salt.$password);
	// Add user to database
	$dbh->query("INSERT INTO users (username,password,email,displayname,role,ip,salt) VALUES('$username', '$password', '$email', '$dname', 3, '$ip', '$salt');");
	// Add blog title to database
	$dbh->query("INSERT INTO core VALUES('title', '$title');");
	// Add blog URL to database
	$dbh->query("INSERT INTO core VALUES('url', '$url');");
	// Shut off database connection
	unset($dbh);
}

// Will process after Step 3
if(isset($_POST['bsetup'])) {
	$return = bsetup();
	if(!$return == null) {
		$error = $return;
		$page = "bsetup";
	}
	else {
		// Set new installer page
		$page = "finish";
		menuClass(4, 1);
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
			list-style-type: disc;
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
		#content h3 {
			color: #222;
			font-size: 1.2em;
			margin: 20px 0 0 25px;
		}
		#content p {
			margin: 10px 0 0 25px;
			font-size: .98em;
			line-height: 1.5em;
		}
		#content table {
			margin: 5px 0 0 25px;
			border-right: 1px solid #CBE1F2;
			border-bottom: 1px solid #CBE1F2;
			border-collapse: collapse;
		}
		#content td, #content th {
			padding: 3px 20px 3px 7px;
			border-top: 1px solid #CBE1F2;
			border-left: 1px solid #CBE1F2;
			text-align: left;
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
		div#bsleft {
			float: left;
			width: 200px;
			margin-right: 60px;
		}
		div#bsright {
			float: left;
			width: 200px;
		}
		label {
			font-size: .9em;
			font-weight: bold;
		}
		label[for="bsdname"] {
			cursor: help;
		}
		input[type="text"], input[type="password"] {
			margin-bottom: 10px;
		}
		input[type="submit"], button {
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
			<?php if(!isset($page) || $page == null): $disable = null; $page = null; ?>
				<h2>Welcome to the LightBlog installer!</h2>
				<p>Thanks for choosing LightBlog! Before we start, the installer<br />needs to be sure that your server can properly run LightBlog.</p>

				<h3>PHP Components</h3>
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
							<td>Too old (5.1+ required)</td>
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
					<tr>
						<td>cURL</td>
						<?php if(extension_loaded('curl')): ?>
							<td><?php $v = curl_version(); echo $v['version']; ?></td>
							<td>OK</td>
						<?php else: ?>
							<td>&nbsp;</td>
							<td>Disabled</td>
						<?php endif; ?>
					</tr>
					<tr>
						<td>fsockopen</td>
						<td>n/a</td>
						<?php if(is_resource(@fsockopen("www.google.com"))): ?>
							<td>OK</td>
						<?php else: ?>
							<td>Disabled</td>
						<?php endif; ?>
					</tr>
				</table>
				
				<h3>File Permissions</h3>
				<table>
					<tr>
						<th>File</th>
						<th>Permission</th>
						<th>Status</th>
					</tr>
					<tr>
						<td>config.php</td>
						<?php if(file_exists("./config.php")): ?>
							<?php if(is_writable("./config.php")): ?>
								<td>Writable</td>
								<td>OK</td>
							<?php else:
								$disable = 'disabled="disabled"';
							?>
								<td>Not Writable</td>
								<td>Please CHMOD to 775</td>
							<?php endif;
							else:
								// Ok, it's missing. If they were using a prewritten conf, it should already be there.
								if(!@file_put_contents('config.php', '<?php //AUTOGENERATED DUMMY ?>')): ?>
									<td>Missing</td>
									<td>Fail - Could not autocreate config</td>
								<?php $disable = 'disabled="disabled"'; 
								else: ?>
									<td>Created</td>
									<td>OK</td>
						  <?php endif;
							endif; ?>		
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
				in a non-public-readable folder. If the path does not exist,<br />
				the installer will try to create it.</p>		
				<form action="<?php echo basename($_SERVER['SCRIPT_FILENAME']); ?>" method="post">
					<label for="dbpath">Database Path</label><br />
					<div>
						<input type="text" name="dbpath" id="dbpath" value="<?php echo dirname(__FILE__); ?>" style="width:350px;" />
						<input type="submit" name="dbsetup" value="Continue" onclick="setTimeout('this.disabled=true', 250);" />
					</div>		
				</form>
				<span id="error"><?php if(!isset($error)){$error=null;}echo $error; ?></span>		
			<?php endif; if($page == 'bsetup'): ?>
				<script type="text/javascript">
					// Password strength meter v2.0
					// Matthew R. Miller - 2007
					// www.codeandcoffee.com
					// Based off of code from:
					// http://www.intelligent-web.co.uk
					// http://www.geekwisdom.com/dyn/passwdmeter
					// Modified by The LightBlog Team
					var m_strUpperCase="ABCDEFGHIJKLMNOPQRSTUVWXYZ";
					var m_strLowerCase="abcdefghijklmnopqrstuvwxyz";
					var m_strNumber="0123456789";
					var m_strCharacters="!@#$%^&*?_~.,+=";
					function checkPassword(strPassword){
						var nScore=0;
						if(strPassword.length<5){nScore+=5;}
						else if(strPassword.length>4&&strPassword.length<8){nScore+=10;}
						else if(strPassword.length>7){nScore+=25;}
						var nUpperCount=countContain(strPassword,m_strUpperCase);
						var nLowerCount=countContain(strPassword,m_strLowerCase);
						var nLowerUpperCount=nUpperCount+nLowerCount;
						if(nUpperCount==0&&nLowerCount!=0){nScore+=10;}
						else if(nUpperCount!=0&&nLowerCount!=0){nScore+=20;}
						var nNumberCount=countContain(strPassword,m_strNumber);
						if(nNumberCount==1){nScore+=10;}
						if(nNumberCount>=3){nScore+=20;}
						var nCharacterCount=countContain(strPassword,m_strCharacters);
						if(nCharacterCount==1){nScore+=10;}
						if(nCharacterCount>1){nScore+=25;}
						if(nNumberCount!=0&&nLowerUpperCount!=0){nScore+=2;}
						if(nNumberCount!=0&&nLowerUpperCount!=0&&nCharacterCount!=0){nScore+=3;}
						if(nNumberCount!=0&&nUpperCount!=0&&nLowerCount!=0&&nCharacterCount!=0){nScore+=5;}
						return nScore;}
					function runPassword(strPassword,strFieldID){
						var nScore=checkPassword(strPassword);
						var ctlText=document.getElementById(strFieldID+"_text");
						if(!ctlText){return;}
						if(nScore>=90){var strText="Very Secure";var strColor="#0ca908";}
						else if(nScore>=80){var strText="Secure";vstrColor="#7ff67c";}
						else if(nScore>=70){var strText="Very Strong";var strColor="#1740ef";}
						else if(nScore>=60){var strText="Strong";var strColor="#5a74e3";}
						else if(nScore>=50){var strText="Average";var strColor="#e3cb00";}
						else if(nScore>=25){var strText="Weak";var strColor="#e7d61a";}
						else{var strText="Very Weak";var strColor="#e71a1a";}
						ctlText.innerHTML="<span style='color: "+strColor+";'>"+strText+"</span>";}
					function countContain(strPassword,strCheck){
						var nCount=0;
						for(i=0;i<strPassword.length;i++){
							if(strCheck.indexOf(strPassword.charAt(i))>-1){nCount++;}}
						return nCount;}
					function matchPasswords(){
						var field1=document.getElementById('bspassword');
						var field2=document.getElementById('bsvpassword');
						var mpspan=document.getElementById('matchpasswords');
						if(field1.value==field2.value){
							mpspan.innerHTML='<span style="color:#0ca908;">OK</span>';}
						else{
							mpspan.innerHTML='<span style="color:#e71a1a;">Not a match</span>';}}
				</script>
				<h2>Blog setup</h2>
				<p>Before we show you your new blog, we need to setup an<br />
				administrator account, so that you can access the admin panel.<br />
				All of the fields below need to be filled.</p>
				<form action="<?php echo basename($_SERVER['SCRIPT_FILENAME']); ?>" method="post">
					<div id="bsleft">
						<label for="bsusername">Username</label><br />
						<input type="text" name="bsusername" id="bsusername" style="width:200px;" /><br />
						<label for="bspassword">Password</label><span id="bspassword_text" style="font-size:.75em;margin-left:10px;"></span><br />
						<input type="password" name="bspassword" id="bspassword" style="width:200px;" onkeyup="runPassword(this.value, 'bspassword');matchPasswords();" />
						<label for="bsvpassword">Confirm Password</label><span id="matchpasswords" style="font-size:.75em;margin-left:10px;"></span><br />
						<input type="password" name="bsvpassword" id="bsvpassword" style="width:200px;" onkeyup="matchPasswords();" />
						<label for="bsemail">Email</label><br />
						<input type="text" name="bsemail" id="bsemail" style="width:200px;" /><br />
						<label for="bsdname" title="This name will be used to identify you in posts and in areas around LightBlog.">Display Name</label><br />
						<input type="text" name="bsdname" id="bsdname" style="width:200px;" /><br /><br />
					</div>
					<div id="bsright">
						<label for="bstitle">Blog Title</label><br />
						<input type="text" name="bstitle" id="bstitle" style="width:200px;" /><br />
						<label for="bsurl">Blog URL</label><br />
						<input type="text" name="bsurl" id="bsurl" value="<?php echo baseurl(); ?>" style="width:200px;" />
					</div>
					<div style="width:100%;clear:both;">
						<input type="submit" name="bsetup" value="Continue" onclick="setTimeout('this.disabled=true', 250);" />
					</div>
				</form>
				<span id="error"><?php if(!isset($error)){$error=null;}echo $error; ?></span>
			<?php endif; if($page == 'finish'): ?>
				<h2>You're done!</h2>
				<p>Click the Finish button to see your new blog! :)</p>
				<button onclick="window.location='<?php echo baseurl(); ?>?install=true'">Finish</button>
			<?php endif; ?>
			<div class="clear"></div>
		</div>
		<div class="clear"></div>
	</div>
</body>
</html>
