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
-- Table structure for table `QUICK-TIPS`
--

DROP TABLE IF EXISTS `QUICK-TIPS`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `QUICK-TIPS` (
  `ID` mediumint NOT NULL AUTO_INCREMENT,
  `Quick Tip` varchar(500) CHARACTER SET utf16 COLLATE utf16_bin NOT NULL,
  `Example` varchar(200) CHARACTER SET utf16 COLLATE utf16_bin DEFAULT NULL,
  `More Help Link` varchar(200) CHARACTER SET utf16 COLLATE utf16_bin DEFAULT NULL,
  `Next Quick Tip ID` mediumint DEFAULT NULL COMMENT 'The ID of the next quick tip to show. If it is NULL, then it will not be possible to show another tip, so only the last tip should really be NULL.',
  `Previous Quick Tip ID` mediumint DEFAULT NULL COMMENT 'The ID of the previous quick tip to show. If it is NULL, then it will not be possible to show the previous tip, so only the first tip should really be NULL.',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `fk_NEW-QUICK-TIPS_Next_Quick_Tip` (`Next Quick Tip ID`),
  UNIQUE KEY `fk_NEW-QUICK-TIPS_Previous_Quick_Tip` (`Previous Quick Tip ID`),
  CONSTRAINT `fk_NEW-QUICK-TIPS_Next_Quick_Tip` FOREIGN KEY (`Next Quick Tip ID`) REFERENCES `QUICK-TIPS` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_NEW-QUICK-TIPS_Previous_Quick_Tip` FOREIGN KEY (`Previous Quick Tip ID`) REFERENCES `QUICK-TIPS` (`ID`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf16 COLLATE=utf16_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `QUICK-TIPS`
--

LOCK TABLES `QUICK-TIPS` WRITE;
/*!40000 ALTER TABLE `QUICK-TIPS` DISABLE KEYS */;
INSERT INTO `QUICK-TIPS` VALUES (1,'Welcome to Qur`an Tools. You can easily look up a verse by simply typing it into the search box above, for example try typing:','2:256','/help/looking-up-a-passage.php',2,NULL),(2,'You can look up an entire sura simply by typing any number between 1 and 114 into the search box above. For example, try typing:','110','/help/looking-up-a-passage.php',3,1),(3,'It\'s easy to look up a range of verses; try typing this into the search box above:','2:1-20','/help/looking-up-a-passage.php',4,2),(4,'You can look up a series of ranges of verses by separating them with a semi-colon, like this:','17:61-64;18:50;20:116-117;38:71-83','/help/looking-up-a-passage',20,3),(5,'Search the Qur`an for an Arabic root by typing ROOT: into the search box above, following by the root you want. For example, try typing:','ROOT:ktb','/help/performing-a-basic-search.php',6,20),(6,'See all the Arabic roots in the Qur`an, along with their definitions, using the Dictionary Tool. You\'ll find it under the Browse menu above.','<a href=\'dictionary.php\'>Open the Dictionary</a>','/help/the-dictionary-tool.php',7,5),(7,'You can also search using Arabic letters rather than transliterated English. The keyboard icon, just below the search box above, will give you an Arabic keyboard. Click on letters to enter them. And then you can try a search like this one:','ROOT:???','/help/performing-a-basic-search.php',8,6),(8,'Search the English translations of the Qur`an by using the ENGLISH command. For example, try typing the example below into the search box above:','ENGLISH:throne','/help/performing-a-basic-search.php',9,7),(9,'You can search the English translations for a phrase, rather than a word, by surrounding it with quote marks. For example, try this:','ENGLISH:\"the world\"','/help/performing-a-basic-search.php',10,8),(10,'Search for more than one root by using the AND command. For example, try typing this into the search box above:','ROOT:ktb AND ROOT:ryb','/help/performing-a-basic-search.php',11,9),(11,'You can search the English translations and for Arabic words in the same search command. For example, try typing this into the search box above:','ROOT:ktb AND ENGLISH:book','/help/performing-a-basic-search.php',12,10),(12,'You can restrict a search to a range of verses by using the RANGE command. For example, to find the Arabic root \'ktb\' in the first 20 verses of sura 2, you would do this:','ROOT:ktb RANGE:2:1-20','/help/advanced-searching.php#RANGE',13,11),(13,'Use the PROVENANCE command to restrict a search to just Meccan or Medinan suras. For example, try typing this:','ROOT:qwl AND PROVENANCE:Meccan','/help/advanced-searching.php#PROVENANCE',14,12),(14,'Qur`an Tools offers dozens of powerful ways to search the text of the Qur`an. Do take the time to read the help page on Advanced Searching and try out the various examples.','/help/advanced-searching.php\' target=\'_blank\'>Read about Advanced Searching</a>','/help/advanced-searching.php',15,13),(15,'Have you explored the Charts menu above? Qur`an Tools offers lots of ways to analyse the Qur`anic text graphically. Also look for the <img src=\'/images/stats.gif\'> icon in many screens ? click on it to get a chart (or point at it to get a quick mini chart).','<a href=\'/charts/chart_sura_length.php?TYPE=1\'>Example chart: Sura Length</a>',NULL,18,14),(18,'Qur`an Tools can powerfully analyse the formulaic diction that underlies the Qur`anic text. For example, type 2 into the search box above to look up sura 2, then click the \"Formula\" button at the top left of the toolbar and then click OK. All the formulaic phrases in sura 2 will be highlighted in blue for you.','<a href=\'verses.php?V=2&FORMULA=3&FORMULA_TYPE=ROOT\'>Show the formulae in sura 2</a>',NULL,19,15),(19,'To see at a glance all the formulaic phrases in a sura and where else they occur in the Qur`an, choose \'Cross Reference Formulae in Suras\' from the \'Formulae\' menu, then pick the sura you want.','<a href=\'/formulae/sura_formulae_analyse.php\'>Cross Reference Formulae in Suras</a>',NULL,21,18),(20,'Click \"Verse Picker\" below the search box above to open the Quick Verse Picker. Then click a sura number, then a verse, to quickly find a particular verse.',NULL,NULL,5,4),(21,'If there are verses that often want to find again, consider creating a <b>tag</b> and applying it to those verses. Create your first tag by clicking the link below.','<a href=\'/tag_manager.php\'>Create a Tag</a>','/help/tags.php',NULL,19);
/*!40000 ALTER TABLE `QUICK-TIPS` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-03-26 15:04:02
