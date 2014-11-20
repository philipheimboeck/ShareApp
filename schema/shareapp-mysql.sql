DROP SCHEMA IF EXISTS shareapp;
CREATE SCHEMA IF NOT EXISTS shareapp DEFAULT CHARSET=utf8;
USE shareapp;


DROP TABLE IF EXISTS collection;

CREATE TABLE collection (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  label varchar(128) NOT NULL,
  public tinyint(1) NOT NULL DEFAULT 0,
  CONSTRAINT pk_collection PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS user;

CREATE TABLE user (
  id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  email varchar(128) NOT NULL,
  username VARCHAR(64) NOT NULL,
  password varchar(32) NOT NULL,
  salt varchar(32) NOT NULL,
  active tinyint(4) NOT NULL DEFAULT '1',
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT pk_user PRIMARY KEY (id),
  CONSTRAINT uq_username UNIQUE (username),
  CONSTRAINT uq_email UNIQUE (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS friendship;

CREATE TABLE friendship (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  user1 varchar(128) NOT NULL,
  user2 varchar(128) NOT NULL,
  accepted tinyint(11) NOT NULL DEFAULT 0,
  KEY idx_friendship_user_user1 (user1),
  KEY idx_friendship_user_user2 (user2),
  CONSTRAINT pk_friendship PRIMARY KEY (id),
  CONSTRAINT fk_friendship_user_user1 FOREIGN KEY (user1) REFERENCES user (id) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT fk_friendship_user_user2 FOREIGN KEY (user2) REFERENCES user (id) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS share;

CREATE TABLE share (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  content varchar(2048) NOT NULL,
  user varchar(128) NOT NULL,
  collection int(11) NOT NULL,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_share_user_user (user),
  KEY idx_share_collection_collection (collection),
  CONSTRAINT pk_share PRIMARY KEY (id),
  CONSTRAINT fk_share_user_user FOREIGN KEY (user) REFERENCES user(id) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT fk_share_collection_collection FOREIGN KEY (collection) REFERENCES collection(id) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS user_collection;

CREATE TABLE user_collection (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  user varchar(128) NOT NULL,
  collection int(11) NOT NULL,
  is_admin tinyint(4) NOT NULL DEFAULT '0',
  is_default tinyint(4) NOT NULL DEFAULT '0',
  KEY idx_user_collection_user (user),
  KEY idx_user_collection_collection (collection),
  CONSTRAINT pk_user_collection PRIMARY KEY (id),
  CONSTRAINT fk_user_collection_user FOREIGN KEY (user) REFERENCES user(id) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT fk_user_collection_collection FOREIGN KEY (collection) REFERENCES collection(id) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8;