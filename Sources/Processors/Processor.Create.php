<?php
/*********************************************

	LightBlog 0.9
	SQLite blogging platform

	Sources/Processors/Processor.Create.php

	©2008-2012 The LightBlog Team. All
	rights reserved. Released under the
	GNU General Public License 3. For
	all licensing information, please
	see the LICENSE.txt document
	included in this distribution.

*********************************************/

class Create
{
	private $dbh;
	
	public function __construct()
	{
		$this->dbh = $GLOBALS['dbh'];
	}
	
	public function processor($data)
	{
		$type = $data['type'];
		if(permissions('Create'.ucwords($type).'s'))
		{
			// Has anything been submitted?
			if(empty($data['title']) || empty($data['text']))
			{
				return array("result" => "error", "response" => "you must give your ".$type." a title and content.");
			}
	
			// Require the HTML filter class
			require(ABSPATH .'/Sources/Class.htmLawed.php');
	
			// Grab the data from form and clean things up
			$title = utf_htmlspecialchars($data['title']);
			$text = htmLawed::hl($data['text'], array('safe' => 1, 'make_tag_strict' => 1, 'balance' => 1, 'keep_bad' => 3));
	
			$date = time();
			$author = user()->displayName();
			$author_id = (int)user()->id();
			if($type == 'post')
			{
				$categories = (int)$data['category'];
			}
	
			// Check published checkbox
			if(isset($data['published']) && $data['published'] == 1)
			{
				$published = 1;
			}
			else
			{
				$published = 0;
			}
	
			// Check comments checkbox
			if(isset($data['comments']) && $data['comments'] == 1)
			{
				$comments = 1;
			}
			else
			{
				$comments = 0;
			}
	
			// Insert post/page into database
			if($type == 'post')
			{
				@$this->dbh->query("
					INSERT INTO 
						posts 
							(post_title,
							short_name,
							post_date,
							published,
							author_name,
							author_id,
							post_text,
							categories,
							allow_comments,
							allow_pingbacks,
							comments) 
					VALUES(
						'".sqlite_escape_string($title)."',
						' ',
						$date,
						$published,
						'".sqlite_escape_string($author)."',
						$author_id,
						'".sqlite_escape_string($text)."',
						$categories,
						$comments,
						0,
						0
					)"
				);
	
				if($this->dbh->changes() == 0)
				{
					return array("result" => "error", "response" => sqlite_error_string($this->dbh->lastError()));
				}
	
				$id = $this->dbh->lastInsertRowid();
	
				@$this->dbh->query("
					INSERT INTO
						post_categories 
							(post_id,
							category_id)
					VALUES(
						$id, 
						$categories
					)"
				);
			}
			else
			{
				@$this->dbh->query("
					INSERT INTO 
						pages 
							(page_title,
							short_name,
							page_date,
							published,
							author_name,
							author_id,
							page_text) 
					VALUES(
						'".sqlite_escape_string($title)."',
						' ',
						$date,
						$published,
						'".sqlite_escape_string($author)."',
						$author_id,
						'".sqlite_escape_string($text)."'
					)
				");
	
				$id = $this->dbh->lastInsertRowid();
			}
	
			if($this->dbh->changes() == 0)
			{
				return array("result" => "error", "response" => sqlite_error_string($this->dbh->lastError()));
			}
	
			// Create URL to return to jQuery
			$url = get_bloginfo('url')."?".$type."=".$id;
	
			// Return JSON-encoded response
			return array("result" => "success", "response" => $url);
		}
	}
}

?>