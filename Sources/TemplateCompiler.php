<?php
/*************************************************

  LightBlog - PHP SQLite blogging platform
  Copyright 2008 soren121.
  
  This software is released under the GNU
  General Public License version 3. For more
  accurate licensing information, please see
  the LICENSE.txt file included in this
  distribution.
  
  TemplateCompiler.php
  
  Thanks to bluephoenix of codewalkers.com
  for the base of this class!
  
*************************************************/

require($sources_dir.'Core.php');

// We don't want this file to be accessed directly!
if(!defined('Lighty')) {
	die("Hacking Attempt...");
}

class Page
{
  var $page;

  function Page($template) {
    if (file_exists($theme_dir.$template.'.template.php'))
      $this->page = join("", file($template));
    else
      die("Template file $template not found.");
  }

  function parse($file) {
    ob_start();
    include($file);
    $buffer = ob_get_contents();
    ob_end_clean();
    return $buffer;
  }

  function replace_tags($tags = array()) {
    if (sizeof($tags) > 0)
      foreach ($tags as $tag => $data) {
        $data = (file_exists($data)) ? $this->parse($data) : $data;
        $this->page = eregi_replace("{" . $tag . "}", $data,
                      $this->page);
        }
    else
      die("No tags designated for replacement.");
  }

  function output() {
    echo $this->page;
  }
}

?>