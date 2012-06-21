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
$where = (isset($category)) ? "AND category=".$category." " : "";
$result = $dbh->query("SELECT * FROM posts WHERE published=1 $where ORDER BY id desc LIMIT 0, 10") or die(sqlite_error_string($dbh->lastError));

while($row = $result->fetch(SQLITE_ASSOC)) {
	// Create a FeedItem
	$newItem = $TestFeed->createNewItem();

	// Add elements to the feed item
	$newItem->setTitle(stripslashes($row['title']));
	$newItem->setLink(bloginfo('url', 'r').'?post='.$row['id']);
	$newItem->setDescription(stripslashes($row['post']));
	$newItem->setDate(date("D, d M Y h:i:s O", $row['date']));
	// Add RSS-unique elements
	if($type == 'rss') {
		$newItem->addElement('guid', get_bloginfo('url').'?post='.$row['id'], array('isPermaLink'=>'true'));
		header('Content-Type: application/rss+xml');
	}
	// Add Atom-unique elements
	elseif($type == 'atom') {
		$newItem->addElement('id', get_bloginfo('url').'?post='.$row['id']);
		header('Content-Type: application/atom+xml');
	}
	// Now add the feed item
	$TestFeed->addItem($newItem);
}

// OK. Everything is done. Now generate the feed.
$TestFeed->generateFeed();

?>