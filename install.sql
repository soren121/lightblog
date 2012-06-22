CREATE TABLE 'categories' (
	'id' INTEGER PRIMARY KEY DEFAULT '0',
	'shortname' TEXT NOT NULL,
	'fullname' TEXT NOT NULL,
	'info' TEXT
);

INSERT INTO categories VALUES(1,'uncategorized','Uncategorized','Posts with no appropriate category are filed here.');

CREATE TABLE 'core' (
	'variable' VARCHAR(255) NOT NULL,
	'value' TEXT
);

CREATE UNIQUE INDEX 'core_variable_index' ON 'core' ('variable');

INSERT INTO core VALUES('theme','default');
INSERT INTO core VALUES('comment_moderation', 'none');
INSERT INTO core VALUES('timezone', '0.0');
INSERT INTO core VALUES('date_format', 'm/j/Y');
INSERT INTO core VALUES('time_format', 'g:i a');

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
CREATE INDEX 'comments_published_index' ON 'comments' ('published');
CREATE INDEX 'comments_spam_index' ON 'comments' ('spam');

CREATE TABLE 'pages' (
	'id' INTEGER PRIMARY KEY NOT NULL DEFAULT '0',
	'title' VARCHAR(100) NOT NULL,
	'page' TEXT NOT NULL,
	'date' INT(10) NOT NULL,
	'author' VARCHAR(20) NOT NULL,
	'published' INT(1) DEFAULT '1'
);

CREATE INDEX 'pages_published_index' ON 'pages' ('published');

INSERT INTO pages VALUES(1,'About','This is a page. It works like a post, but it lives outside of the hierarchic world of posts. You can edit and delete it in the admin panel.',strftime('%s', 'now'),'LightBlog Devs',1);

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

CREATE INDEX 'posts_published_index' ON 'posts' ('published');

INSERT INTO posts VALUES(1,'Hello world!','Thank you for choosing LightBlog to manage your website. We hope you like it! Feel free to delete this post after you''re all set up. :)<br /><br />-The LightBlog Team<br />http://lightblog.googlecode.com/',strftime('%s', 'now'),'LightBlog Devs',1,1,1);

CREATE TABLE 'users' (
	'id' INTEGER PRIMARY KEY NOT NULL DEFAULT '0',
	'username' VARCHAR(20) NOT NULL,
	'password' VARCHAR(40) NOT NULL,
	'email' VARCHAR(255) NOT NULL,
	'displayname' VARCHAR(40) NOT NULL,
	'role' INTEGER NOT NULL,
	'ip' VARCHAR(16),
	'salt' VARCHAR(9) NOT NULL
);

CREATE UNIQUE INDEX 'users_username_index' ON 'users' ('username');

CREATE TABLE 'error_log'
(
  'error_id' INTEGER PRIMARY KEY NOT NULL DEFAULT '0',
  'error_time' INT(10) NOT NULL,
  'error_type' INT NOT NULL DEFAULT '0',
  'error_message' TEXT NOT NULL,
  'error_file' VARCHAR(255) NOT NULL,
  'error_line' INT NOT NULL DEFAULT '0',
  'error_url' VARCHAR(255) NOT NULL
);

CREATE INDEX 'error_log_time_index' ON 'error_log' ('error_time');
CREATE INDEX 'error_log_type_index' ON 'error_log' ('error_type');

CREATE TABLE 'roles' (
	'id' INTEGER PRIMARY KEY NOT NULL, 
	'role' VARCHAR(255) NOT NULL, 
	'permissions' TEXT
);

INSERT INTO roles VALUES ('1', 'Standard User', '');
INSERT INTO roles VALUES ('2', 'Editor', '');
INSERT INTO roles VALUES ('3', 'Administrator', '');
