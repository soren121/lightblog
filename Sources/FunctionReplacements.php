<?php
/***********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	Sources/FunctionReplacements.php
	
	©2009 soren121. All rights reserved.
	Released under the GNU General
	Public License. For all licensing
	information, please see the
	LICENSE.txt document included in this
	distribution.

***********************************************/

//=============
//mb_ functions
//=============
if (!function_exists( 'mb_strlen' )) {
	function mb_strlen( $string, $encoding=NULL ) {
		if ($encoding != NULL) {
			return strlen( $string, $encoding );
		} else {
			return strlen( $string );
		}
	}
}

if (!function_exists( 'mb_substr' )) {
	function mb_substr( $string, $start, $length=NULL, $encoding=NULL ) {
		if ($length != NULL) {
			return substr( $string, $start, $length );
		} else {
			return substr( $string, $start );
		}
	}
}
?>