DROP TABLE IF EXISTS collection;

CREATE TABLE collection (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  label varchar(128) NOT NULL,
  public tinyint(1) NOT NULL DEFAULT 0
);


DROP TABLE IF EXISTS user;

CREATE TABLE user (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  email varchar(128) NOT NULL,
  password varchar(32) NOT NULL,
  salt varchar(32) NOT NULL,
  active tinyint(4) NOT NULL DEFAULT '1',
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT uq_user_email UNIQUE (email)
);


DROP TABLE IF EXISTS friendship;

CREATE TABLE friendship (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  user1 int(11) NOT NULL,
  user2 int(11) NOT NULL,
  user2_approved tinyint(11) NOT NULL DEFAULT 0,
  CONSTRAINT fk_friendship_user_user1 FOREIGN KEY (user1) REFERENCES user (id) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT fk_friendship_user_user2 FOREIGN KEY (user2) REFERENCES user (id) ON DELETE RESTRICT ON UPDATE RESTRICT
)  ;

DROP TABLE IF EXISTS share;

CREATE TABLE share (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  label varchar(256),
  description varchar(2048) DEFAULT NULL,
  url varchar(2000) DEFAULT NULL,
  user int(11) NOT NULL,
  collection int(11) NOT NULL,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

  CONSTRAINT fk_share_user_user FOREIGN KEY (user) REFERENCES user(id) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT fk_share_collection_collection FOREIGN KEY (collection) REFERENCES collection(id) ON DELETE RESTRICT ON UPDATE RESTRICT
)  ;


DROP TABLE IF EXISTS user_collection;

CREATE TABLE user_collection (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  user int(11) NOT NULL,
  collection int(11) NOT NULL,
  is_admin tinyint(4) NOT NULL DEFAULT '0',

  CONSTRAINT fk_user_collection_user FOREIGN KEY (user) REFERENCES user(id) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT fk_user_collection_collection FOREIGN KEY (collection) REFERENCES collection(id) ON DELETE RESTRICT ON UPDATE RESTRICT
)  ;