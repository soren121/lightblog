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

// This function loads a language (obviously :P)
function loadLanguage(strtolower(ucwords($input))) {
	return include($language_dir.$input.'.language.php');
}

// This function adds the proper jQuery scripts to 
// the header of the theme
function lighty_head() {
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
	$smarty->template_dir = $theme_dir;
	$smarty->compile_dir = $sources_dir.'Smarty/compiled_templates';
	$smarty->cache_dir = $sources_dir.'Smarty/cache';
	$smarty->config_dir = $sources_dir.'Smarty/config';
	// Assign all required variables and functions
	
	// Output the template!
	return $smarty->display($lighty['current_theme'].$input);
}

?>