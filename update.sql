/* Update SQL - run this on your 0.9.2 database to update it for 0.9.3 */

DELETE FROM core WHERE variable='akismet';
DELETE FROM core WHERE variable='akismet_key';

CREATE TEMPORARY TABLE 'comments_updbak' (
	'id' INTEGER PRIMARY KEY DEFAULT '0',
	'pid' INTEGER NOT NULL,
	'name' VARCHAR(35) NOT NULL,
	'email' VARCHAR(255) NOT NULL,
	'website' VARCHAR(255),
	'date' INT(10) NOT NULL,
	'text' TEXT NOT NULL,
	'spam' INT(1) DEFAULT '0'
);
INSERT INTO comments_updbak SELECT * FROM comments;
DROP TABLE comments;
CREATE TABLE 'comments' (
	'id' INTEGER PRIMARY KEY DEFAULT '0',
	'published' INT(1) DEFAULT '1',
	'pid' INTEGER NOT NULL,
	'name' VARCHAR(35) NOT NULL,
	'email' VARCHAR(255) NOT NULL,
	'website' VARCHAR(255),
	'date' INT(10) NOT NULL,
	'text' TEXT NOT NULL,
	'spam' INT(1) DEFAULT '0'
);
CREATE INDEX 'comments_pid_index' ON 'comments' ('pid');
INSERT INTO comments SELECT * FROM comments_updbak;
DROP TABLE comments_updbak;

CREATE TEMPORARY TABLE 'pages_updbak' (
	'id' INTEGER PRIMARY KEY NOT NULL DEFAULT '0',
	'title' VARCHAR(100) NOT NULL,
	'page' TEXT NOT NULL,
	'date' INT(10) NOT NULL,
	'author' VARCHAR(20) NOT NULL
);
INSERT INTO pages_updbak SELECT * FROM pages;
DROP TABLE pages;
CREATE TABLE 'pages' (
	'id' INTEGER PRIMARY KEY NOT NULL DEFAULT '0',
	'title' VARCHAR(100) NOT NULL,
	'page' TEXT NOT NULL,
	'date' INT(10) NOT NULL,
	'author' VARCHAR(20) NOT NULL,
	'published' INT(1) DEFAULT '1'
);
INSERT INTO pages SELECT * FROM pages_updbak;
DROP TABLE pages_updbak;

CREATE TEMPORARY TABLE 'posts_updbak' (
	'id' INTEGER PRIMARY KEY NOT NULL DEFAULT '0',
	'title' VARCHAR(100) NOT NULL,
	'page' TEXT NOT NULL,
	'date' INT(10) NOT NULL,
	'author' VARCHAR(20) NOT NULL,
	'category' INTEGER NOT NULL
);
INSERT INTO posts_updbak SELECT * FROM posts;
DROP TABLE posts;
CREATE TABLE 'posts' (
	'id' INTEGER PRIMARY KEY NOT NULL DEFAULT '0',
	'title' VARCHAR(100) NOT NULL,
	'post' TEXT NOT NULL,
	'date' INT(10) NOT NULL,
	'author' INTEGER NOT NULL,
	'published' INT(1) DEFAULT '1',
	'category' INTEGER NOT NULL,
	'comments' INT(1) DEFAULT '1'
);
INSERT INTO posts SELECT * FROM posts_updbak;
DROP TABLE posts_updbak;