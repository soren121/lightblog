<?php

/***********************************************

	LightBlog 0.9
	SQLite blogging platform

	Sources/Class.PostLoop.php

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

require(ABSPATH. '/Sources/PostFunctions.php');

/*
	Class: PostLoop

	Provides an easy method to display a list of posts, for example, on the
	front page.
*/
class PostLoop
{
	// Variable: dbh
	// The database handle.
	private $dbh;

	// Variable: result
	// The query result from fetching the posts to be displayed.
	private $result;

	// Variable: current
	// The current row retrieved from the database.
	private $current;

	/*
		Constructor: __construct

		Sets the database handle for all functions in our class.
	*/
	public function __construct()
	{
		$this->dbh = null;
		$this->data  = array(
										 'posts' => array(),
										 'count' => null,
										 'max_page' => null,
										 'page' => null,
										 'categories' => array(),
									 );
		$this->current = null;
		$this->post = null;

		$this->set_dbh($GLOBALS['dbh']);
	}

	/*
		Function: set_dbh

		Sets the database handle.

		Parameters:
			resource $dbh - Database handle object.

		Returns:
			void - Nothing is returned by this method.
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
		Function: generateQuery

		Generates a query to use to fetch the currently desired posts.

		Parameters:
			bool $is_count - Whether to replace COUNT(*) with the column selectors
											 in the generated query.

		Returns:
			string - A complete SQL query.
	*/
	private function generateQuery($is_count = false)
	{
		// What kind of query are we generating?
		if(!isset($GLOBALS['postquery']['type']))
		{
			// I guess we don't know... You have to tell us!
			trigger_error('Unknown post query type', E_USER_ERROR);
		}

		$querytype = $GLOBALS['postquery']['type'];
		$options = array(
								 'join' => array(),
								 'where' => array(),
								 'order_by' => array('post_date DESC'),
							 );

		// Perhaps they're viewing a single post?
		if($querytype == 'post')
		{
			// Then get the specific post ID. If it is defined...
			if(!isset($GLOBALS['pid']))
			{
				trigger_error('Unknown pid', E_USER_ERROR);
			}

			$pid = (int)$GLOBALS['pid'];

			// We know the specific ID, so add it to our WHERE clause.
			$options['where'][] = 'post_id= '. $pid;

			// We don't need to order by anything.
			$options['order_by'] = array();
		}
		// Viewing the archive list?
		elseif($querytype == 'archive')
		{
			list($year, $month) = explode('-', substr_replace((int)$GLOBALS['postquery']['date'], '-', 4, 0));

			// Now we will get the range for the post dates to retrieve.
			$firstday = mktime(0, 0, 0, $month, 1, $year);
			$lastday = mktime(0, 0, 0, $month + 1, 0, $year);

			$options['where'][] = 'post_date BETWEEN '. $firstday. ' AND '. $lastday;
		}
		elseif($querytype == 'category')
		{
			$category_id = (int)$GLOBALS['postquery']['catid'];

			// Just add a JOIN.
			$options['join'][] = 'INNER JOIN post_categories AS pc ON pc.post_id = p.post_id AND pc.category_id = '. $category_id;
		}
		elseif($querytype != 'latest')
		{
			trigger_error('Unknown post query type '. utf_htmlspecialchars($querytype), E_USER_ERROR);
		}

		return '
			SELECT
				'. (!empty($is_count) ? 'COUNT(*)' : 'p.*'). '
			FROM posts AS p'. (count($options['join']) > 0 ? '
			'. implode("\r\n", $options['join']). "\r\n" : ''). '
			WHERE '. (count($options['where']) > 0 ? implode(' AND ', $options['where']) : '1'). (count($options['order_by']) > 0 ? '
			ORDER BY '. implode(', ', $options['order_by']) : '');
	}

	/*
		Function: obtain_post

		Obtains the data for a single post from the database.

		Parameters:
			none

		Returns:
			void
	*/
	public function obtain_post()
	{
		// Just load that single post, please!
		$this->data['count'] = 1;
		$this->load($this->dbh->query($this->generateQuery()));
	}

	/*
		Function: obtain_posts

		Obtains the data for multiple posts from the database.

		Parameters:
			int $page - The current page being viewed.
			int $limit - The maximum number of posts to load on the page.

		Returns:
			void
	*/
	public function obtain_posts($page = 1, $limit = 8)
	{
		// We won't load less than 1 post :-P.
		$limit = (int)$limit >= 1 ? (int)$limit : 1;

		// Okay, first off, we need to see how many posts there are in total.
		$request = $this->dbh->query($this->generateQuery(true));

		list($this->data['count']) = $request->fetch(SQLITE_NUM);

		// Let's see, how many pages can we have?
		$this->data['max_page'] = ceil($this->data['count'] / (int)$limit);

		// Now let's make sure the page is valid.
		$this->data['page'] = (int)$page <= 1 ? 1 : ((int)$page > $this->data['max_page'] ? $this->data['max_page'] : (int)$page);
		$start = ($this->data['page'] - 1) * $limit;

		// Query the database for post data
		$this->load($this->dbh->query($this->generateQuery(). "\r\n". 'LIMIT '. $start. ', '. $limit));
	}

	/*
		Function: load

		Processes the posts from the specified query resource.

		Parameters:
			resource $request

		Returns:
			void
	*/
	private function load($request)
	{
		// No need to load the users data over and over again, so we'll do it
		// once later. The same goes for categories.
		$users = array();
		$categories = array();
		$this->data['posts'] = array();
		while($row = $request->fetch(SQLITE_ASSOC))
		{
			$this->data['posts'][] = array(
																 'id' => $row['post_id'],
																 'title' => $row['post_title'],
																 'short_name' => $row['short_name'],
																 'date' => date('F j, Y', $row['post_date']),
																 'timestamp' => $row['post_date'],
																 'published' => $row['published'],
																 'author' => array(
																							 'id' => $row['author_id'],
																							 'name' => $row['author_name'],
																						 ),
																 'text' => $row['post_text'],
																 'categories' => explode(',', $row['categories']),
																 'allow_comments' => !empty($row['allow_comments']),
																 'allow_pingbacks' => !empty($row['allow_pingbacks']),
																 'comments' => $row['comments'],
															 );

			// Add the author's ID to the list to load.
			$users[] = $row['author_id'];

			// Same goes for the categories.
			$categories = array_merge($categories, explode(',', $row['categories']));
		}

		// Now load the user data.
		users_load($users);

		// Then all the category information. But first make sure the ID's are
		// all safe.
		$categories = array_unique($categories);
		foreach($categories as $key => $category_id)
		{
			$categories[$key] = (int)$category_id;
		}

		$request = $this->dbh->query("
			SELECT
				category_id, short_name, full_name, category_text
			FROM categories
			WHERE category_id IN(". implode(', ', $categories). ")");

		$this->data['categories'] = array();
		while($row = $request->fetch(SQLITE_ASSOC))
		{
			$this->data['categories'][$row['category_id']] = array(
																												 'id' => $row['category_id'],
																												 'name' => $row['full_name'],
																												 'short_name' => $row['short_name'],
																												 'text' => $row['category_text'],
																												 'href' => get_bloginfo('url'). 'index.php?category='. $row['category_id'],
																												 'url' => '<a href="'. get_bloginfo('url'). 'index.php?category='. $row['category_id']. '" title="'. (utf_strlen($row['category_text']) > 255 ? utf_substr($row['category_text'], 0, 252). '...' : $row['category_text']). '">'. $row['full_name']. '</a>',
																											 );
		}

		// Make sure our pointer in the posts array is at the beginning. That is
		// unless we don't have any posts.
		if(count($this->data['posts']) > 0)
		{
			reset($this->data['posts']);
			$this->current = key($this->data['posts']);
		}
		else
		{
			$this->data['posts'] = null;
			$this->data['count'] = 0;
			$this->current = null;
		}
	}

	/*
		Function: has_posts

		Determines whether there are more posts to iterate through if $count is
		false, but otherwise returns the total number of posts currently loaded.

		Parameters:
			bool $count - Whether to return the number of posts loaded.

		Returns:

			Boolean value (true/false).
	*/
	public function has_posts($count = false)
	{
		if(!empty($count))
		{
			return $this->data['posts'] !== null ? count($this->data['posts']) : 0;
		}

		// Do we have any posts?
		if($this->data['posts'] !== null && $this->current !== null)
		{
			// Save the current location.
			$this->current = key($this->data['posts']);
			$this->post = $this->current !== null ? $this->data['posts'][$this->current] : null;

			// Move us along, for the next time.
			next($this->data['posts']);

			return $this->current !== null;
		}
		else
		{
			// Nope, no posts.
			return false;
		}
	}

	/*
		Function: permalink

		Outputs the permanent URL (or permalink) to the current post.
	*/
	public function permalink()
	{
		// We didn't screw up and keep an empty query, did we?
		if($this->post !== null)
		{
			// Nope, so return the post's permalink
			echo get_bloginfo('url'). '?post='. $this->post['id'];
		}
		else
		{
			// Looks like we messed up, send nothing
			return false;
		}
	}

	/*
		Function: title

		Outputs the title of the current post.
	*/
	public function title()
	{
		// We didn't screw up and keep an empty query, did we?
		if($this->post !== null)
		{
			// Nope, so remove all sanitation and echo it out
			echo $this->post['title'];
		}
		else
		{
			// Looks like we messed up, send nothing
			return false;
		}
	}

	/*
		Function: content

		Outputs the content, in full form or as an excerpt.

		Parameters:

			ending - The excerpt suffix. If set, this function will output an excerpt. (e.g. Read More...)
	*/
	public function content($ending = false)
	{
		// We didn't screw up and keep an empty query, did we?
		if($this->post !== null)
		{
			$text = $this->post['text'];
			$length = 360;

			// The following truncator code is from CakePHP
			// http://www.cakephp.org/
			// Licensed under the MIT license

			// if the plain text is shorter than the maximum length, return the whole text
			if(utf_strlen(preg_replace('/<.*?>/', '', $text)) <= $length || !$ending)
			{
				echo $text;
			}
			else
			{
				// splits all html-tags to scanable lines
				preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
				$total_length = strlen($ending);
				$open_tags = array();
				$truncate = '';

				foreach ($lines as $line_matchings)
				{
					// if there is any html-tag in this line, handle it and add it (uncounted) to the output
					if (!empty($line_matchings[1]))
					{
						// if it's an "empty element" with or without xhtml-conform closing slash
						if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1]))
						{
							// do nothing
						}
						// if tag is a closing tag
						else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings))
						{
							// delete tag from $open_tags list
							$pos = array_search($tag_matchings[1], $open_tags);
							if ($pos !== false)
							{
								unset($open_tags[$pos]);
							}
						}
						// if tag is an opening tag
						else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings))
						{
							// add tag to the beginning of $open_tags list
							array_unshift($open_tags, utf_strtolower($tag_matchings[1]));
						}

						// add html-tag to $truncate'd text
						$truncate .= $line_matchings[1];
					}
					// calculate the length of the plain text part of the line; handle entities as one character
					$content_length = utf_strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));

					if ($total_length + $content_length > $length)
					{
						// the number of characters which are left
						$left = $length - $total_length;
						$entities_length = 0;

						// search for html entities
						if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE))
						{
							// calculate the real length of all entities in the legal range
							foreach ($entities[0] as $entity)
							{
								if ($entity[1] + 1 - $entities_length <= $left) {
									$left--;
									$entities_length += strlen($entity[0]);
								}
								else
								{
									// no more characters left
									break;
								}
							}
						}

						$truncate .= substr($line_matchings[2], 0, $left + $entities_length);

						// maximum lenght is reached, so get off the loop
						break;
					}
					else
					{
						$truncate .= $line_matchings[2];
						$total_length += $content_length;
					}
					// if the maximum length is reached, get off the loop
					if($total_length >= $length)
					{
						break;
					}
				}

				// ...search the last occurance of a space...
				$spacepos = utf_strrpos($truncate, ' ');

				if (isset($spacepos))
				{
					// ...and cut the text in this position
					$truncate = utf_substr($truncate, 0, $spacepos);
				}
				// close all unclosed html-tags
				foreach ($open_tags as $tag)
				{
					$truncate .= '</' . $tag . '>';
				}

				if($ending)
				{
					// add the defined ending to the text
					$truncate .= '... <a href="?post='. $this->post['id']. '">'. $ending. '</a>';
				}

				// and echo
				echo $truncate;
			}
		}
		// Oh no, we screwed up :(
		else
		{
			// Send nothing back
			return false;
		}
	}

	/*
		Function: date

		Outputs the published date of the post.

		Parameters:

			format - The format, in PHP's date() format, in which to display the date. (e.g. F js, Y)
	*/
	public function date($format = null)
	{
		// We didn't screw up and keep an empty query, did we?
		if($this->post !== null)
		{
			// Nope, so output the date in the right format
			echo !empty($format) ? date($format, $this->post['timestamp']) : $this->post['date'];
		}
		// Oh no, we screwed up :(
		else
		{
			// Send nothing back
			return false;
		}
	}

	/*
		Function: author

		Outputs the author of the current post.
	*/
	public function author()
	{
		if($this->post !== null)
		{
			// Does the user currently exist?
			if(($user = users_get($this->post['author']['id'])) !== false)
			{
				echo $user['name'];
			}
			else
			{
				// We will use the saved name, then.
				echo $this->post['author']['name'];
			}
		}
		else
		{
			return false;
		}
	}

	/*
		Function: commentNum

		Outputs the number of comments on the current post.
	*/
	public function commentNum()
	{
		if($this->post !== null)
		{
			return $this->post['comments'];
		}
		else {
			return false;
		}
	}

	/*
		Function: category

		Outputs the category the post was filed under.
	*/
	public function category()
	{
		if($this->post !== null)
		{
			$categories = array();
			foreach($this->post['categories'] as $category_id)
			{
				if(!isset($this->data['categories'][$category_id]))
				{
					continue;
				}

				$categories[] = $this->data['categories'][$category_id]['url'];
			}

			echo count($categories) == 0 ? l('No categories') : implode(', ', $categories);
		}
		else
		{
			return false;
		}
	}

	/*
		Destructor: __destruct

		Displays simple paginations link when the PostLoop class is destroyed with unset().
	*/
	public function pagination()
	{
		if($GLOBALS['postquery']['type'] != 'post' && $GLOBALS['postquery']['type'] != 'page')
		{
			global $file;

			echo '<div class="pagination">';

			// Set various required variables
			$prev = $this->data['page'] - 1;						// Previous page is page - 1
			$next = $this->data['page'] + 1;						// Next page is page + 1

			// Set $pagination
			$pagination = "";

			// Add the 'Newer Posts' link if we're beyond the first page.
			if($this->data['page'] > 1)
			{
				$pagination .= "<a href=\"".$file."?p=".$prev."\" class=\"next\">Newer Posts &raquo;</a>";
			}

			// Add 'Older Posts' link if the next page exists.
			if($next <= $this->data['max_page'])
			{
				$pagination .= "<a href=\"".$file."?p=".$next."\" class=\"prev\">&laquo; Older Posts</a>";
			}

			// Return the links! Duh!
			echo $pagination.'</div>';
		}
	}
}
?>