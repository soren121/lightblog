<?php

/***********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	Sources/StringFunctions.php
	
	©2009-2010 The LightBlog Team. All 
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

/*
	Function: cleanHTML
	
	Cleans HTML input to reduce the risk of XSS attacks.
	
	Parameters:
	
		str - HTML code to clean.
		
	Returns:
	
		Clean HTML code.
*/
function cleanHTML($str) {
	// Remove empty space
	$str = trim($str);
	// Strip out CDATA
	preg_match_all('/<!\[cdata\[(.*?)\]\]>/is', $str, $matches);
    $str = str_replace($matches[0], $matches[1], $str);
	// Strip out all JavaScript
    $str = preg_replace("/href=(['\"]).*?javascript:(.*)?\\1/i", "onclick=' $2 '", $str);
    while(preg_match("/<(.*)?javascript.*?\(.*?((?>[^()]+)|(?R)).*?\)?\)(.*)?>/i", $str))
        $str = preg_replace("/<(.*)?javascript.*?\(.*?((?>[^()]+)|(?R)).*?\)?\)(.*)?>/i", "<$1$3$4$5>", $str);
    // Remove all expressions
	$str = preg_replace("/:expression\(.*?((?>[^(.*?)]+)|(?R)).*?\)\)/i", "", $str);
    while(preg_match("/<(.*)?:expr.*?\(.*?((?>[^()]+)|(?R)).*?\)?\)(.*)?>/i", $str))
        $str = preg_replace("/<(.*)?:expr.*?\(.*?((?>[^()]+)|(?R)).*?\)?\)(.*)?>/i", "<$1$3$4$5>", $str);
	// Remove all on* attributes
    while(preg_match("/<(.*)?\s?on.+?=?\s?.+?(['\"]).*?\\2\s?(.*)?>/i", $str))
       $str = preg_replace("/<(.*)?\s?on.+?=?\s?.+?(['\"]).*?\\2\s?(.*)?>/i", "<$1$3>", $str);
	// Strip all but allowed tags
	$str = strip_tags($str, "<b><strong><i><em><u><a><img><quote><div><span><br><ol><ul><li>");
	// Convert symbols to HTML entities to kill hex attacks
	$str = str_replace("#", "&#35;", $str);
	$str = str_replace("%", "&#37;", $str);
	// Return the final string
	return $str;
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
