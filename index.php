<?php
session_start();
/*************************************************

  LightBlog - PHP SQLite blogging platform
  Copyright 2008 soren121 and aldo.
  
  This software is released under the GNU
  General Public License version 3. For more
  accurate licensing information, please see
  the LICENSE.txt file included in this
  distribution.
  
  index.php
  
*************************************************/

// Define Lighty to show that this file can be accessed directly
define('Lighty', true);
// Open up the config and startup files
require_once('./config.php');
require_once($sources_dir. '/Startup.php');

// Are we doing an ?act, ?page, or what?
if(isset($_GET['act']) && $core->isAction($_GET['act'])) {
  $core->loadAction($_GET['page']);
}
elseif(!empty($_GET['page'])) {
  $core->loadPage($_GET['page']);
}
else {
  $core->loadIndex();
}
?>