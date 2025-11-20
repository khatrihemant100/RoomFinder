-- Add Profile Photo and Messaging System
-- Run this SQL script to add profile photo support and messaging system

USE roomfinder;

-- 1. Add profile_photo column to users table
ALTER TABLE users 
ADD COLUMN profile_photo VARCHAR(255) DEFAULT NULL AFTER role;

-- 2. Create messages table for in-app messaging
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

-- 3. Create index for faster message queries
CREATE INDEX idx_messages_conversation ON messages(sender_id, receiver_id, created_at DESC);
CREATE INDEX idx_messages_unread ON messages(receiver_id, is_read, created_at DESC);

