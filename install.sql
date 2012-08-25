CREATE TABLE 'categories'
(
	'category_id' INTEGER PRIMARY KEY NOT NULL DEFAULT '0',
	'short_name' TEXT NOT NULL,
	'full_name' TEXT NOT NULL,
	'category_text' TEXT NOT NULL
);

CREATE UNIQUE INDEX 'categories_shortname_index' ON 'categories' ('short_name');

INSERT INTO 'categories' VALUES(1, 'uncategorized', 'Uncategorized', 'Posts with no appropriate category are filed here.');

CREATE TABLE 'comments'
(
	'comment_id' INTEGER PRIMARY KEY NOT NULL DEFAULT '0',
	'post_id' INTEGER NOT NULL,
	'comment_type' VARCHAR(15) NOT NULL DEFAULT 'comment',
	'published' TINYINT(1) NOT NULL DEFAULT '1',
	'commenter_id' INTEGER NOT NULL DEFAULT '0',
	'commenter_name' VARCHAR(255) NOT NULL,
	'commenter_email' VARCHAR(255) NOT NULL,
	'commenter_website' VARCHAR(255) NULL,
	'commenter_ip' VARCHAR(255) NOT NULL,
	'comment_date' INT(10) NOT NULL,
	'comment_text' TEXT NOT NULL,
	'spam' TINYINT(1) NOT NULL DEFAULT '0'
);

CREATE INDEX 'comments_pid_index' ON 'comments' ('post_id');
CREATE INDEX 'comments_published_index' ON 'comments' ('published');
CREATE INDEX 'comments_author_index' ON 'comments' ('commenter_id');
CREATE INDEX 'comments_ip_index' ON 'comments' ('commenter_ip');
CREATE INDEX 'comments_date_index' ON 'comments' ('comment_date');
CREATE INDEX 'comments_spam_index' ON 'comments' ('spam');

CREATE TABLE 'settings'
(
	'variable' VARCHAR(255) NOT NULL,
	'value' TEXT NULL
);

CREATE UNIQUE INDEX 'settings_variable_index' ON 'settings' ('variable');

INSERT INTO 'settings' VALUES('theme','default');
INSERT INTO 'settings' VALUES('comment_moderation', 'none');
INSERT INTO 'settings' VALUES('timezone', '0.0');
INSERT INTO 'settings' VALUES('date_format', 'm/j/Y');
INSERT INTO 'settings' VALUES('time_format', 'g:i a');

CREATE TABLE 'errors'
(
	'error_id' INTEGER PRIMARY KEY NOT NULL DEFAULT '0',
	'error_time' INT(10) NOT NULL,
	'error_type' INT NOT NULL DEFAULT '0',
	'error_message' TEXT NOT NULL,
	'error_file' VARCHAR(255) NOT NULL,
	'error_line' INT NOT NULL DEFAULT '0',
	'error_url' VARCHAR(255) NOT NULL
);

CREATE INDEX 'errors_type_index' ON 'errors' ('error_type');
CREATE INDEX 'errors_time_index' ON 'errors' ('error_time');

CREATE TABLE 'pages'
(
	'page_id' INTEGER PRIMARY KEY NOT NULL DEFAULT '0',
	'page_title' VARCHAR(255) NOT NULL,
	'short_name' VARCHAR(255) NOT NULL,
	'page_date' INT(10) NOT NULL DEFAULT '0',
	'published' TINYINT(1) NOT NULL DEFAULT '1',
	'author_name' VARCHAR(100) NOT NULL,
	'author_id' INTEGER NOT NULL DEFAULT '0',
	'page_text' TEXT NOT NULL
);

CREATE UNIQUE INDEX 'pages_shortname_index' ON 'pages' ('short_name');
CREATE INDEX 'pages_published_index' ON 'pages' ('published');
CREATE INDEX 'pages_author_index' ON 'pages' ('author_id');

INSERT INTO 'pages' VALUES(1, 'About', 'about', strftime('%s', 'now'), 1, 'LightBlog Dev Team', 0, 'This is a page. It works like a post, but it lives outside of the hierarchic world of posts. You can edit and delete it in the admin panel.');

CREATE TABLE 'posts'
(
	'post_id' INTEGER PRIMARY KEY NOT NULL DEFAULT '0',
	'post_title' VARCHAR(255) NOT NULL,
	'short_name' VARCHAR(255) NOT NULL,
	'post_date' INT(10) NOT NULL DEFAULT '0',
	'published' INT(10) NOT NULL DEFAULT '0',
	'author_name' VARCHAR(100) NOT NULL,
	'author_id' INTEGER NOT NULL DEFAULT '0',
	'post_text' TEXT NOT NULL,
	'categories' VARCHAR(255) NOT NULL,
	'allow_comments' TINYINT(1) NOT NULL DEFAULT '1',
	'allow_pingbacks' TINYINT(1) NOT NULL DEFAULT '1',
	'comments' INT NOT NULL DEFAULT '0'
);

CREATE UNIQUE INDEX 'posts_shortname_index' ON 'posts' ('short_name');
CREATE INDEX 'posts_date_index' ON 'posts' ('post_date');
CREATE INDEX 'posts_published_index' ON 'posts' ('published');
CREATE INDEX 'posts_author_index' ON 'posts' ('author_id');

INSERT INTO 'posts' VALUES(1, 'Hello world!', 'hello-world', strftime('%s', 'now'), 1, 'LightBlog Dev Team', 0, 'Thank you for choosing LightBlog to manage your website. We hope you like it! Feel free to delete this post after you''re all set up. :)<br /><br />-The LightBlog Team<br /><a href="http://lightblog.googlecode.com/">http://lightblog.googlecode.com/</a>', '1', 1, 0, 0);

CREATE TABLE 'post_categories'
(
	'post_id' INTEGER NOT NULL,
	'category_id' INTEGER NOT NULL
);

CREATE UNIQUE INDEX 'post_categories_unique' ON 'post_categories' ('post_id', 'category_id');

INSERT INTO 'post_categories' VALUES(1, 1);

CREATE TABLE 'roles'
(
	'role_id' INTEGER PRIMARY KEY NOT NULL DEFAULT '0',
	'role_name' VARCHAR(100) NOT NULL
);

INSERT INTO 'roles' ('role_id', 'role_name') VALUES(1, 'Administrator');
INSERT INTO 'roles' ('role_id', 'role_name') VALUES(2, 'Editor');
INSERT INTO 'roles' ('role_id', 'role_name') VALUES(3, 'Contributor');
INSERT INTO 'roles' ('role_id', 'role_name') VALUES(4, 'Standard User');

CREATE TABLE 'role_permissions'
(
	'role_id' INTEGER NOT NULL,
	'permission' VARCHAR(50) NOT NULL,
	'status' TINYINT(1) NOT NULL DEFAULT '1'
);

CREATE UNIQUE INDEX 'role_permissions_index' ON 'role_permissions' ('role_id', 'permission');

INSERT INTO role_permissions VALUES ('1','CreatePosts','1');
INSERT INTO role_permissions VALUES ('1','CreatePages','1');
INSERT INTO role_permissions VALUES ('1','CreateCategories','1');
INSERT INTO role_permissions VALUES ('1','EditPosts','1');
INSERT INTO role_permissions VALUES ('1','EditOthersPosts','1');
INSERT INTO role_permissions VALUES ('1','EditPages','1');
INSERT INTO role_permissions VALUES ('1','EditOthersPages','1');
INSERT INTO role_permissions VALUES ('1','EditCategories','1');
INSERT INTO role_permissions VALUES ('1','AddUsers','1');
INSERT INTO role_permissions VALUES ('1','EditOtherUsers','1');
INSERT INTO role_permissions VALUES ('1','EditSettings','1');
INSERT INTO role_permissions VALUES ('1','EditComments','1');
INSERT INTO role_permissions VALUES ('1','AccessMaintenance','1');
INSERT INTO role_permissions VALUES ('1','AccessACP','1');
INSERT INTO role_permissions VALUES ('1','CreateComments','1');
INSERT INTO role_permissions VALUES ('1','EditRoles','1');
INSERT INTO role_permissions VALUES ('2','CreatePosts','1');
INSERT INTO role_permissions VALUES ('2','CreatePages','1');
INSERT INTO role_permissions VALUES ('2','CreateCategories','1');
INSERT INTO role_permissions VALUES ('2','EditPosts','1');
INSERT INTO role_permissions VALUES ('2','EditOthersPosts','1');
INSERT INTO role_permissions VALUES ('2','EditPages','1');
INSERT INTO role_permissions VALUES ('2','EditOthersPages','1');
INSERT INTO role_permissions VALUES ('2','EditCategories','1');
INSERT INTO role_permissions VALUES ('2','AddUsers','0');
INSERT INTO role_permissions VALUES ('2','EditOtherUsers','0');
INSERT INTO role_permissions VALUES ('2','EditSettings','0');
INSERT INTO role_permissions VALUES ('2','EditComments','1');
INSERT INTO role_permissions VALUES ('2','AccessMaintenance','0');
INSERT INTO role_permissions VALUES ('2','AccessACP','1');
INSERT INTO role_permissions VALUES ('2','CreateComments','1');
INSERT INTO role_permissions VALUES ('2','EditRoles','0');
INSERT INTO role_permissions VALUES ('3','CreatePosts','1');
INSERT INTO role_permissions VALUES ('3','CreatePages','1');
INSERT INTO role_permissions VALUES ('3','CreateCategories','1');
INSERT INTO role_permissions VALUES ('3','EditPosts','1');
INSERT INTO role_permissions VALUES ('3','EditOthersPosts','0');
INSERT INTO role_permissions VALUES ('3','EditPages','1');
INSERT INTO role_permissions VALUES ('3','EditOthersPages','0');
INSERT INTO role_permissions VALUES ('3','EditCategories','1');
INSERT INTO role_permissions VALUES ('3','AddUsers','0');
INSERT INTO role_permissions VALUES ('3','EditOtherUsers','0');
INSERT INTO role_permissions VALUES ('3','EditSettings','0');
INSERT INTO role_permissions VALUES ('3','EditComments','1');
INSERT INTO role_permissions VALUES ('3','AccessMaintenance','0');
INSERT INTO role_permissions VALUES ('3','AccessACP','1');
INSERT INTO role_permissions VALUES ('3','CreateComments','1');
INSERT INTO role_permissions VALUES ('3','EditRoles','0');
INSERT INTO role_permissions VALUES ('4','CreatePosts','0');
INSERT INTO role_permissions VALUES ('4','CreatePages','0');
INSERT INTO role_permissions VALUES ('4','CreateCategories','0');
INSERT INTO role_permissions VALUES ('4','EditPosts','0');
INSERT INTO role_permissions VALUES ('4','EditOthersPosts','0');
INSERT INTO role_permissions VALUES ('4','EditPages','0');
INSERT INTO role_permissions VALUES ('4','EditOthersPages','0');
INSERT INTO role_permissions VALUES ('4','EditCategories','0');
INSERT INTO role_permissions VALUES ('4','AddUsers','0');
INSERT INTO role_permissions VALUES ('4','EditOtherUsers','0');
INSERT INTO role_permissions VALUES ('4','EditSettings','0');
INSERT INTO role_permissions VALUES ('4','EditComments','0');
INSERT INTO role_permissions VALUES ('4','AccessMaintenance','0');
INSERT INTO role_permissions VALUES ('4','AccessACP','0');
INSERT INTO role_permissions VALUES ('4','CreateComments','1');
INSERT INTO role_permissions VALUES ('4','EditRoles','0');

CREATE TABLE 'users'
(
	'user_id' INTEGER PRIMARY KEY NOT NULL DEFAULT '0',
	'user_name' VARCHAR(100) NOT NULL,
	'user_pass' VARCHAR(40) NOT NULL,
	'user_email' VARCHAR(255) NOT NULL,
	'display_name' VARCHAR(100) NOT NULL,
	'user_role' INTEGER NOT NULL DEFAULT '3',
	'user_ip' VARCHAR(150) NOT NULL,
	'user_salt' VARCHAR(20) NOT NULL,
	'user_activated' TINYINT(1) NOT NULL DEFAULT '1',
	'user_created' INT(10) NOT NULL DEFAULT '0'
);

CREATE UNIQUE INDEX 'users_name_index' ON 'users' ('user_name');
CREATE UNIQUE INDEX 'users_email_index' ON 'users' ('user_email');
CREATE UNIQUE INDEX 'users_display_index' ON 'users' ('display_name');
CREATE INDEX 'users_activated_index' ON 'users' ('user_activated');
CREATE INDEX 'users_created_index' ON 'users' ('user_created');

CREATE TABLE 'user_data'
(
	'user_id' INTEGER NOT NULL,
	'variable' VARCHAR(255) NOT NULL,
	'value' TEXT NULL
);

CREATE UNIQUE INDEX 'user_data_index' ON 'user_data' ('user_id', 'variable');