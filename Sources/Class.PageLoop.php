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
	private $dbh;

	// Variable: data
	// An array containing loaded data.
	private $data;

	// Variable: current
	// An integer containing the location of the current page in the array.
	private $current;

	// Variable: page
	// The current page.
	private $page;

	/*
		Constructor: __construct

		Sets the database handle for all functions in our class.
	*/
	public function __construct()
	{
		$this->dbh = null;
		$this->data = array(
										'pages' => array(),
										'count' => array(),
									);
		$this->current = null;
		$this->page = null;

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
		if(is_object($dbh) && is_a($dbh, 'SQLiteDatabase'))
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
		if(!isset($GLOBALS['pid']))
		{
			trigger_error('Unknown pid', E_USER_ERROR);
		}

		// Sanitize and set variables
		$pid = (int)$GLOBALS['pid'];

		$this->load($this->dbh->query("
			SELECT
				*
			FROM pages
			WHERE page_id = $pid". (!permissions(1) ? ' AND published = 1' : ''). "
			LIMIT 1"));
	}

	/*
		Function: obtain_pages

		Obtains all the current pages.
	*/
	public function obtain_pages()
	{
		$this->load($this->dbh->query("
			SELECT
				*
			FROM pages
			WHERE ". (!permissions(1) ? 'published = 1' : '1'). "
			ORDER BY post_title ASC"));
	}

	/*
		Function: has_pages

		Determines whether there are more pages to iterate through if $count is
		false, but otherwise returns the total number of pages currently loaded.

		Parameters:
			bool $count - Whether to return the number of posts loaded.

		Returns:

			Boolean value (e.g. true/false.)
	*/
	public function has_pages()
	{
		if(!empty($count))
		{
			return $this->data['pages'] !== null ? $this->data['count'] : 0;
		}

		// Do we have any pages?
		if($this->data['pages'] !== null && $this->current !== null)
		{
			// Save the current location.
			$this->current = key($this->data['pages']);
			$this->page = $this->current !== null ? $this->data['pages'][$this->current] : null;

			// Move us along, for the next time.
			next($this->data['pages']);

			return $this->current !== null;
		}
		else
		{
			// Nope, no posts.
			return false;
		}
	}

	/*
		Function: load

		Processes the results of a page load query.

		Parameters:
			resource $request

		Returns:
			void
	*/
	private function load($request)
	{
		if(empty($request))
		{
			trigger_error('An unknown error occurred while processing the pages', E_USER_ERROR);
		}

		// Alright, time to load them up!
		$users = array();
		$this->data['pages'] = array();
		$this->data['count'] = 0;
		while($row = $request->fetch(SQLITE_ASSOC))
		{
			$this->data['pages'][] = array(
																 'id' => $row['page_id'],
																 'title' => $row['page_title'],
																 'short_name' => $row['short_name'],
																 'date' => date('F j, Y', $row['page_date']),
																 'timestamp' => $row['page_date'],
																 'published' => $row['published'],
																 'author' => array(
																							 'id' => $row['author_id'],
																							 'name' => $row['author_name'],
																						 ),
																 'text' => $row['page_text'],
															 );

			$users[] = $row['author_id'];
			$this->data['count']++;
		}

		// Load the user data.
		users_load($users);

		// Make sure our pointer in the pages array is at the beginning. That is
		// unless we don't have any pages.
		if(count($this->data['pages']) > 0)
		{
			reset($this->data['pages']);
			$this->current = key($this->data['pages']);
			$this->page = null;
		}
		else
		{
			$this->data['pages'] = null;
			$this->data['count'] = 0;
			$this->current = null;
			$this->page = null;
		}
	}

	/*
		Function: permalink

		Outputs a permanent link to the page.
	*/
	public function permalink()
	{
		// We didn't screw up and keep an empty query, did we?
		if($this->page !== null)
		{
			echo get_bloginfo('url'). '?page='. $this->page['id'];
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
		if($this->page !== null)
		{
			echo $this->page['title'];
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
		if($this->page !== null)
		{
			echo $this->page['text'];
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