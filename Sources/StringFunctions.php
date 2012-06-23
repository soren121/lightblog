<?php

/***********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	Sources/StringFunctions.php
	
	©2008-2012 The LightBlog Team. All 
	rights reserved. Released under the 
	GNU General Public License 3. For 
	all licensing information, please 
	see the LICENSE.txt document 
	included in this distribution.

***********************************************/

/*
	Function: randomString
	
	Returns a random alphanumeric string.
	
	Parameters:
	
		length - Length of string to make.
		
	Returns:
	
		A completely random string.
*/
function randomString($length) {
	if((is_numeric($length)) && ($length > 0) && (!is_null($length))) {
		// Start with a blank string
		$string = '';
		$accepted_chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890-,';
		// Loop through and make a string
		for($i = 0; $i <= $length; $i++) {
			$random_number = rand(0, (strlen($accepted_chars) -1));
			$string .= $accepted_chars[$random_number];
		}
		// Return the final string
		return $string;
	}
}

// Function to correct plurals and such on dynamic numbers, mainly comment numbers
function grammarFix($number, $singular, $plural) {
	if($number == 1) {
		// The number is 1, so we will use the singular form of the word
		echo $number.' '.$singular;
	}
	else {
		// The number is something other than 1, so we'll use the plural form
		echo $number.' '.$plural;
	}
}

?>
