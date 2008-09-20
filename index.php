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
// Define something, and get some files and such
define('Lighty', true);
require_once('./config.php');
require_once($sources_dir. '/Startup.php');

// Are we doing an ?act, ?page etc?
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