<?php session_start();

/*********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	admin/register.php
	
	©2009 soren121. All rights reserved.
	Released under the GNU General
	Public License. For all licensing
	information, please see the
	LICENSE.txt document included in this
	distribution.

*********************************************/

# Open config if not open
require('../config.php');
require(ABSPATH .'/Sources/Core.php');
require(ABSPATH .'/Sources/MathValidator.php');

# Initiate MathVaildator
$mv = new MathValidator;

# Process registration
if(isset($_REQUEST['processregistration'])) {
	# Generate and set salt
	$salt = substr(md5(uniqid(rand(), true)), 0, 9);
	# Set and escape all variables for easy access
	$username = sqlite_escape_string($_REQUEST['username']);
	$password = md5($salt.$_REQUEST['password']);
	$email = sqlite_escape_string($_REQUEST['email']);
	$dname = sqlite_escape_string($_REQUEST['dname']);
	$ip = sqlite_escape_string($_SERVER['REMOTE_ADDR']);
	$arians = (int)$_REQUEST['arians'];
	# Check math answer
	if($mv->checkResult($arians, $_SESSION['mathvalidator_c']) == false) { /* Tell user somehow that the answer was wrong */ }
	# Insert into database
	$dbh->query("INSERT INTO users (username,password,email,displayname,role,ip,salt) VALUES('".$username."', '".$password."', '".$email."', '".$displayname."', 0, '".$ip."', '".$salt."')");	
	die();
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title><?php bloginfo('title') ?> - Registration</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="generator" content="Geany 0.13/soren121" />
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.js"></script>
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.Validation.js"></script>
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.ValidateRegistration.js"></script>
	<script type="text/javascript" src="<?php bloginfo('url') ?>Sources/jQuery.Corners.js"></script>
	<script type="text/javascript" charset="utf-8">
	$(document).ready(function(){ $('.rounded').corners(); });
	$(function() {
		$('#register').submit(function() {
			var inputs = [];
			$(':input', this).each(function() {
				inputs.push(this.name + '=' + escape(this.value));
			})
			$('#register').empty().html('<' + 'img src="style/loading.gif" alt="" />');
			jQuery.ajax({
				data: inputs.join('&'),
				url: this.getAttribute('action'),
				timeout: 2000,
				error: function() {
					$('#register').empty(); 
					console.log("Failed to submit");
					alert("Failed to submit.");
				},
				success: function(r) {
					$('#register').empty(); 
					tabber1.show(2); return false;
				}
			})
			return false;
		})
	})
	</script>
	<style type="text/css">
	body {
		text-align: center;
		background: #eee;
	}
	#content {
		margin: 0 auto;
		height: 95%;
		width: 400px;
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
</head>

<body>
	<div id="content">
		<h2><?php bloginfo('title') ?> Registration</h2>
		<label for="username" class="error"></label>
		<label for="password" class="error"></label>
		<label for="email" class="error"></label>
		<label for="dname" class="error"></label>
		<form action="<?php echo basename(__FILE__); ?>" method="get" id="register">
			<p><label for="username">Username</label>
			<input type="text" name="username" id="username" /></p>
			<br />
			<p><label for="password">Password</label>
			<input type="password" name="password" id="password" /></p>
			<br />
			<p><label for="cpassword">Confirm Password</label>
			<input type="password" name="cpassword" id="cpassword" /></p>
			<br />
			<p><label for="email">Email</label>
			<input type="text" name="email" id="email" /></p>
			<br />
			<p><label for="dname">Display Name</label>
			<input type="text" name="dname" id="dname" /></p>
			<br />
			<p><label for="arians">What is <?php $mv->insertQuestion() ?>?</label>
			<input type="text" name="arians" id="arians" maxlength="2" /></p>
			<br />
			<p><input type="submit" name="processregistration" /></p>
		</form>
	</div>
</body>
</html>