-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 18, 2013 at 08:02 PM
-- Server version: 1.0.3
-- PHP Version: 5.4.4-14+deb7u3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `printers`
--

-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `campuses` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `campus_name` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;
--
-- Table structure for table `history`
--

CREATE TABLE IF NOT EXISTS `history` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `printer_id` int(255) NOT NULL,
  `timestamp` int(255) NOT NULL,
  `status` varchar(64) COLLATE utf8_bin NOT NULL,
  `desc` varchar(255) COLLATE utf8_bin NOT NULL,
  `tray_1` tinyint(255) NOT NULL,
  `tray_2` tinyint(255) NOT NULL,
  `tray_3` tinyint(255) NOT NULL,
  `count` int(255) NOT NULL,
  `toner` double(5,2) NOT NULL,
  `kit_a` double(5,2) NOT NULL,
  `kit_b` double(5,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Table structure for table `printers`
--

CREATE TABLE IF NOT EXISTS `printers` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `mac` varchar(32) COLLATE utf8_bin NOT NULL,
  `serial` varchar(255) COLLATE utf8_bin NOT NULL,
  `model` varchar(255) COLLATE utf8_bin NOT NULL,
  `campus_id` int (255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE (`name`),
  KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
