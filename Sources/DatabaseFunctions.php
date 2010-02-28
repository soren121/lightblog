<?php

/***********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	Sources/DatabaseFunctions.php
	
	©2009-2010 The LightBlog Team. All 
	rights reserved. Released under the 
	GNU General Public License 3. For 
	all licensing information, please 
	see the LICENSE.txt document 
	included in this distribution.

***********************************************/

/*
	Function: bloginfo
	
	Returns the value of a given row.
	
	Parameters:
	
		var - Row to obtain value from.
		output - Specifies whether the version will be echoed or returned.
		
	Returns:
	
		The value of the given row.
*/
function bloginfo($var, $output = 'e') {
	# Global the database handle
	global $dbh;
	# Make PHP remember $bloginfo next time
	static $bloginfo = null;
	# If this is the first time bloginfo's been called...
	if($bloginfo == null) {
		$result = $dbh->query('SELECT * FROM core') or die(sqlite_error_string($dbh->lastError));
		# Let's make an array!
		$bloginfo = array();
		# For each row, set a key with the value
		while($row = $result->fetchObject()) {
			$bloginfo[$row->variable] = $row->value;
		}
		if(!isset($bloginfo['themeurl'])) {
			// Set the theme URL
			$bloginfo['themeurl'] = $bloginfo['url'].'themes/'.$bloginfo['theme'];
		}
	}
	# Are we echoing or returning?
	if($output == 'e') { echo !empty($bloginfo[$var]) ? $bloginfo[$var] : false; }
	else { return !empty($bloginfo[$var]) ? $bloginfo[$var] : false; }	
}

?>
