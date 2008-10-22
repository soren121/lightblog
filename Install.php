<?php
/*************************************************

  LightBlog - PHP SQLite blogging platform
  Copyright 2008 soren121 and aldo.
  
  This software is released under the GNU
  General Public License version 3. For more
  accurate licensing information, please see
  the LICENSE.txt file included in this
  distribution.
  
  Install.php
  
*************************************************/

// First, check to see if Lighty has already been installed
if(file_exists('Config.php')) {
	$success = 3;
}

// It hasn't, so set the page to default
elseif(!file_exists('Config.php')) {
	$success = 1;
}

// Database name generator
// Based on code from www.webtoolkit.info
function generateDatabaseName($length=9, $strength=0) {
    // Default vowels and consonants for name
	$vowels = 'aeuy';
    $consonants = 'bdghjmnpqrstvz';
	// Controls strength levels
    if ($strength & 1) {
        $consonants .= 'BDGHJLMNPQRSTVWXZ';
    }
    if ($strength & 2) {
        $vowels .= 'AEUY';
    }
    if ($strength & 4) {
        $consonants .= '23456789';
    }
    if ($strength & 8) {
        $consonants .= '@#$%';
    }

	// Blank variable
    $grname = '';
	// Set salt
    $alt = time() % 2;
    for ($i = 0; $i < $length; $i++) {
        if ($alt == 1) {
            $grname .= $consonants[(rand() % strlen($consonants))];
            $alt = 0;
        } else {
            $grname .= $vowels[(rand() % strlen($vowels))];
            $alt = 1;
        }
    }
	// Return generated name
    return $grname;
}

// Function to get current URL in PHP
function fullURL() {
    $iurl = explode('/', $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
    unset($iurl[count($iurl)-1]);
    $iurl = implode('/', $iurl);
    $installpath = 'http://'.$iurl.'/';
	return $installpath;
}

// Function to get full path with forward-slashes!
function fullPath() {
	$path = str_replace('\\', '/', dirname(__FILE__));
	return $path;
	unset($path);
}

// Redirect home if installation was successful!
if(array_key_exists('_redirecthome', $_POST)) {
	header('Location: index.php');
}

// The exciting part! (a.k.a. the actual installer) :P 
if(array_key_exists('_install', $_POST)) {
	// Generate database name
	$dbname = generateDatabaseName(10, 4).'.db';
	// Create database
	fclose(fopen($dbname, 'w')) or die('Could not create the database. Please check your permissions.');
	// Open database for writing
	$handler = sqlite_open($dbname) or die('WTF? Could not open the database. Please check your permissions.');
	// Read SQL
	$sqlopener = fopen('Install.sql', 'r');
	$sql = fread($sqlopener, filesize('Install.sql'));
	fclose($sqlopener);
	// Replace prefix, admin data, etc.
	str_replace('{dbprefix}', $_POST['_dbprefix'], $sql);
	// Inject SQL into database
	sqlite_query($handler, $sql) or die('Could not write to the database. Please check your permissions.');
	// Begin Config.php creation process
	// Read start of example and store in variable
	$copener = fopen('Config.example.php', 'r');
	$cstart = fread($copener, 601);
	fclose($copener);
	// Create new Config.php
	fclose(fopen('Config.php', 'w')) or die('Could not create Config.php. Please check your permissions.');
	// Write start of Config.php
	unset($copener);
	$copener = fopen('Config.php', 'w');
	fwrite($copener, $cstart) or die('Could not write to Config.php. Please check your permissions.');
	fclose($copener);
	// Create end of Config.php
	$cend = "
\$db_path = '".fullPath().'/'.$dbname."'; // Absolute server path to your SQLite database file
\$db_prefix = '".$_POST['_dbprefix']."'; // Prefix for all your tables, just in case!

// Path settings for LightBlog folders
// These should have been setup during installation
\$main_dir = '".fullPath()."';     // Path to your base directory with trailing /
\$sources_dir = '".fullPath()."/Sources/';  // Path to your Sources directory with trailing /
\$theme_dir = '".fullPath()."/Themes/';    // Path to your Themes directory with trailing /
\$language_dir = '".fullPath()."/Languages/'; // Path to your Languages directory with trailing /
\$site_url = '".fullURL()."';     // URL to your LightBlog installation with trailing /
?>";
	// Write the end of Config.php
	unset($copener);
	$copener = fopen('Config.php', 'a');
	fwrite($copener, $cend) or die('Could not append to Config.php. Please check your permissions.');
	fclose($copener);
	// Close and unset all variables
	unset($dbname, $sql, $sqlopener, $copener, $cstart, $cend);
	sqlite_close($handler);
	// Send success message to user
	$success = 2;
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>LightBlog SVN Installer</title>
	<style type="text/css">
	body {
		background: #eeeeec;
		text-align: center;
	}
	#container {
		text-align: left;
		background: #204A87;
		width: 340px;
		height: 400px;
		margin: 20px auto;
		padding-top: 5px;
	}
	.contentbox {
		margin: 4px 4px 4px 4px !important;
		background: #8FB7CF;
		color: #000000;
		border: 1px solid #fff;
		text-align: center;
	}
	form, strong, p {
		margin-left: auto;
		margin-right: auto;
	}
	.text {
		width: 100px;
	}
  </style>
</head>
<body>
	<div id="container">
		<div class="contentbox">
			<strong>LightBlog SVN Installer</strong>
		</div>
		<?php if($success == 1) : ?>
		<div class="contentbox">
			<p>Welcome to the quick 'n dirty installer for LightBlog SVN. Click Install to create the database.</p>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
				<p>DB Prefix: <input class="text" type="text" name="_dbprefix" value="lighty_" /></p>
				<p><input type="submit" name="_install" value="Install" /></p>
			</form>
		</div>
		<?php endif; if($success == 2) : ?>
		<div class="contentbox">
			<p>LightBlog was successfully installed! Click Next to continue to your new blog's front page.</p>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
				<p><input type="submit" name="_redirecthome" value="Next" /></p>
			</form>
		</div>
		<?php endif; if($success = 3) : ?>
		<div class="contentbox">
			<p>LightBlog is already installed! Delete Config.php if you REALLY want to reinstall.</p>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
				<p><input type="submit" name="_redirecthome" value="Next" /></p>
			</form>
		</div>
		<?php endif; ?>
	</div>
</body>
</html>