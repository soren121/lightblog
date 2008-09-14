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
	<script type="text/javascript" src="'.$site_url.'sources/jquery.js"></script>
	<script type="text/javascript" src="'.$site_url.'sources/jquery.ui.js"></script>
	<script type="text/javascript" src="'.$site_url.'sources/jquery.form.js"></script>
	<script type="text/javascript" src="'.$site_url.'sources/jquery.wysiwyg.js"></script>
	<script type="text/javascript">
	$(document).ready(function() { $(\'#ajaxform\').ajaxForm(function() { alert("The form has been submitted. Thanks!");});});</script>
	<script type="text/javascript">$(function() { $(\'#wysiwyg\').wysiwyg(); });</script>';
	return $output;
}


// This function compiles and loads themes
function loadTemplate(strtolower(ucwords($input))) {
	// Start up the template compiler!
	require_once('TemplateCompiler.php');
	$page = new Page($input);
	// Replace the tags and send it out!
	loadLanguage($current_language);
	$page->replace_tags(array(
		"l=$lparameter" => $language[$lparameter],
		"headscripts" => lighty_head()
	));
	return $output;
}

?>