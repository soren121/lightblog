<?php session_start();define("Light", true);
if(isset($_POST['step0'])) { 
	$step0 = "0"; $step1 = "1"; 
}
if(isset($_POST['step1'])) { 
	$dbrand = mt_rand(1000000,10000000);
	$dbstr = "admin/".$dbrand.".db";
	$cdb = fopen($dbstr, 'w') or die("Unable to create database."); 
	fclose($cdb);
	$dbhle = sqlite_open(dirname(__FILE__)."/".$dbstr) or die("Could not open database");
	sqlite_query($dbhle, "CREATE TABLE coreinfo(variable text NOT NULL,value text NOT NULL)");
	sqlite_query($dbhle, "CREATE TABLE comments(id integer primary key,post_id int(11) NOT NULL default '0',username text NOT NULL,email text NOT NULL,website text NOT NULL,text text NOT NULL)");
	sqlite_query($dbhle, "CREATE TABLE pages(id INTEGER NOT NULL PRIMARY KEY DEFAULT '0',title TEXT NOT NULL,page TEXT NOT NULL)");
	sqlite_query($dbhle, "CREATE TABLE posts(id INTEGER NOT NULL PRIMARY KEY DEFAULT '0',title TEXT NOT NULL,post TEXT NOT NULL,date TEXT NOT NULL,author TEXT NOT NULL)");
	sqlite_query($dbhle, "INSERT INTO posts VALUES(1,'Welcome to LightBlog!','Welcome to LightBlog! We hope you enjoy it!<br /><br />-The LightBlog Team<br />http://lightblog.googlecode.com/',1218324266,'LightBlog Devs')");
	sqlite_query($dbhle, "CREATE TABLE users(id INTEGER NOT NULL PRIMARY KEY DEFAULT '0',username TEXT NOT NULL,password TEXT NOT NULL,email TEXT NOT NULL,realname TEXT NOT NULL,vip INTEGER NOT NULL,ip TEXT NOT NULL)");
	sqlite_close($dbhle);
	$ccp = fopen("config.php", 'w') or die("Unable to write to configuration file.");
	$config = '<?php // DO NOT TOUCH THIS FILE IF YOU DON\'T KNOW WHAT YOU\'RE DOING!
	/* let\'s stop them hackers =) */ if(!defined("Light")) { die("DIE!"); }
	// open database file
	$handle = sqlite_open(dirname(__FILE__)."/'.$dbstr.'") or die("Database error: code 01");';
	fwrite($ccp, $config);
	fclose($ccp);
	$_SESSION['465650ad50650760ab'] = $dbstr;
	$step0 = "0"; $step1 = "0"; $step2 = "1"; 
}
if(isset($_POST['step2'])) {
	$password = md5($_POST['password']);
	$username = addslashes(sqlite_escape_string($_POST['username']));
    $password = md5($r['pass']);
    $email = addslashes(sqlite_escape_string($_POST['email']));
    $realname = addslashes(sqlite_escape_string($_POST['realname']));
	$ip = addslashes(sqlite_escape_string($_SERVER['REMOTE_ADDR']));
	$dbhle2 = sqlite_open(dirname(__FILE__)."/".$_SESSION['465650ad50650760ab']) or die("Could not open database");
	sqlite_query($dbhle2, "INSERT INTO users (username,password,email,realname,vip,ip) VALUES('".$username."','".$password."','".$email."','".$realname."',1,'".$ip."')");
	sqlite_close($dbhle2);
	$ccp2 = fopen("config.php", 'a') or die("Unable to write to configuration file.");
	$sitename = addslashes(sqlite_escape_string($_POST['sitename']));
	$config2 = '// site data
$site_name = "'.$sitename.'";
$site_url = explode(\'/\', $_SERVER[\'SERVER_NAME\'].$_SERVER[\'REQUEST_URI\']);
	unset($site_url[count($site_url)-1]);
	$site_url = implode(\'/\', $site_url);
	$site_url = \'http://\'.$site_url.\'/\'; ?>';
	fwrite($ccp2, $config2);
	fclose($ccp2);
	session_destroy();
	$step0 = "0"; $step1 = "0"; $step2 = "0"; $step3 = "1";
}
if(isset($_POST['step3'])) {
	header('Location: index.php');
}

?>
<!--	LightBlog v0.9.0
		Copyright 2008 soren121. Some Rights Reserved.
		Licensed under the General Public License v3.
		For more info, see the LICENSE.txt file included.
-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title><?php echo $site_name; ?></title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="generator" content="Geany 0.13/soren121" />
	<style type="text/css">
	/* 
		LightBlog
		Stylesheet - April 28, 2008
		Copyright 2008 soren121.
*/

body {
	background: #EDEDED;
	font-family: Verdana,Arial,Sans;
}

#container {
	width: 670px;
	margin-left: auto;
	margin-right: auto;
	margin-top: 20px;
}

#header {
	background: #2877B3;
	width: 670px;
	height: 30px;
	margin-bottom: 0px;
	padding-bottom: 0px;
	float: center;
}

img.headerimg {
	margin-left: 46px;
	margin-top: 3px;
	margin-bottom: 1px;
}

#content {
	float: right;
	background: #fff;
	color: #252525;
	margin: 0 0 0 0;
	padding-top: 10px;
	padding-left: 7px;
	width: 503px;
	display: inline;
}
	
#sidebar {
	background: #fff;
	color: #000;
	margin: 0 0 0 0;
	padding-top: 0px;
	border-right: 1px solid #ededed;
	width: 159px;
	float: left;
}

#sidebar li {
	color: #000;
	padding-top: 3px;
	font-size: 0.9em;
	list-style-type: square;
}

#sidebar a {
	color: #000;
	font-size: 0.9em;
	text-decoration: none;
}

#sidebar a:hover {
	color: #2877B3;
} 
</style>
	<!--[if IE]><style type="text/css">
	#content,#sidebar { margin-top: -2px; !important } 
	#sidebar { width: 167px; !important }
	</style><![endif]-->
</head>

<body>
<div id="container">
	<div id="header">
		<div id="headerimg">
			<img class="headerimg" src="admin/style/title.png" alt="LightBlog" />
		</div>
	</div>
	<div id="sidebar">
	<ul>
	<li><a href="http://lightblog.googlecode.com/">Google Code</a></li>
	<li><a href="http://groups.google.com/group/lightblog-s">Discussion Group</a></li>
	</ul>
	</div>
	<div id="content">
	<?php if($step3 == "1") {
	echo '
	<h3>Congratulations!</h3>
	<br />
	<p>You\'re done! We ask that you delete this file so no one can override your settings. Have fun!</p>
	<br />
	<p>--soren121, LightBlog Dev</p>
	<br />
	<form action="" method="post">
	<p><input name="step3" type="submit" value="Finish"/></p>
	</table>
	</form>
	<br />'; }
	if($step2 == "1") {
	echo '
	<h3>Settings</h3>
	<br />
	<p>You\'re almost done! All we need now is your account data and the name of your site!</p>
	<br />
	<form action="" method="post">
	<table style="margin-left: auto; margin-right: auto;">
	<tr><td>Site Name:</td><td><input name="sitename" type="text" size="36" /></td></tr>
	<tr><td>Username:</td><td><input name="username" type="text" size="16" /></td></tr>
	<tr><td>Password:</td><td><input name="password" type="password" size="16" /></td></tr>
	<tr><td>Email:</td><td><input name="email" type="text" size="28" /></td></tr>
	<tr><td>Display Name:</td><td><input name="realname" type="text" size="14" /></td></tr>
	<tr><td>&nbsp;</td><td><input name="step2" type="submit" value="Submit"/></td></tr>
	</table>
	</form>
	<br />'; }
	if($step1 == "1") {
	echo '
	<h3>Create database</h3>
	<br />
	<p>Please CHMOD the admin directory and root directory to 777 before continuing.\nDon\'t worry, you\'ll be able to change it back later.</p>
	<br />
	<form action="" method="post">
	<p><input name="step1" type="submit" value="Create database"/></p>
	</form>
	<br />'; }
	
	elseif($step0 == "1" or $step0 == NULL) { echo '
	<h3>Welcome to LightBlog!</h3>
	<br />
	<p>Welcome to the LightBlog installer. Click the Install button and we\'ll post started!</p>
	<br />
	<form action="" method="post">
	<input name="step0" type="submit" value="Install"/>
	</form>
	<br />'; }
	?>
	</div>
</div>
</body>
</html>
