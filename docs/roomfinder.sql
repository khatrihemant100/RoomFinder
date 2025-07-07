-- DROP TABLES IF EXIST (to avoid errors on restore)
DROP TABLE IF EXISTS properties;
DROP TABLE IF EXISTS rooms;
DROP TABLE IF EXISTS contacts;
DROP TABLE IF EXISTS users;

-- CREATE TABLE: users
CREATE TABLE users (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(100) DEFAULT NULL,
  email varchar(100) DEFAULT NULL,
  password varchar(255) DEFAULT NULL,
  role enum('owner','seeker') DEFAULT 'seeker',
  PRIMARY KEY (id),
  UNIQUE KEY email (email)
);

-- CREATE TABLE: contacts
CREATE TABLE contacts (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(100) DEFAULT NULL,
  email varchar(100) DEFAULT NULL,
  message text DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (id)
);

-- CREATE TABLE: rooms
CREATE TABLE rooms (
  id int(11) NOT NULL AUTO_INCREMENT,
  user_id int(11) DEFAULT NULL,
  title varchar(255) DEFAULT NULL,
  location varchar(255) DEFAULT NULL,
  price int(11) DEFAULT NULL,
  type varchar(50) DEFAULT NULL,
  description text DEFAULT NULL,
  image varchar(255) DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (id)
);

-- CREATE TABLE: properties
CREATE TABLE properties (
  id int(11) NOT NULL AUTO_INCREMENT,
  user_id int(11) DEFAULT NULL,
  title varchar(255) DEFAULT NULL,
  location varchar(255) DEFAULT NULL,
  price decimal(10,2) DEFAULT NULL,
  type varchar(50) DEFAULT NULL,
  description text DEFAULT NULL,
  image_url varchar(255) DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (id),
  KEY user_id (user_id),
  CONSTRAINT properties_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id)
);
