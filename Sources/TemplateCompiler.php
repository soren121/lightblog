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
  
  Thanks goes to bluephoenix of codewalkers.com
  for this template compiler!
  
*************************************************/

class Page
{
  // Start $page variable for easy use
  var $page;
  // Make the template path easier to read and write ;)
  $current_theme_dir = $theme_dir.$current_theme.'/';

  function Page($template) {
    if (file_exists($current_theme_dir.$template.'.template.html'))
      $this->page = join("", file($current_theme_dir.$template.'.template.html'));
    else
      die("Template file $template not found.");
  }

  // File parser function
  function parse($file) {
    ob_start();
	$file = $current_theme_dir.$file.'.template.html';
    include($file);
    $buffer = ob_get_contents();
    ob_end_clean();
    return $buffer;
  }

  // Tag replacer function
  function replace_tags($tags = array()) {
    if (sizeof($tags) > 0) {
      foreach ($tags as $tag => $data) {
        $data = (file_exists($data)) ? $this->parse($data) : $data;
        $this->page = eregi_replace("{" . $tag . "}", $data, $this->page);
      }
	}
	
    else {
      die("No tags designated for replacement.");
	}
  }

  // Function to output the page to the browser
  function output() {
    echo $this->page;
  }
}
?>