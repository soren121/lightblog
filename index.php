<?php
/*************************************************

  LightBlog - PHP SQLite blogging platform
  Copyright 2008 soren121.
  
  This software is released under the GNU
  General Public License version 3. For more
  accurate licensing information, please see
  the LICENSE.txt file included in this
  distribution.
  
  index.php
  
*************************************************/

define('Lighty', true);
require_once('Config.php');
require_once($sources_dir.'Core.php');

// Load page if specified
if(isset($_GET['page'])) {
	loadTemplate($_GET['page']);
}

// If no page is specified, load the index
if(!(isset($_GET['page']))) {
	loadTemplate('loop');
}
	
?>