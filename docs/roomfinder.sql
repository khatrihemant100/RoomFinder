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
  train_station varchar(255) DEFAULT NULL,
  status varchar(50) DEFAULT 'available',
  description text DEFAULT NULL,
  image_url varchar(255) DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (id),
  KEY user_id (user_id),
  KEY idx_properties_status (status),
  KEY idx_properties_location (location),
  KEY idx_properties_price (price),
  CONSTRAINT properties_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id)
);

-- CREATE TABLE: inquiries
CREATE TABLE inquiries (
  id int(11) NOT NULL AUTO_INCREMENT,
  room_id int(11) DEFAULT NULL,
  name varchar(100) DEFAULT NULL,
  email varchar(100) DEFAULT NULL,
  phone varchar(50) DEFAULT NULL,
  visit_date date DEFAULT NULL,
  message text DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (id),
  KEY room_id (room_id),
  CONSTRAINT inquiries_ibfk_1 FOREIGN KEY (room_id) REFERENCES properties (id) ON DELETE CASCADE
);
