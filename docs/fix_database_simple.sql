-- Simple Database Fix Script
-- Run this in phpMyAdmin - Select roomfinder database first

USE roomfinder;

-- Add train_station column to properties table
ALTER TABLE properties 
ADD COLUMN train_station VARCHAR(255) DEFAULT NULL;

-- Add status column to properties table  
ALTER TABLE properties 
ADD COLUMN status VARCHAR(50) DEFAULT 'available';

-- Create inquiries table
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

-- Add indexes
CREATE INDEX idx_properties_status ON properties(status);
CREATE INDEX idx_properties_location ON properties(location);
CREATE INDEX idx_properties_price ON properties(price);

