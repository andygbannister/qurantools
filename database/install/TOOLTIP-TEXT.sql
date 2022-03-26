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
-- Table structure for table `TOOLTIP-TEXT`
--

DROP TABLE IF EXISTS `TOOLTIP-TEXT`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `TOOLTIP-TEXT` (
  `ID` smallint NOT NULL AUTO_INCREMENT,
  `NAME` varchar(20) CHARACTER SET utf16 COLLATE utf16_bin NOT NULL,
  `TITLE TEXT` varchar(250) CHARACTER SET utf16 COLLATE utf16_bin NOT NULL,
  `BODY TEXT` text CHARACTER SET utf16 COLLATE utf16_bin NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `NAME` (`NAME`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf16 COLLATE=utf16_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `TOOLTIP-TEXT`
--

LOCK TABLES `TOOLTIP-TEXT` WRITE;
/*!40000 ALTER TABLE `TOOLTIP-TEXT` DISABLE KEYS */;
INSERT INTO `TOOLTIP-TEXT` VALUES (1,'BOOKMARK','Bookmark','Save this selection of verses as a bookmark, so you can easily access it again.'),(2,'COPYREF','Copy References','Copy this selection of verses to the clipboard as a list of references.'),(3,'ANALYSE','Analyse Verses','You can count (or chart) the different words found, and even the number of each Arabic letter used in this selection of verses.'),(4,'FORMULAE','Formulaic Analysis Options','Qur&rsquo;an Gateway can highlight any phrase in the currently selected text that are part of a <i>formula</i> (a portion of text that is repeated multiple times in the Qur&rsquo;an). You can browse a <a href=\'formulae/list_formulae.php?L=ANY\'><font color=blue>list of every formula</font></a> in the Qur&rsquo;an or <a href=\'charts/chart-formulae-used-per-sura.php\'><font color=blue>chart their frequency</font></a> across the entire text.'),(5,'CHANGES_PERMANENT','Scribal Change Word Underlining','You have turned underlining of words effected by scribal changes permanently on. You can change this in <a href=\'preferences.php\'><font color=blue>Preferences</font></a>.'),(6,'CHANGES_OFF','Scribal Change Word Underlining','Click to underline any words affected by scribal changes. (Qur&rsquo;an Gateway has a database of thousands of manuscript changes which you can <a href=\'manuscripts/list_all_changes.php\'><font color=blue>browse</font></a>, or <a href=\'search.php?S=CHANGES>0&UNDERLINE_CHANGES=Y\'><font color=blue>search</font></a> for to see them within the text itself).'),(7,'CHANGES_ON','Scribal Change Word Underlining','Click to stop underlining any words affected by scribal changes. (Qur&rsquo;an Gateway has a database of thousands of manuscript changes which you can <a href=\'manuscripts/list_all_changes.php\'><font color=blue>browse</font></a>, or <a href=\'search.php?S=CHANGES>0&UNDERLINE_CHANGES=Y\'><font color=blue>search</font></a> for to see them within the text itself).'),(8,'READER MODE','Switch to Reader Mode','View your selection of verses as text in three columns (Arabic, transliteration, and translation). Reader Mode is the easiest and most natural way to read the text.'),(9,'PARSE MODE','Switch to Parse Mode','Break down each verse in your selection into its constituent words, with full linguistic data shown beside each word.'),(10,'EDIT VERSES','Edit Verses','Click to return to the home page and edit the list of verses you have just looked up.'),(11,'TRANSLATION PICKER','Change Translation','Change the English translation.'),(12,'WHOLE SURA','Show Whole Sura','Click to view the whole of this sura.'),(13,'PREVIOUS SURA','Previous','Show the previous sura.'),(14,'NEXT SURA','Next','Show the next sura.'),(15,'CONTEXT','Show Context','Click to see this verse in its wider qur\'anic context.'),(16,'MSS','Show Manuscripts','Click to see any manuscripts in the Qur’an Gateway database that include this particular verse.'),(18,'FAV-STAR-YELLOW','','This is one of your favourites. Click to unfavourite it.'),(19,'FAV-STAR-TOGGLE','','Click to toggle this change between a favourite (shown by a yellow star <IMG SRC=\'/images/fav_single_yes.png\'>) or a non-favourite (the star will be grayed out <IMG SRC=\'/images/fav_single_no.png\'>).'),(20,'FAV-STAR-ALL-ON','','Add all these scribal changes to your list of favourites.'),(21,'FAV-STAR-ALL-OFF','','Clear your list of favourite scribal changes.'),(22,'TAGS','Add or Remove Tags','Add (or remove) a tag to/from every verse in the selection below. If you haven\'t yet created any tags, <a href=\'tag_manager.php\' class=linky-light>click here</a> to create some.'),(23,'COPYVERSE','Copy Verse to Clipboard','Click to copy this verse (its Arabic, transliteration, translation and reference) to the clipboard.'),(24,'INTERTEXTUALITY','Intertextual Connections','If there are other texts to which this verse has intertextual links of some kind, click this button to show them.'),(25,'INTERLINEAR MODE','Switch to Interlinear Mode','Show each word in a verse along with a gloss, basic morphology (number, person, gender, Arabic form), and its lemma and root.'),(26,'INSTITUTIONAL-LOGIN','University or Institutional Login','<p>Gain access to Qur’an Gateway via your university - or another institution you are part of. Choose your institution and press continue. After you have logged into your institution, you will then be redirected to Qur’an Gateway.</p>'),(27,'IP-USER-WHY-REGISTER','Why do I need to create an account?','<p>Although we know you are accessing Qur’an Gateway from your institution, in order for you to be able to use the personalised features like bookmarking, tagging and storing your previous searches, we need a way to identify you. Hence, we need you to register with an email address and password.</p>');
/*!40000 ALTER TABLE `TOOLTIP-TEXT` ENABLE KEYS */;
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
