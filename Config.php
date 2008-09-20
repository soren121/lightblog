<?php
/*************************************************

  LightBlog - PHP SQLite blogging platform
  Copyright 2008 soren121.
  
  This software is released under the GNU
  General Public License version 3. For more
  accurate licensing information, please see
  the LICENSE.txt file included in this
  distribution.
  
  Config.php
  
*************************************************/

// We don't want this file to be accessed directly!
if(!defined('Lighty')) {
	die("Hacking Attempt...");
}

// Database settings
$database = sqlite_open(""); // Path to your SQLite database

// Path settings for LightBlog folders
// These should have been setup during installation
$lighty['sources_dir'] = ''; // Path to your Sources directory with trailing /
$lighty['theme_dir'] = ''; // Path to your Themes directory with trailing /
$lighty['language_dir'] = ''; // Path to your Languages directory with trailing /
$lighty['site_url'] = ''; // URL to your LightBlog installation with trailing /

// Don't touch this!
$lighty_installed = false;
?>