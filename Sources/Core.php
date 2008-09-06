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

require_once('../Config.php');

// We don't want this file to be accessed directly!
if(!defined('Lighty')) {
	die("Hacking Attempt...");
}

// Check if LightBlog is installed
// We wouldn't want PHP producing fatal errors ;)
if($lighty_installed == "false") {
	header('Location: install.php');
}

// This function compiles and loads themes
function loadTemplate(strtolower(ucwords($pagename))) {
	// First, we want to check if a template for this page exists
	if(file_exists($theme_dir.$current_theme.'/'.$page.'.template.php')) {
		// Open up the template compiler
		require_once($sources_dir.'TemplateCompiler.php');
		// Start up the template compiler class
		$page = new Page($pagename);
		// Replace and compile template tags
		$page->replace_tags(array(
		));
		// Output page in pure (X)HTML
		return $page->output();
	}
}

?>