
<?php
/**
* Smarty plugin
* @package Smarty
* @subpackage plugins
*/

/**
* Smarty {while} compiler function plugin
*
* Type:     compiler function<br>
* Name:     while<br>
* Date:     Sep 08 2006<br>
* Purpose:  provide a while-loop<br>
* @author Frank Habermann <lordlamer at lordlamer dot de>
* @param string containing var-attribute and value-attribute
* @param Smarty_Compiler
* Examples: The following code will loop 5 times ;)
*     <pre>
* {assign var="test" value=1}
* {while ($test <= 5)}
*    {assign var="test" value="`$test+1`"}
*   jo
* {/while}
*     </pre>
*/

$this->register_compiler_function('/while', 'smarty_compiler_endwhile');

function smarty_compiler_while($tag_arg, &$smarty) {
   $res = $smarty->_compile_if_tag($tag_arg);
   preg_match("/<\?php if (.*): \?>/",$res,$token);
   return "while " . $token[1] . " {";
}

function smarty_compiler_endwhile($tag_arg, &$smarty) {
   return "}";
}

?> 