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

// We don't want this file to be accessed directly!
if(!defined('Lighty')) {
	die("Hacking Attempt...");
}

// Check if LightBlog is installed
// We wouldn't want PHP producing fatal errors ;)
if($lighty_installed == "false") {
	header('Location: install.php');
}

// Set database handle
$database = sqlite_open($dbpath) or die(fatalError('DB','NotFound'));

// Grab the main options from the "core" table
while($row = sqlite_fetch_array(sqlite_query($database, "SELECT * FROM core") or die(fatalError('DB','QueryFailed')))) {
	$lighty[$row['variable']] = stripslashes(stripslashes($row['value']));
}

// This function loads a language (obviously :P) for Smarty
function loadLanguage($params, &$smarty) {
	include_once($language_dir.strtolower(ucwords($lighty['current_language'])).'.language.php';
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


// This function compiles and loads themes
function loadTemplate(strtolower(ucwords($input))) {
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
	// Output the template!
	return $smarty->display($input.'.tpl');
}

?>