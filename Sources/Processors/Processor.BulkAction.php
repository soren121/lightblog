<?php
/*********************************************

	LightBlog 0.9
	SQLite blogging platform

	Sources/Processors/Processor.BulkAction.php

	©2008-2012 The LightBlog Team. All
	rights reserved. Released under the
	GNU General Public License 3. For
	all licensing information, please
	see the LICENSE.txt document
	included in this distribution.

*********************************************/

class BulkAction
{
	private $dbh;

	public function __construct()
	{
		$this->dbh = $GLOBALS['dbh'];
	}

	public function processor($data)
	{
		if($data['checked'] != '' && permissions('EditOthersPosts'))
		{
			$type = sqlite_escape_string(strip_tags($data['type']));
			$in = sqlite_escape_string(implode(',', $data['checked']));
			switch($data['action'])
			{
				case 'delete':
					$query = @$this->dbh->query("DELETE FROM {$type}s WHERE {$type}_id IN ({$in})");
					break;
				case 'publish':
					$query = @$this->dbh->query("UPDATE {$type}s SET published=".time()." WHERE {$type}_id IN ({$in})");
					break;
				case 'unpublish':
					$query = @$this->dbh->query("UPDATE {$type}s SET published=0 WHERE {$type}_id IN ({$in})");
					break;
			}
	
			if(!$query)
			{
				return array("result" => "error", "response" => sqlite_error_string($this->dbh->lastError()));
			}
			else
			{
				return array("result" => "success");
			}
		}
	}
}

?>