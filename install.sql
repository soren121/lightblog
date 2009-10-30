CREATE TABLE banned_users (
	'username' VARCHAR(20) NOT NULL,
	'length' INTEGER NOT NULL,
	'reason' TEXT NOT NULL
);

CREATE TABLE core (
	'variable' TEXT NOT NULL,
	'value' TEXT
);

INSERT INTO core VALUES('theme','default');
INSERT INTO core VALUES('akismet','1');
INSERT INTO core VAlUES('akismet_key','');

CREATE TABLE 'comments' (
	'id' INTEGER PRIMARY KEY DEFAULT '0',
	'pid' INTEGER NOT NULL,
	'name' VARCHAR(35) NOT NULL,
	'email' VARCHAR(255) NOT NULL,
	'website' VARCHAR(255),
	'date' INT(10) NOT NULL,
	'text' TEXT NOT NULL,
	'spam' INTEGER DEFAULT '0'
);

CREATE INDEX 'comments_pid_index' ON 'comments' ('pid');

CREATE TABLE 'pages' (
	'id' INTEGER PRIMARY KEY NOT NULL DEFAULT '0',
	'title' VARCHAR(100) NOT NULL,
	'page' TEXT NOT NULL,
	'date' INT(10) NOT NULL,
	'author' VARCHAR(20) NOT NULL
);

INSERT INTO pages VALUES('1','About','This is a page. It works like a post, but it lives outside of the hierarchic world of posts. You can edit and delete it in the admin panel.','1247356860','LightBlog Devs');

CREATE TABLE 'posts' (
	'id' INTEGER PRIMARY KEY NOT NULL DEFAULT '0',
	'title' VARCHAR(100) NOT NULL,
	'post' TEXT NOT NULL,
	'date' INT(10) NOT NULL,
	'author' VARCHAR(20) NOT NULL
);

INSERT INTO 'posts' VALUES(1,'Hello world!','Thank you for choosing LightBlog to manage your website. We hope you like it! Feel free to delete this post after you''re all set up. :)<br /><br />-The LightBlog Team<br />http://lightblog.googlecode.com/',1247356860,'LightBlog Devs');

CREATE TABLE 'users' (
	'id' INTEGER PRIMARY KEY NOT NULL DEFAULT '0',
	'username' VARCHAR(20) NOT NULL,
	'password' VARCHAR(49) NOT NULL,
	'email' VARCHAR(255) NOT NULL,
	'displayname' VARCHAR(40) NOT NULL,
	'role' INTEGER NOT NULL,
	'ip' VARCHAR(16),
	'salt' VARCHAR(9) NOT NULL
);

CREATE INDEX 'users_username_index' ON 'users' ('username');