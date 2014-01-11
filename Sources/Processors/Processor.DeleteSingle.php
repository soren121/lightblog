<?php
/*********************************************

	LightBlog 0.9
	SQLite blogging platform

	Sources/Processors/Processor.DeleteSingle.php

	©2008-2012 The LightBlog Team. All
	rights reserved. Released under the
	GNU General Public License 3. For
	all licensing information, please
	see the LICENSE.txt document
	included in this distribution.

*********************************************/

class DeleteSingle
{
	private $dbh;

	public function __construct()
	{
		$this->dbh = $GLOBALS['dbh'];
	}

	public function processor($data)
	{	
		switch($data['type'])
		{
			case 'post':
				if(!permissions('EditOthersPosts'))
				{
					return array("result" => "error", "response" => "You're not allowed to delete this post.");
				}
				// Delete comments associated with this post
				@$this->dbh->exec("DELETE FROM comments WHERE post_id=".(int)$data['id']);
				// Update comment number in the posts table
				@$this->dbh->exec("UPDATE posts SET comments=0 WHERE post_id=".(int)$data['id']);
				break;
			case 'page':
				if(!permissions('EditPages'))
				{
					return array("result" => "error", "response" => "You're not allowed to delete this page.");
				}
				break;
			case 'user':
				if(!permissions('EditOtherUsers') || (int)$data['id'] == user()->id())
				{
					return array("result" => "error", "response" => "You're not allowed to delete this user.");
				}		
				break;
		}
	
		// Execute query to delete post/page/category
		$sql_delete = @$this->dbh->exec("DELETE FROM ".sqlite_escape_string(strip_tags($_POST['type']))."s WHERE ".sqlite_escape_string($data['type'])."_id=".(int)$_POST['id']);
	
		if($sql_delete == 0)
		{
			return array("result" => "error", "response" => "Delete query failed.");
		}
		return array("result" => "success");
	}
}

?>