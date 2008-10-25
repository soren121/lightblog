<?php
/*************************************************

  LightBlog - PHP SQLite blogging platform
  Copyright 2008 soren121 and aldo.
  
  This software is released under the GNU
  General Public License version 3. For more
  accurate licensing information, please see
  the LICENSE.txt file included in this
  distribution.
  
  Startup.php
  
*************************************************/

// We don't want this file to be accessed directly!
if(!defined('Lighty')) {
  die(header("HTTP/1.1 404 Not Found"));
}

// Get some needed files...
require_once($sources_dir.'Core.php');

// Define these variables as class handles
$core = new Core;

// And now connect to the database...
$db = new SQLiteDatabase($db_path, $db_mode, $con_error);

// Is there a connection error?
if(!empty($con_error)) {
  $core->fatalError($con_error);
}

?>