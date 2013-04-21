-- Output of 
-- SELECT LocationID, Name1, X( GeoLoc ), Y(GeoLoc) FROM `location`;


-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 11, 2012 at 01:09 AM
-- Server version: 5.1.41
-- PHP Version: 5.3.1

-- SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `forecast`
--

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE IF NOT EXISTS `location` (
  `LocationID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Name1` varchar(32) NOT NULL,
  `Name2` varchar(32) NOT NULL,
  `GeoLoc` point NOT NULL,
  PRIMARY KEY (`LocationID`),
  KEY `GeoLoc` (`GeoLoc`(25)),
  KEY `Name1` (`Name1`),
  KEY `Name2` (`Name2`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `location`
--

INSERT INTO `location` (`LocationID`, `Name1`, `GeoLoc`) VALUES
(1, 'Orkney and Shetland', GeomFromWKB( Point(59.052209, -2.981415) ) ),
(2, 'Highlands and Eilean Siar', GeomFromWKB( Point(55.877622, -5.115509 )) ),
(3, 'Grampian', GeomFromWKB( Point(57.230016, -2.617493 )) ),
(4, 'Strathclyde', GeomFromWKB( Point(55.877622, -5.115509 )) ),
(5, 'Central Tayside and Fife', GeomFromWKB( Point(56.260897, -3.162689)) ),
(6, 'SW Scotland, Lothian Borders', GeomFromWKB( Point(55.446153, -3.304138)) ),
(7, 'N Ireland', GeomFromWKB( Point(54.64525, -6.24057)) ),
(8, 'Wales', GeomFromWKB( Point(52.400743, -3.468933)) ),
(9, 'NW England', GeomFromWKB( Point(53.712965, -2.550201)) ),
(10, 'NE England', GeomFromWKB( Point(54.612641, -1.425476)) ),
(11, 'Yorks & Humber', GeomFromWKB( Point(53.653592, -0.829468)) ),
(12, 'E Midlands', GeomFromWKB( Point(52.707179, -0.336456)) ),
(13, 'W Midlands', GeomFromWKB( Point(52.503684, -1.934967)) ),
(14, 'E of England', GeomFromWKB( Point(52.287483, 0.848694)) ),
(15, 'SW England', GeomFromWKB( Point(50.977453, -3.666687)) ),
(16, 'London and SE England', GeomFromWKB( Point(51.37178, -0.458679)) );

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
