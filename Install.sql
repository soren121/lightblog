----
-- {$db_prefix}core table
----
CREATE TABLE '{$db_prefix}core' (
  'variable' VARCHAR(255) NOT NULL,
  'value' TEXT NOT NULL
);
----
-- Index for {$db_prefix}core
----
CREATE UNIQUE INDEX '{$db_prefix}core_index' ON '{$db_prefix}core' ('variable');
----
-- Data for {$db_prefix}core
----
INSERT INTO '{$db_prefix}core' ('variable','value') VALUES('current_theme','default');
INSERT INTO '{$db_prefix}core' ('variable','value') VALUES('current_language','English');
----
-- {$db_prefix}posts table
----
CREATE TABLE '{$db_prefix}posts' (
  'postID' INTEGER NOT NULL PRIMARY KEY,
  'title' TEXT NOT NULL,
  'time' INT(10) NOT NULL,
  'authorID' INT NOT NULL,
  'authorName' VARCHAR(80) NOT NULL,
  'content' TEXT NOT NULL,
  'categoryID' INT NOT NULL,
  'numComments' INT NOT NULL default '0',
  'isViewable' INT(1) NOT NULL default '1'
);
----
-- Index for {$db_prefix}posts
----
CREATE INDEX '{$db_prefix}posts_index' ON '{$db_prefix}posts' ('postID');