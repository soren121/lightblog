CREATE TABLE categories(
id INTEGER NOT NULL PRIMARY KEY,
title text NOT NULL,
description text NOT NULL);

INSERT INTO categories (title, description) VALUES('Uncategorized','Posts with no specific category go here');

CREATE TABLE coreinfo(
variable text NOT NULL,
value text NOT NULL);

INSERT INTO coreinfo VALUES('theme','default');

CREATE TABLE comments(
id integer primary key,
post_id int(11) NOT NULL default '0',
username text NOT NULL,
email text NOT NULL,
website text NOT NULL,
text text NOT NULL);

CREATE TABLE pages(
id INTEGER NOT NULL PRIMARY KEY DEFAULT '0',
title TEXT NOT NULL,
page TEXT NOT NULL);

CREATE TABLE posts(
id INTEGER NOT NULL PRIMARY KEY DEFAULT '0',
title TEXT NOT NULL,
post TEXT NOT NULL,
date TEXT NOT NULL,
author TEXT NOT NULL,
catid INTEGER NOT NULL default '1');

INSERT INTO posts VALUES(1,'Welcome to LightBlog!','Welcome to LightBlog! We hope you enjoy it!<br /><br />-The LightBlog Team<br />http://lightblog.googlecode.com/',1218324266,'LightBlog Devs',1);

CREATE TABLE users(
id INTEGER NOT NULL PRIMARY KEY DEFAULT '0',
username TEXT NOT NULL,
password TEXT NOT NULL,
email TEXT NOT NULL,
displayname TEXT NOT NULL,
vip INTEGER NOT NULL,
ip TEXT NOT NULL);