<?php

/***********************************************

	LightBlog 0.9
	SQLite blogging platform

	Sources/Class.PageLoop.php

	©2008-2012 The LightBlog Team. All
	rights reserved. Released under the
	GNU General Public License 3. For
	all licensing information, please
	see the LICENSE.txt document
	included in this distribution.

***********************************************/

/*
	Class: PageLoop

	Provides an easy way for theme developers to display pages.
*/
class PageLoop
{
	// Variable: dbh
	// The database handle.
	private $dbh = null;
	private $result = null;
	private $cur_result = null;

	/*
		Constructor: __construct

		Sets the database handle for all functions in our class.
	*/
	public function __construct()
	{
		$this->set_dbh($GLOBALS['dbh']);
	}

	/*
		Function: set_dbh

		Sets the database handle.

		Parameters:

			dbh - Database handle object.
	*/
	private function set_dbh($dbh)
	{
		// Is this a valid handle?
		if(is_object($dbh) && $dbh instanceof SQLiteDatabase)
		{
			$this->dbh = $dbh;
		}
		else
		{
			// It's not a valid database :(
			trigger_error('Invalid object supplied.', E_USER_ERROR);
		}
	}

	/*
		Function: obtain_page

		Obtains the data for a page from the database.
	*/
	public function obtain_page()
	{
		// Sanitize and set variables
		$pid = (int)$GLOBALS['pid'];
		$dbh = $this->dbh;

		// Query the database for the page data
		$this->result = $dbh->query("
			SELECT
				*
			FROM 'pages'
			WHERE page_id = ".$pid);
	}

	/*
		Function: has_pages

		Checks if the query result we got contained any pages.

		Returns:

			Boolean value (e.g. true/false.)
	*/
	public function has_pages()
	{
		// Do we have any pages?
		if(!empty($this->result))
		{
			// Convert query results into something usable
			$this->cur_result = $this->result->fetchObject();

			// This while loop will remain true until we run out of pages
			while($post = $this->cur_result)
			{
				return true;
			}

			// At which point it turns false, ending the loop in the template file
			return false;
		}
		// We don't have any pages :(
		else
		{
			// Erase our useless query results
			$this->result = null;
			$this->cur_result = null;

			// Send the bad news (aka end the while loop)
			return false;
		}
	}

	/*
		Function: permalink

		Outputs a permanent link to the page.
	*/
	public function permalink()
	{
		// We didn't screw up and keep an empty query, did we?
		if(!empty($this->cur_result))
		{
			echo get_bloginfo('url'). '?page='. (int)$this->cur_result->page_id;
		}
		// Oh no, we screwed up :(
		else
		{
			// Send nothing back
			return false;
		}
	}

	/*
		Function: title

		Outputs the title of the page.
	*/
	public function title()
	{
		// We didn't screw up and keep an empty query, did we?
		if(!empty($this->cur_result))
		{
			echo $this->cur_result->page_title;
		}
		// Oh no, we screwed up :(
		else
		{
			// Send nothing back
			return false;
		}
	}

	/*
		Function: content

		Outputs the content of the page.
	*/
	public function content()
	{
		// We didn't screw up and keep an empty query, did we?
		if(!empty($this->cur_result))
		{
			echo $this->cur_result->page_text;
		}
		// Oh no, we screwed up :(
		else
		{
			// Send nothing back
			return false;
		}
	}
}
?>