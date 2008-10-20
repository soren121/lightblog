<?php
/*************************************************

  LightBlog - PHP SQLite blogging platform
  Copyright 2008 soren121 and aldo.
  
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
// These should have been setup during installation
$db_path = ''; // Absolute server path to your SQLite database file
$db_prefix = 'lightblog_'; // Prefix for all your tables, just in case!

// Path settings for LightBlog folders
// These should have been setup during installation
$sources_dir = '';  // Path to your Sources directory with trailing /
$theme_dir = '';    // Path to your Themes directory with trailing /
$language_dir = ''; // Path to your Languages directory with trailing /
$site_url = '';     // URL to your LightBlog installation with trailing /
?>