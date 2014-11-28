-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Nov 28, 2014 at 03:59 AM
-- Server version: 5.6.21
-- PHP Version: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `scholarbowl`
--

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE IF NOT EXISTS `classes` (
`id` bigint(20) unsigned NOT NULL,
  `name` varchar(128) NOT NULL,
  `instructor` varchar(128) NOT NULL,
  `total_pts` smallint(5) unsigned NOT NULL,
  `student` bigint(20) unsigned NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Triggers `classes`
--
DELIMITER //
CREATE TRIGGER `add_class` AFTER INSERT ON `classes`
 FOR EACH ROW INSERT INTO classes_audit
    SET action = 'insert',
     c_name = NEW.name,
     c_id = NEW.id,
     c_instructor = NEW.instructor,
     c_total_pts = NEW.total_pts,
     c_student = NEW.student,
        changedon = NOW()
//
DELIMITER ;
DELIMITER //
CREATE TRIGGER `delete_class` BEFORE DELETE ON `classes`
 FOR EACH ROW INSERT INTO classes_audit
    SET action = 'delete',
     c_name = OLD.name,
     c_id = OLD.id,
     c_instructor = OLD.instructor,
     c_total_pts = OLD.total_pts,
     c_student = OLD.student,
        changedon = NOW()
//
DELIMITER ;
DELIMITER //
CREATE TRIGGER `update_class` BEFORE UPDATE ON `classes`
 FOR EACH ROW INSERT INTO classes_audit
    SET action = 'update',
     c_name = OLD.name,
     c_id = OLD.id,
     c_instructor = OLD.instructor,
     c_total_pts = OLD.total_pts,
     c_student = OLD.student,
        changedon = NOW()
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `classes_audit`
--

CREATE TABLE IF NOT EXISTS `classes_audit` (
`id` bigint(20) unsigned NOT NULL,
  `c_id` bigint(20) unsigned NOT NULL,
  `c_instructor` varchar(128) NOT NULL,
  `c_name` varchar(128) NOT NULL,
  `c_student` bigint(20) unsigned NOT NULL,
  `c_total_pts` smallint(5) unsigned NOT NULL,
  `action` varchar(50) NOT NULL,
  `changedOn` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE IF NOT EXISTS `grades` (
`id` bigint(20) unsigned NOT NULL,
  `category` bigint(20) unsigned NOT NULL,
  `points_earned` float unsigned NOT NULL,
  `max_points` float unsigned NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `grade_categories`
--

CREATE TABLE IF NOT EXISTS `grade_categories` (
`id` bigint(20) unsigned NOT NULL,
  `name` varchar(64) NOT NULL,
  `class` bigint(20) unsigned NOT NULL,
  `max_points` smallint(5) unsigned NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

--
-- Triggers `grade_categories`
--
DELIMITER //
CREATE TRIGGER `class_total_points` AFTER INSERT ON `grade_categories`
 FOR EACH ROW BEGIN
	UPDATE classes SET total_pts = (total_pts+NEW.max_points) WHERE classes.id =  NEW.class;
END
//
DELIMITER ;
DELIMITER //
CREATE TRIGGER `class_total_points_sub` AFTER DELETE ON `grade_categories`
 FOR EACH ROW BEGIN
	UPDATE classes SET total_pts = (total_pts-OLD.max_points) WHERE classes.id = OLD.class;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `orgs`
--

CREATE TABLE IF NOT EXISTS `orgs` (
`id` bigint(20) unsigned NOT NULL,
  `school` bigint(20) NOT NULL DEFAULT '0',
  `name` varchar(128) NOT NULL,
  `address` varchar(128) NOT NULL,
  `city` varchar(128) NOT NULL,
  `state` varchar(2) NOT NULL,
  `zip` int(9) NOT NULL,
  `phone` bigint(11) unsigned DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `schools`
--

CREATE TABLE IF NOT EXISTS `schools` (
`id` bigint(20) unsigned NOT NULL,
  `name` varchar(128) NOT NULL,
  `city` varchar(128) NOT NULL,
  `state` varchar(2) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
`id` bigint(20) NOT NULL,
  `username` varchar(128) NOT NULL,
  `hashword` varchar(128) NOT NULL,
  `org` bigint(20) NOT NULL,
  `first_name` varchar(128) NOT NULL,
  `last_name` varchar(128) NOT NULL,
  `email` varchar(256) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `classes_audit`
--
ALTER TABLE `classes_audit`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `grade_categories`
--
ALTER TABLE `grade_categories`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orgs`
--
ALTER TABLE `orgs`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `schools`
--
ALTER TABLE `schools`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `classes_audit`
--
ALTER TABLE `classes_audit`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=72;
--
-- AUTO_INCREMENT for table `grade_categories`
--
ALTER TABLE `grade_categories`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT for table `orgs`
--
ALTER TABLE `orgs`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `schools`
--
ALTER TABLE `schools`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
