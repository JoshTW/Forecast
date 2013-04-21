-- MySQL dump 10.13  Distrib 5.1.41, for Win32 (ia32)
--
-- Host: localhost    Database: forecast
-- ------------------------------------------------------
-- Server version	5.1.41

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `device`
--

DROP TABLE IF EXISTS `device`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `device` (
  `byte1` tinyint(3) unsigned NOT NULL,
  `byte2` tinyint(3) unsigned NOT NULL,
  `byte3` tinyint(3) unsigned NOT NULL,
  `byte4` tinyint(3) unsigned NOT NULL,
  `lastIPaddress` varchar(32) NOT NULL,
  `Location` int(11) unsigned NOT NULL,
  `ForecastQty` tinyint(1) NOT NULL,
  `ForecastOneType` int(11) unsigned NOT NULL,
  `ForecastTwoType` int(11) unsigned NOT NULL,
  `ForecastThreeType` int(11) unsigned NOT NULL,
  PRIMARY KEY (`byte1`,`byte2`,`byte3`,`byte4`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `device`
--

LOCK TABLES `device` WRITE;
/*!40000 ALTER TABLE `device` DISABLE KEYS */;
/*!40000 ALTER TABLE `device` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forecasttypes`
--

DROP TABLE IF EXISTS `forecasttypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forecasttypes` (
  `ForecastTypeID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ForecastType` varchar(32) NOT NULL,
  PRIMARY KEY (`ForecastTypeID`),
  UNIQUE KEY `ForecastType` (`ForecastType`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forecasttypes`
--

LOCK TABLES `forecasttypes` WRITE;
/*!40000 ALTER TABLE `forecasttypes` DISABLE KEYS */;
INSERT INTO `forecasttypes` VALUES (1,'Pollen');
/*!40000 ALTER TABLE `forecasttypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `location`
--

DROP TABLE IF EXISTS `location`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `location` (
  `LocationID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Name1` varchar(32) NOT NULL,
  `Name2` varchar(32) NOT NULL,
  `GeoLoc` point NOT NULL,
  PRIMARY KEY (`LocationID`),
  KEY `GeoLoc` (`GeoLoc`(25)),
  KEY `Name1` (`Name1`),
  KEY `Name2` (`Name2`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `location`
--

LOCK TABLES `location` WRITE;
/*!40000 ALTER TABLE `location` DISABLE KEYS */;
INSERT INTO `location` VALUES (1,'Orkney and Shetland','\0\0\0\0\0\0\0GÇÕÈ®†M@Dn†ðÙÀ','');
/*!40000 ALTER TABLE `location` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `relation`
--

DROP TABLE IF EXISTS `relation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `relation` (
  `Location` int(11) unsigned NOT NULL,
  `Source` int(11) unsigned NOT NULL,
  `Rank` int(11) NOT NULL,
  PRIMARY KEY (`Location`,`Source`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `relation`
--

LOCK TABLES `relation` WRITE;
/*!40000 ALTER TABLE `relation` DISABLE KEYS */;
/*!40000 ALTER TABLE `relation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `source`
--

DROP TABLE IF EXISTS `source`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `source` (
  `SourceID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `phpFIle` varchar(32) NOT NULL,
  `UpdateTime` time NOT NULL,
  `LocationField` varchar(32) NOT NULL,
  `ForecastType` int(11) NOT NULL,
  PRIMARY KEY (`SourceID`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `source`
--

LOCK TABLES `source` WRITE;
/*!40000 ALTER TABLE `source` DISABLE KEYS */;
INSERT INTO `source` VALUES (1,'metoffice.php','00:05:30','Name1',1);
/*!40000 ALTER TABLE `source` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-11-01 23:54:26
