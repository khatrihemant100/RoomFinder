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
  profile_photo varchar(255) DEFAULT NULL,
  is_admin tinyint(1) DEFAULT '0',
  role enum('owner','seeker') DEFAULT 'seeker',
  is_verified TINYINT(1) DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE KEY email (email),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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
  is_approved TINYINT(1) DEFAULT 1,
  description text DEFAULT NULL,
  image_url varchar(255) DEFAULT NULL,
  utilities_cost DECIMAL(10,2) DEFAULT 0.00,
  management_fee DECIMAL(10,2) DEFAULT 0.00,
  deposit DECIMAL(10,2) DEFAULT 0.00,
  key_money DECIMAL(10,2) DEFAULT 0.00,
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

CREATE TABLE IF NOT EXISTS messages (
  id int(11) NOT NULL AUTO_INCREMENT,
  sender_id int(11) NOT NULL,
  receiver_id int(11) NOT NULL,
  room_id int(11) DEFAULT NULL,
  subject VARCHAR(255) DEFAULT NULL,
  message text NOT NULL,
  is_read tinyint(1) DEFAULT 0,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (id),
  KEY sender_id (sender_id),
  KEY receiver_id (receiver_id),
  KEY room_id (room_id),
  KEY is_read (is_read),
  CONSTRAINT messages_ibfk_1 FOREIGN KEY (sender_id) REFERENCES users (id) ON DELETE CASCADE,
  CONSTRAINT messages_ibfk_2 FOREIGN KEY (receiver_id) REFERENCES users (id) ON DELETE CASCADE,
  CONSTRAINT messages_ibfk_3 FOREIGN KEY (room_id) REFERENCES properties (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS admin_settings (
  id INT(11) NOT NULL AUTO_INCREMENT,
  setting_key VARCHAR(100) NOT NULL,
  setting_value TEXT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY setting_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS admin_logs (
  id INT(11) NOT NULL AUTO_INCREMENT,
  admin_id INT(11) NOT NULL,
  action VARCHAR(100) NOT NULL,
  target_type VARCHAR(50) NOT NULL,
  target_id INT(11) DEFAULT NULL,
  details TEXT,
  ip_address VARCHAR(45),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY admin_id (admin_id),
  KEY created_at (created_at),
  FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX idx_messages_conversation ON messages(sender_id, receiver_id, created_at DESC);
CREATE INDEX idx_messages_unread ON messages(receiver_id, is_read, created_at DESC);

CREATE INDEX idx_users_admin ON users(is_admin, role);
CREATE INDEX idx_properties_approved ON properties(is_approved, created_at DESC);
