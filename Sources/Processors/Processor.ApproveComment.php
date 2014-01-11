<?php
/*********************************************

	LightBlog 0.9
	SQLite blogging platform

	Sources/Processors/Processor.ApproveComment.php

	©2008-2012 The LightBlog Team. All
	rights reserved. Released under the
	GNU General Public License 3. For
	all licensing information, please
	see the LICENSE.txt document
	included in this distribution.

*********************************************/

class ApproveComment
{
	private $dbh;

	public function __construct()
	{
		$this->dbh = $GLOBALS['dbh'];
	}

	public function processor($data)
	{	
		if(permissions('EditComments'))
		{
			// Execute query to approve comment
			$query = @$this->dbh->exec("UPDATE comments SET published=1 WHERE id=".(int)$data['id']);
	
			if(!$query)
			{
				return array("result" => "error", "response" => sqlite_error_string($this->dbh->lastError()));
			}
	
			return array('result' => 'success');
		}
	}
}

?>