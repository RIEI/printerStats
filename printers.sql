--
-- Table structure for table `campuses`
--
CREATE TABLE `campuses` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `campus_name` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



--
-- Table structure for table `error_log`
--
CREATE TABLE `error_log` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `host` varchar(255) COLLATE utf8_bin NOT NULL,
  `message` varchar(32) COLLATE utf8_bin NOT NULL,
  `timestamp` int(32) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



--
-- Table structure for table `history`
--

CREATE TABLE `history` (
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
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



--
-- Table structure for table `printers`
--
CREATE TABLE `printers` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_bin NOT NULL,
  `mac` varchar(32) COLLATE utf8_bin NOT NULL,
  `serial` varchar(255) COLLATE utf8_bin NOT NULL,
  `model` varchar(255) COLLATE utf8_bin NOT NULL,
  `campus_id` int(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

