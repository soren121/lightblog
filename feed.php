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
if(!isset($_GET['type']) or $_GET['type'] == 'rss'):
	$type = 'rss';
	$TestFeed = new FeedWriter(RSS2);	
elseif($_GET['type'] == 'atom'):
	$type = 'atom';
	$TestFeed = new FeedWriter(ATOM);
endif;

// Setting the channel elements
// Use wrapper functions for common elements
$TestFeed->setTitle('Syndication feed for '.bloginfo('title', 'r'));
$TestFeed->setLink(bloginfo('url', 'r').'rss.php?type='.$type);
	
// For other channel elements, use setChannelElement() function
if($type == 'atom'):
	$TestFeed->setChannelElement('updated', date(DATE_ATOM, time()));
	$TestFeed->setChannelElement('author', array('name'=>'LightBlog')); // temporary
elseif($type == 'rss'):
	$TestFeed->setChannelElement('pubDate', date('D, d M Y H:i:s T', time()));
	$TestFeed->setChannelElement('description', 'RSS2 syndication feed for '.bloginfo('title', 'r'));
endif;

// Adding items to feed. Generally this protion will be in a loop and add all feeds.
$result = $dbh->query("SELECT * FROM posts ORDER BY id desc") or die(sqlite_error_string($dbh->lastError));

while($row = $result->fetch(SQLITE_ASSOC)) {
	// Create a FeedItem
	$newItem = $TestFeed->createNewItem();
		
	// Add elements to the feed item    
	$newItem->setTitle(stripslashes($row['title']));
	$newItem->setLink(bloginfo('url', 'r').'post.php?id='.$row['id']);
	$newItem->setDescription(stripslashes($row['post']));
	// Add RSS-unique elements
	if($type == 'rss'):
		$newItem->addElement('guid', bloginfo('url', 'r').'post.php?id='.$row['id'], array('isPermaLink'=>'true'));
		$newItem->setDate(date('D, d M Y H:i:s T', $row['date']));
	// Add Atom-unique elements
	elseif($type == 'atom'):
		$newItem->addElement('id', bloginfo('url', 'r').'post.php?id='.$row['id']);
		$newItem->setDate(date(DATE_ATOM, $row['date']));
	endif;	
	// Now add the feed item
	$TestFeed->addItem($newItem);
}

// OK. Everything is done. Now generate the feed.
$TestFeed->generateFeed();

?>