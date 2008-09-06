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

class Page
{
  var $page;

  function Page($template = "template.html") {
    if (file_exists($template))
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
      foreach ($tags as $tag =&gt; $data) {
        $data = (file_exists($data)) ? $this-&gt;parse($data) : $data;
        $this-&gt;page = eregi_replace("{" . $tag . "}", $data,
                      $this-&gt;page);
        }
    else
      die("No tags designated for replacement.");
  }

  function output() {
    echo $this-&gt;page;
  }
}
?>