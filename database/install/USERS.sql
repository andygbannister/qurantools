-- MySQL dump 10.13  Distrib 8.0.28, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: quran-os
-- ------------------------------------------------------
-- Server version	8.0.28

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `USERS`
--

DROP TABLE IF EXISTS `USERS`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `USERS` (
  `User ID` mediumint NOT NULL AUTO_INCREMENT,
  `User Type` enum('CONSUMER','INSTITUTIONAL','SYSTEM') CHARACTER SET utf16 COLLATE utf16_bin NOT NULL DEFAULT 'CONSUMER',
  `Email Address` varchar(150) CHARACTER SET utf16 COLLATE utf16_bin DEFAULT NULL,
  `Password Hash` varchar(250) CHARACTER SET utf16 COLLATE utf16_bin DEFAULT NULL,
  `First Name` varchar(100) CHARACTER SET utf16 COLLATE utf16_bin DEFAULT NULL,
  `Last Name` varchar(100) CHARACTER SET utf16 COLLATE utf16_bin DEFAULT NULL,
  `Administrator` enum('ADMIN','SUPERUSER','WORD_FIXER') CHARACTER SET utf16 COLLATE utf16_bin DEFAULT NULL,
  `Reset Code` varchar(50) CHARACTER SET utf16 COLLATE utf16_bin DEFAULT NULL,
  `Reset Timecode` bigint DEFAULT NULL,
  `Last Login Date` date DEFAULT NULL,
  `Last Login Time` time DEFAULT NULL,
  `Last Login Timestamp` timestamp NULL DEFAULT NULL,
  `Is Blocked` tinyint,
  `Login Count` int DEFAULT 0,
  `Fails Count` int DEFAULT 0,
  `Fail Time` int DEFAULT NULL,
  `Creation Date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Preferred Highlight Colour` varchar(6) CHARACTER SET utf16 COLLATE utf16_bin NOT NULL DEFAULT 'FFFF00',
  `Preferred Highlight Colour Lightness Value` smallint NOT NULL DEFAULT '127',
  `Preferred Cursor Colour` varchar(6) CHARACTER SET utf16 COLLATE utf16_bin NOT NULL DEFAULT 'DDDDDD',
  `Preferred Translation` tinyint NOT NULL DEFAULT '1',
  `Preferred Verse Count` smallint NOT NULL DEFAULT '50',
  `Preferred Default Mode` tinyint NOT NULL DEFAULT '0',
  `Preferred Keyboard Direction` varchar(3) CHARACTER SET utf16 COLLATE utf16_bin NOT NULL DEFAULT 'LTR',
  `Preference Italics Transliteration` tinyint NOT NULL DEFAULT '1',
  `Preference Show Quick Tips` tinyint NOT NULL DEFAULT '1',
  `Preference Floating Page Navigator` tinyint NOT NULL DEFAULT '1',
  `Current Quick Tip ID` mediumint NOT NULL DEFAULT '1',
  `AJAX Token` varchar(14) CHARACTER SET utf16 COLLATE utf16_bin DEFAULT NULL,
  `Preference Formulaic Glosses` tinyint DEFAULT '1',
  `Preference Hide Transliteration` tinyint NOT NULL DEFAULT '0',
  `LOCKED WITH MESSAGE` text CHARACTER SET utf16 COLLATE utf16_bin,
  `User Name` varchar(200) CHARACTER SET utf16 COLLATE utf16_bin DEFAULT NULL,
  PRIMARY KEY (`User ID`),
  UNIQUE KEY `Email Address_UNIQUE` (`Email Address`),
  KEY `Reset Codes Index` (`Reset Code`),
  KEY `Last Login Timestamp` (`Last Login Timestamp`)
) ENGINE=InnoDB AUTO_INCREMENT=1525 DEFAULT CHARSET=utf16 COLLATE=utf16_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `USERS`
--

LOCK TABLES `USERS` WRITE;
/*!40000 ALTER TABLE `USERS` DISABLE KEYS */;
/*!40000 ALTER TABLE `USERS` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-03-26 15:04:03
