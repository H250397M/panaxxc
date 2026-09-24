-- =========================================================================
-- ICS 2102 — Web Development Semester Mini Project
-- Database Schema: Innovate Club Member Management System
-- File: php/database.sql
-- =========================================================================

-- Create Database if not already present
CREATE DATABASE IF NOT EXISTS `innovate_club_db`
DEFAULT CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE `innovate_club_db`;

-- Table structure for club member applications and contact submissions
DROP TABLE IF EXISTS `club_members`;

CREATE TABLE `club_members` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `fullname` VARCHAR(150) NOT NULL COMMENT 'Student full name',
  `email` VARCHAR(150) NOT NULL COMMENT 'University or personal email',
  `phone` VARCHAR(30) NOT NULL COMMENT 'Contact phone number',
  `course` VARCHAR(100) NOT NULL COMMENT 'Academic department or degree program',
  `year_of_study` VARCHAR(20) DEFAULT 'Year 1' COMMENT 'Year of study (Year 1 to Year 4+)',
  `interest` VARCHAR(100) DEFAULT 'Web Development' COMMENT 'Technical interest domain',
  `message` TEXT NOT NULL COMMENT 'Reason for joining or message to committee',
  `status` ENUM('pending', 'approved', 'contacted') DEFAULT 'pending',
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_email` (`email`),
  INDEX `idx_course` (`course`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample Seed Data
INSERT INTO `club_members` (`fullname`, `email`, `phone`, `course`, `year_of_study`, `interest`, `message`, `status`) 
VALUES
('Alex Kiprono', 'alex.kip@strathmore.edu', '+254 712 345 678', 'Computer Science', 'Year 2', 'Web Development', 'Looking to collaborate on open-source web applications and participate in the upcoming campus hackathon.', 'approved'),
('Faith Muthoni', 'faith.m@uonbi.ac.ke', '+254 723 456 789', 'Software Engineering', 'Year 3', 'AI & Machine Learning', 'Excited about the sustainable AI hackathon and wanting to contribute as an IoT designer.', 'approved'),
('Brian Ochieng', 'brian.och@jkuat.ac.ke', '+254 734 567 890', 'Information Technology', 'Year 1', 'Cybersecurity', 'First year IT student eager to learn from senior students and attend coding bootcamps.', 'pending');
