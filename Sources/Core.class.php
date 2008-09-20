<?php
/*************************************************

  LightBlog - PHP SQLite blogging platform
     Copyright 2008 soren121 and aldo.
  
   This software is released under the GNU
  General Public License version 3. For more
  accurate licensing information, please see
     the LICENSE.txt file included in this
                  distribution.
  
                     Core.php
  
*************************************************/
if(!defined('Lighty'))
  die("Hacking Attempt...");
  
class Core {
  var $acts = array();
  var $l = array();
  var $lighty = array();
  
  public function loadIndex() {    
    $this->loadTemplate('index');
  }
  
  public function isAction($action) {
    // All the actions predefined in LightBlog are here...
    $acts = array(
      'login' => array('Login', 'Login'),
      'logout' => array('Logout', 'Login'),
      'register' => array('Register', 'Register')
    );
  }
  
  public function fatalError($message, $type = E_USER_ERROR) {
    trigger_error($message, $type);
  }
  
  public function loadSettings() {
  global $db, $lighty;
    $result = $db->query("SELECT * FROM '{$db_prefix}core'");
    $lighty = array();
    while($row = $db->fetchObject($result)) {
      $lighty[$row->variable] = stripslashes($row->value);
    }
    $this->lighty = $lighty;
  }
  
  public function loadLanguage() {
  global $language_dir, $lighty, $l;  
    require_once($language_dir. strtolower(ucwords($lighty['current_language'])). '.language.php');
    $this->l = $l;
  }
  
  public function loadTemplate($template, $data = array()) {
  global $sources_dir, $theme_dir; 
    require_once($sources_dir. '/Smarty.class.php');
    $smarty = new Smarty();
    $smarty->template_dir = $theme_dir. $this->lighty['current_theme'];
    $smarty->compile_dir = $sources_dir. 'Smarty/compiled_templates/'. $this->lighty['current_theme'];
    $smarty->cache_dir = $sources_dir. '/Smarty/cache';
	  $smarty->config_dir = $sources_dir. '/Smarty/config';
	  $smarty->plugins_dir = $sources_dir. '/Smarty/plugins';
	  if(!file_exists($smarty->compile_dir)) {
		  mkdir($smarty->compile_dir, 0755);
	  }
	  // Assign all required variables and functions
	  $smarty->register_function('l', 'loadLanguage');
	  $smarty->register_function('info', 'loadSettings');
	  $smarty->register_function('loadJS', $this->loadJS);
	  $smarty->register_function('loadpost', 'loadPost');
	  $smarty->assign('site_url', $site_url);
	  $smarty->assign('theme_dir', $theme_dir);
	  $vars = array(
	    'main_title' => $this->lighty['site_title'],
	    'title' => !empty($data['title']) ? $data['title'] : null,
	    'site_url' => $site_url
	  );
	  // Output the template!
	  $smarty->display($template. '.tpl');
  }
}
?>