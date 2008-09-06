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
require($sources_dir.'Core.php');

if(isset($_GET['page'])) {
	loadTemplate($_GET['page']);
}

if(!(isset($_GET['page'])) {
	loadTemplate('main');
}
	
?>