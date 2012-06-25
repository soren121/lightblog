<?php
/*********************************************

	LightBlog 0.9
	SQLite blogging platform

	Sources/Processors/Processor.Settings.php

	©2008-2012 The LightBlog Team. All
	rights reserved. Released under the
	GNU General Public License 3. For
	all licensing information, please
	see the LICENSE.txt document
	included in this distribution.

*********************************************/

class Settings
{
	private $dbh;

	public function __construct()
	{
		$this->dbh = $GLOBALS['dbh'];
	}

	public function processor($data)
	{
		// We will collect the response here.
		$response = array(
									'result' => 'error',
									'response' => array(),
								);
	
		$options = array();
	
		// A title is required.
		if(empty($data['title']) || utf_strlen($data['title']) == 0)
		{
			$response['response'][] = 'A blog title is required.';
		}
		else
		{
			$options['title'] = utf_htmlspecialchars($data['title']);
		}
	
		// Same goes for the URL. It also needs to be valid.
		if(empty($data['url']) || !is_url($data['url']))
		{
			$response['response'][] = 'A valid URL is required.';
		}
		else
		{
			// We also want it to have a trailing slash.
			$options['url'] = utf_substr($data['url'], -1, 1) == '/' ? $data['url'] : $data['url']. '/';
		}
	
		// Make sure the time zone is valid.
		if(!array_key_exists('timezone', $data) || (float)$data['timezone'] < -12 || (float)$data['timezone'] > 12)
		{
			$response['response'][] = 'Invalid time zone selected.';
		}
		else
		{
			$options['timezone'] = (float)$data['timezone'];
		}
	
		// Now for the date...
		if(empty($data['date']) || ($data['date'] == 'custom' && empty($data['custom_date'])))
		{
			$response['response'][] = 'Invalid date format.';
		}
		else
		{
			$options['date_format'] = utf_htmlspecialchars($data['date'] == 'custom' ? $data['custom_date'] : $data['date']);
		}
	
		// Then time formatting.
		if(empty($data['time']) || ($data['time'] == 'custom' && empty($data['custom_time'])))
		{
			$response['response'][] = 'Invalid time format.';
		}
		else
		{
			$options['time_format'] = utf_htmlspecialchars($data['time'] == 'custom' ? $data['custom_time'] : $data['time']);
		}
	
		// Were there any issues?
		if(count($response['response']) == 0)
		{
			// Nope, so we can save the settings.
			$save_query = array();
			foreach($options as $option => $value)
			{
				$GLOBALS['bloginfo_data'][$option] = $value;
	
				if(is_string($value))
				{
					$value = sqlite_escape_string($value);
				}
	
				$save_query[] = 'INSERT OR REPLACE INTO settings (variable, value) VALUES(\''. sqlite_escape_string($option). '\', '. (is_string($value) ? '\''. $value. '\'' : $value). ');';
			}
	
			if($this->dbh->queryExec(implode("\r\n", $save_query)))
			{
				$response['result'] = 'success';
				$response['response'] = 'Settings saved.';
			}
			else
			{
				$response['response'] = $error_message;
			}
		}
	
		return $response;
	}
}

?>