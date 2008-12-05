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

// We don't want this file to be accessed directly!
if(!defined('Lighty')) {
  die(header("HTTP/1.1 404 Not Found"));
}
  
class Core {
  var $acts = array();
  var $l = array();
  var $lighty = array();
  
  // We were told to load the index, sir!
  // So we must load the index, sir!
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
  
  // This function produces a nice fatal error message
  // that even a casual user could read
  public function fatalError($message, $type = E_USER_ERROR) {
    trigger_error($message, $type);
  }
  
  // This function loads settings
  public function init($main_dir) {
  global $db, $lighty;
    include($main_dir.'Config.php');
	$db = new SQLiteDatabase(DBPATH);
    $result = $db->query("SELECT * FROM ". DBPREFIX ."core'");
    $lighty = array();
    while($row = $result->fetchObject()) {
      $lighty[$row->variable] = stripslashes($row->value);
    }
	$lighty['dbprefix'] = $db_prefix;
    $this->lighty = $lighty;
  }
  
  // This is the almighty POST GRABBER. Respect it! :P
  public function loadPost($params, &$smarty) {
  global $db;
	$result = $db->query("SELECT ".$params['column']." FROM '".$this->lighty['dbprefix']."posts' WHERE id='".$params['id']."'");
	return $result;
  }
  
  // This is the -other- almighty grabber, the PHRASE GRABBER. Respect it less! :P
  public function loadLanguage() {
  global $lighty, $l;  
    require_once( LANGUAGEDIR . ucwords(strtolower($lighty['current_language'])). '.language.php');
    $this->l = $l;
  }
  
  // This function...well, I shouldn't need to explain this one.
  public function loadTemplate($template, $data = array()) {
  global $db; 
    // Define the Smarty internals directory
    define('SMARTY_CORE_DIR',  SOURCESDIR .'Smarty/internals'.DIRECTORY_SEPARATOR);
	// Lowercase and capitalize the template name
	$template = ucwords(strtolower($template));
	// Open up the Smarty class!
    require_once( SOURCESDIR . '/Smarty.class.php');
	// Startup the class
    $smarty = new Smarty();
	// Set all the required paths and other settings needed by Smarty
    $smarty->template_dir =  THEMEDIR . $this->lighty['current_theme'];
    $smarty->compile_dir =  SOURCESDIR . 'Smarty/compiled_templates/'. $this->lighty['current_theme'];
    $smarty->cache_dir =  SOURCESDIR . 'Smarty/cache';
	$smarty->config_dir =  SOURCESDIR . 'Smarty/config';
	$smarty->plugins_dir =  SOURCESDIR . 'Smarty/plugins';
	$smarty->caching = 1;
	$smarty->cache_lifetime = 1440;
	// If a compile directory for the theme doesn't exist, make it
	if(!file_exists($smarty->compile_dir)) {
		mkdir($smarty->compile_dir, 0755);
	}
	// Assign all required variables and functions
	$smarty->register_function('l', 'loadLanguage');
	$smarty->register_function('loadpost', 'loadPost');
	$smarty->assign('info', $this->lighty);
	$smarty->assign('site_url',  MAINURL );
	$smarty->assign('script_dir',  MAINURL .'Sources/');
	$smarty->assign('theme_dir',  MAINURL .'Themes/'.$this->lighty['current_theme'].'/');
	$result = $db->query("SELECT id FROM lighty_posts ORDER BY desc");
	$smarty->assign('postcount', $result->numRows());
	// Output the template!
	return $smarty->display($smarty->template_dir .$template.'.tpl');
  }
}
?>