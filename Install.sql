CREATE TABLE '{dbprefix}core' (
  'variable' VARCHAR(255) NOT NULL,
  'value' TEXT NOT NULL
);

CREATE UNIQUE INDEX '{dbprefix}core_index' ON '{dbprefix}core' ('variable');

INSERT INTO '{dbprefix}core' ('variable','value') VALUES('current_theme','default');
INSERT INTO '{dbprefix}core' ('variable','value') VALUES('current_language','English');

CREATE TABLE '{dbprefix}posts' (
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

CREATE INDEX '{dbprefix}posts_index' ON '{dbprefix}posts' ('postID');