CREATE TABLE core(
'variable' text NOT NULL,
'value' text NOT NULL);

INSERT INTO core VALUES('theme','default');

CREATE TABLE 'comments'(
'id' INTEGER NOT NULL PRIMARY KEY,
'post_id' INTEGER NOT NULL,
'name' VARCHAR(35) NOT NULL,
'email' VARCHAR(255) NOT NULL,
'website' VARCHAR(255),
'date' INT(10) NOT NULL,
'text' TEXT NOT NULL);

CREATE INDEX 'comments_post_id_index' ON 'comments' ('post_id');

CREATE TABLE 'pages'(
'id' INTEGER NOT NULL PRIMARY KEY DEFAULT '0',
'title' VARCHAR(100) NOT NULL,
'page' TEXT NOT NULL,
'date' INT(10) NOT NULL,
'author' VARCHAR(20) NOT NULL);

CREATE TABLE 'posts'(
'id' INTEGER NOT NULL PRIMARY KEY DEFAULT '0',
'title' VARCHAR(100) NOT NULL,
'post' TEXT NOT NULL,
'date' INT(10) NOT NULL,
'author' VARCHAR(20) NOT NULL);

INSERT INTO 'posts' VALUES(1,'Welcome to LightBlog!','Welcome to LightBlog! We hope you enjoy it!<br /><br />-The LightBlog Team<br />http://lightblog.googlecode.com/',1239321795,'LightBlog Devs');

CREATE TABLE 'users'(
'id' INTEGER NOT NULL PRIMARY KEY DEFAULT '0',
'username' VARCHAR(20) NOT NULL,
'password' VARCHAR(20) NOT NULL,
'email' VARCHAR(255) NOT NULL,
'displayname' VARCHAR(40) NOT NULL,
'role' INTEGER NOT NULL,
'ip' VARCHAR(16) NOT NULL,
'salt' VARCHAR(9) NOT NULL);

CREATE INDEX 'users_username_index' ON 'users' ('username');