<?php session_start();

/*********************************************

	LightBlog 0.9
	SQLite blogging platform
	
	feed.php
	
	©2009 soren121. All rights reserved.
	Released under the GNU General
	Public License. For all licensing
	information, please see the
	LICENSE.txt document included in this
	distribution.

*********************************************/

// Include config and FeedWriter library
require('config.php');
require(ABSPATH .'/Sources/Core.php');
require(ABSPATH .'/Sources/FeedWriter.php');

// Check requested feed type
if(!isset($_GET['type'])):
	// Default to RSS
	$type = 'rss';
else:
$type = strtolower($_GET['type']);
endif;

// Creating an instance of FeedWriter class. 
if($type == 'atom'):
	$TestFeed = new FeedWriter(ATOM);
	
elseif($type == 'rss'):
	$TestFeed = new FeedWriter(RSS2);	
endif;

// Setting the channel elements
// Use wrapper functions for common elements
$TestFeed->setTitle('Syndication feed for '.bloginfo('title'));
$TestFeed->setLink(bloginfo('url').'rss.php');
	
// For other channel elements, use setChannelElement() function
if($type == 'atom'):
	$TestFeed->setChannelElement('updated', date(DATE_ATOM , time()));
	$TestFeed->setChannelElement('author', array('name'=>'Anis uddin Ahmad'));
elseif($type == 'rss'):
	$TestFeed->setChannelElement('pubDate', date(RFC822, time()));
endif;

// Adding items to feed. Generally this protion will be in a loop and add all feeds.
$result = $dbh->query("SELECT * FROM posts ORDER BY id desc") or die(sqlite_error_string($dbh->lastError));

while($row = $result->fetch(SQLITE_ASSOC)) {
	// Create a FeedItem
	$newItem = $TestFeed->createNewItem();
		
	// Add elements to the feed item    
	$newItem->setTitle($row['title']);
	$newItem->setLink(bloginfo('url', 'r').'post.php?id='.$row['id']);
	$newItem->setDescription($row['post']);
	$newItem->setDate(date(RFC822, $row['date']));
	$newItem->addElement('author', $row['author']);
	$newItem->addElement('guid', bloginfo('url', 'r').'post.php?id='.$row['id'], array('isPermaLink'=>'true'));
		
	// Now add the feed item
	$TestFeed->addItem($newItem);
}

// OK. Everything is done. Now generate the feed.
$TestFeed->generateFeed();

?>