<?php
/*********************************************

	LightBlog 0.9
	SQLite blogging platform

	feed.php

	©2008-2012 The LightBlog Team. All
	rights reserved. Released under the
	GNU General Public License 3. For
	all licensing information, please
	see the LICENSE.txt document
	included in this distribution.

*********************************************/

// Include config and FeedWriter library
require('Sources/Core.php');
require(ABSPATH .'/Sources/Class.FeedItem.php');
require(ABSPATH .'/Sources/Class.FeedWriter.php');

// Check requested feed type
if(!isset($_GET['type']) or $_GET['type'] == 'rss') {
	$type = 'rss';
	$TestFeed = new FeedWriter(RSS2);
}
elseif($_GET['type'] == 'atom') {
	$type = 'atom';
	$TestFeed = new FeedWriter(ATOM);
}

if(isset($_GET['category'])) {
	$category = (int)$_GET['category'];
}

// Setting the channel elements
// Use wrapper functions for common elements
$TestFeed->setTitle('Syndication feed for '.get_bloginfo('title'));

if($type == 'rss') {
	$TestFeed->setLink(get_bloginfo('url').'feed.php');
}
else {
	$TestFeed->setLink(get_bloginfo('url').'feed.php?type=atom');
}

// For other channel elements, use setChannelElement() function
if($type == 'atom') {
	$TestFeed->setChannelElement('updated', date(DATE_ATOM, time()));
	$TestFeed->setChannelElement('author', 'LightBlog');
}
elseif($type == 'rss') {
	$TestFeed->setChannelElement('pubDate', date("D, d M Y h:i:s O", time()));
	$TestFeed->setChannelElement('description', 'RSS2 syndication feed for '.get_bloginfo('title'));
}


// Adding items to feed. Generally this portion will be in a loop and add all feeds.
$result = $dbh->query("
	SELECT
		p.*
	FROM posts AS p". (isset($category) ? '
		INNER JOIN post_categories AS pc ON pc.post_id = p.post_id AND pc.category_id = '. $category : ''). "
	WHERE p.published <= ". time(). "
	ORDER BY p.post_date DESC
	LIMIT 10");

while($row = $result->fetch(PDO::FETCH_ASSOC)) {
	// Create a FeedItem
	$newItem = $TestFeed->createNewItem();

	// Add elements to the feed item
	$newItem->setTitle(stripslashes($row['post_title']));
	$newItem->setLink(get_bloginfo('url').'?post='.$row['post_id']);
	$newItem->setDescription(stripslashes($row['post_text']));
	$newItem->setDate(date("D, d M Y h:i:s O", $row['post_date']));
	// Add RSS-unique elements
	if($type == 'rss') {
		$newItem->addElement('guid', get_bloginfo('url').'?post='.$row['post_id'], array('isPermaLink'=>'true'));
		header('Content-Type: application/rss+xml');
	}
	// Add Atom-unique elements
	elseif($type == 'atom') {
		$newItem->addElement('id', get_bloginfo('url').'?post='.$row['post_id']);
		header('Content-Type: application/atom+xml');
	}
	// Now add the feed item
	$TestFeed->addItem($newItem);
}

// OK. Everything is done. Now generate the feed.
$TestFeed->generateFeed();

?>