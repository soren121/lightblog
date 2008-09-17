<?php
/*************************************************

  LightBlog - PHP SQLite blogging platform
  Copyright 2008 soren121.
  
  This software is released under the GNU
  General Public License version 3. For more
  accurate licensing information, please see
  the LICENSE.txt file included in this
  distribution.
  
  Core.php
  
*************************************************/

error_reporting(E_ALL);
require_once(getcwd().'\Config.php');

// We don't want this file to be accessed directly!
if(!defined('Lighty')) {
	die("Hacking Attempt...");
}

// Check if LightBlog is installed
// We wouldn't want PHP producing fatal errors ;)
if($lighty_installed == false) {
	header('Location: install.php');
}

function fatalError($type, $message) {
	echo $type.$message;
}

// Grab the main options from the "core" table
$query = sqlite_query($database, "SELECT * FROM core") or die(fatalError('DB','QueryFailed'));
while($row = sqlite_fetch_array($query)) {
	$lighty[$row['variable']] = stripslashes(stripslashes($row['value']));
}
unset($query);

// This function loads a language (obviously :P) for Smarty
function loadLanguage($params, &$smarty) {
	include_once($language_dir.strtolower(ucwords($lighty['current_language'])).'.language.php');
	return $l[$params['name']];
}

// This function adds the proper jQuery scripts to 
// the header of the theme
function loadJS() {
	$output = '
	<script type="text/javascript" src="'.$site_url.'Sources/jquery.js"></script>
	<script type="text/javascript" src="'.$site_url.'Sources/jquery.ui.js"></script>
	<script type="text/javascript" src="'.$site_url.'Sources/jquery.form.js"></script>
	<script type="text/javascript" src="'.$site_url.'Sources/jquery.wysiwyg.js"></script>
	<script type="text/javascript">
	$(document).ready(function() { $(\'#ajaxform\').ajaxForm(function() { alert("The form has been submitted. Thanks!");});});</script>
	<script type="text/javascript">$(function() { $(\'#wysiwyg\').wysiwyg(); });</script>';
	return $output;
}

// This function will load a specific part of
// a post from the database
function loadPost($params, &$smarty) {
	return sqlite_query($database, "SELECT ".$params['name']." from posts ORDER BY id desc") or die(fatalError('DB', 'QueryFailed'));
}

// This function simply takes the $lighty variable
// and gives it to Smarty in an easier way
function loadSettings($params, &$smarty) {
	return $lighty[$params['v']];
}

// This function compiles and loads themes
function loadTemplate($input) {
	// Lowercase and capitalize the input; can't be too careful!
	$input = strtolower(ucwords($input));
	// Open up the Smarty class
	require_once($sources_dir.'Smarty.class.php');
	// Startup the class!
	$smarty = new Smarty();
	// Set all the paths...
	$smarty->template_dir = $theme_dir.$lighty['current_theme'];
	$smarty->compile_dir = $sources_dir.'Smarty/compiled_templates/'.$lighty['current_theme'];
	$smarty->cache_dir = $sources_dir.'Smarty/cache';
	$smarty->config_dir = $sources_dir.'Smarty/config';
	$smarty->plugins_dir = $sources_dir.'Smarty/plugins';
	// Check if current theme has a compile directory
	// If not, make one
	if(!file_exists($smarty->compile_dir)) {
		mkdir($smarty->compile_dir, 0755);
	}
	// Assign all required variables and functions
	$smarty->register_function('l', 'loadLanguage');
	$smarty->register_function('info', 'loadSettings');
	$smarty->register_function('loadjs', 'loadJS');
	$smarty->register_function('loadpost', 'loadPost');
	$query = sqlite_query($database, "SELECT * FROM posts ORDER BY id desc") or die(fatalError('DB', 'QueryFailed'));
	$smarty->assign('postcount_main', sqlite_num_rows($query));
	unset($query);
	$smarty->assign('site_url', $site_url);
	$smarty->assign('theme_dir', $theme_dir);
	// Output the template!
	return $smarty->display($input.'.tpl');
}

?>