<?php

/***********************************************

	LightBlog 0.9
	SQLite blogging platform

	Sources/PostFunctions.php

	©2008-2012 The LightBlog Team. All
	rights reserved. Released under the
	GNU General Public License 3. For
	all licensing information, please
	see the LICENSE.txt document
	included in this distribution.

***********************************************/

if(!defined('INLB'))
{
	die('Nice try...');
}

/*
	Function: generate_shortname

	Parameters:
		int $id
		string $name
*/
function generate_shortname($id, $name)
{
	$char_map = 'abcdefghijklmnopqrstuvwxyz0123456789';
	$name_length = utf_strlen($name);
	$name = utf_strtolower($name);
	$shortname = '';
	$prev_char = null;
	for($index = 0; $index < $name_length; $index++)
	{
		$char = utf_substr($name, $index, 1);

		// Is this an allowed character?
		if(utf_strpos($char_map, $char) === false)
		{
			// No repeated -.
			if($prev_char !== null && $prev_char != '-')
			{
				$prev_char = '-';
				$shortname .= '-';
			}
		}
		else
		{
			$prev_char = $char;
			$shortname .= $char;
		}
	}

	return ((int)$id). '-'. trim($shortname, '-');
}
?>