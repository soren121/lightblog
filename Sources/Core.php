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

// This function compiles and loads themes
function loadTemplate(strtolower(ucwords($input))) {
	// Make the template path easier to read ;)
	$current_theme_dir = $theme_dir.$current_theme.'/';
	// First, we want to check if a template for this page exists
	if(file_exists($current_theme_dir.$input.'.template.php')) {
		// Open up the template
		require_once($current_theme_dir.$input.'.template.php');
		// Tack on the header, sidebar, and footer and send it out!
		loadLanguage($current_language);
		$header = include($current_theme_dir.'Header.template.php');
		$sidebar = include($current_theme_dir.'Sidebar.template.php');
		$page = include($current_theme_dir.$input.'.template.php');
		$footer = include($current_theme_dir.'Footer.template.php');
		$output = echo $header.$sidebar.$page.$footer;
		return $output;
	}
}

?>