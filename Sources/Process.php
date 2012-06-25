<?php
/*********************************************

	LightBlog 0.9
	SQLite blogging platform

	Sources/Process.php

	©2008-2012 The LightBlog Team. All
	rights reserved. Released under the
	GNU General Public License 3. For
	all licensing information, please
	see the LICENSE.txt document
	included in this distribution.

*********************************************/

function processForm($data)
{
	if(!empty($data) && isset($data['form']))
	{
		if(!isset($data['csrf_token']) || $data['csrf_token'] !== user()->csrf_token())
		{
			if($data['form'] != 'Comment')
			{
				return array('result' => 'error', 'response' => 'CSRF token incorrect or missing.');
			}
		}
		if(file_exists(ABSPATH .'/Sources/Processors/Processor.'.$data['form'].'.php'))
		{
			require(ABSPATH .'/Sources/Processors/Processor.'.$data['form'].'.php');
			$class = new $data['form']();
			return $class->processor($data);
		}
		else
		{
			return array('result' => 'error', 'response' => 'Form processor "'.$data['form'].'.php" does not exist.');
		}
	}
	else
	{
		return;
	}
}

?>