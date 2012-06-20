<?php

/***********************************************

	LightBlog 0.9
	SQLite blogging platform

	Sources/FunctionReplacements.php

	©2008-2012 The LightBlog Team. All
	rights reserved. Released under the
	GNU General Public License 3. For
	all licensing information, please
	see the LICENSE.txt document
	included in this distribution.

***********************************************/

// Replacement for json_encode
// Code by aldo of http://www.todayinwindows.com/ for www.snowcms.com
if(!function_exists('json_encode'))
{
	function json_encode($value)
	{
		// Number..? Thats fine... its just that
		if(is_numeric($value))
		{
			return $value;
		}
		// A string..?
		elseif(is_string($value))
		{
			// Just incased in "
			return '"'. __json_sanitize($value). '"';
		}
		//An array..? this could be a biggy
		elseif(is_array($value))
		{
			// So we need to see if this is like a "flat" array,
			// as in, if the array is like: array('something','else')
			// it is "flat", or you could say, a keyless array
			if(__json_flat_array($value))
			{
				// Cool, cool, its flat... so now prepare it...
				$values = array();

				// Get the values...
				foreach($value as $val)
				{
					// Recursion, sorta
					$values[] = json_encode($val);

					// Implode and return...
					return '['. implode($values, ','). ']';
				}
			}
			else
			{
				// Now now, this is different, and this array has keys and values
				// So lets loop
				$values = array();
				foreach($value as $key => $val)
				{
					// This is a bit different, but should be easy...
					$values[] = '"'. __json_sanitize($key). '":'. json_encode($val);
				}

				// Implode and return
				return '{'. implode($values, ','). '}';
			}
		}
		elseif(is_object($value))
		{
			// For an object, we will just type cast it to an array.
			return json_encode((array)$value);
		}
	}

	function __json_flat_array($array)
	{
		foreach($array as $key => $value)
		{
			if(!is_int($key))
			{
				return false;
			}
		}

		return true;
	}

	function __json_sanitize($value)
	{
		// Sanitize and /
		$value = strtr($value, array('\\' => '\\\\', '/' => '\/'));

		// Now line breaks and what not...
		$value = strtr($value, array("\r\n" => '\\r\\n', "\r" => '\\r', "\n" => '\\n'));

		// Now escape ONLY "
		$value = addcslashes($value, '"');

		return $value;
	}
}
?>