<?php
/*
    LightBlog, a PHP/SQLite blogging platform
    Copyright (C) 2008-2016 The LightBlog Team.
    
    feed.php

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

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
