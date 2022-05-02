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
-- Table structure for table `HELP-PAGE-LINKS`
--

DROP TABLE IF EXISTS `HELP-PAGE-LINKS`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `HELP-PAGE-LINKS` (
  `QT PAGE TITLE` varchar(60) CHARACTER SET utf16 COLLATE utf16_bin NOT NULL,
  `ARTICLE URL` varchar(200) CHARACTER SET utf16 COLLATE utf16_bin NOT NULL,
  PRIMARY KEY (`QT PAGE TITLE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `HELP-PAGE-LINKS`
--

LOCK TABLES `HELP-PAGE-LINKS` WRITE;
/*!40000 ALTER TABLE `HELP-PAGE-LINKS` DISABLE KEYS */;
INSERT INTO `HELP-PAGE-LINKS` VALUES ('Analyse Root Position in Verse','/help/analyse-root-position.php'),('Bookmarks Manager','/help/bookmarks-manager.php'),('Browse Intertextual Connections','/help/browse-intertextual-connections.php'),('Chart of Average Word Length per Sura','/help/average-word-length-per-sura-chart.php'),('Chart of Formulae Used per Sura','/help/number-of-formulae-used-per-sura-chart.php'),('Chart of Formulaic Density by Sura','/help/formulaic-density-by-sura-chart.php'),('Chart of Grammatical Features By Sura','/help/grammatical-features-by-sura-chart.php'),('Chart of Intertextual Links per Source','/help/chart-of-intertextual-links-per-source.php'),('Chart of Number of Different Verse Endings (Rhymes) per Sura','/help/number-of-different-verse-endings-rhymes-per-sura.php'),('Chart of Number of Loanwords per Sura','/help/foreign-words-vocabulary-per-sura-chart.php'),('Chart of Verse Ending (Rhyme) Frequency','/help/verse-ending-rhyme-frequency-chart.php'),('Chart of Verses with Intertextual Connections per Sura','/help/chart-of-verses-with-intertextual-connections.php'),('Cross Referencing Formulae','/help/listing-and-cross-referencing-formulae-in-suras.php'),('Cross Referencing Formulae in Selection','/help/cross-referencing-formulas-in-a-selection-of-verses.php'),('Dictionary','/help/the-dictionary-tool.php'),('Easy Search','/help/the-easy-search-tool.php'),('Examine Root','/help/examine-root.php'),('Exhaustive List of References for Lemma','/help/exhaustive-list-of-references-for-root-or-lemma.php'),('Exhaustive List of References for Root','/help/exhaustive-list-of-references-for-root-or-lemma.php'),('Formulae List','/help/list-all-formulae.php'),('Formulae in Common','/help/formulae-in-common-chart.php'),('Formulaic Commonalities','/help/formulaic-commonalities-between-suras.php'),('Formulaic Density & Usage by Sura','/help/formulaic-density-and-usage-statistics-per-sura.php'),('Formulaic Density Summaries','/help/formulaic-density-summaries.php'),('Formulaic Density by Verse','/help/formulaic-density-by-verse.php'),('Formulaic Diversity per Sura','/help/formulaic-diversity-per-sura-chart.php'),('Home','/help/home-page.php'),('List All Lemmata','/help/word-lists-lemmata.php'),('List All Nouns','/help/word-lists-nouns.php'),('List All Proper Nouns','/help/word-lists-proper-nouns.php'),('List All Roots','/help/word-lists-roots.php'),('List All Verbs','/help/list-all-verbs.php'),('List Loanwords','/help/list-all-foreign-words.php'),('Mean Verse Length by Sura','/help/sura-length-by-mean-verse-length.php'),('Preferences & Account Settings','/help/preferences.php'),('Rhyme Counts per Sura','/help/number-of-different-verse-ending-rhyme-patterns-per-sura.php'),('Root Usage by Sura','/help/root-usage-by-sura.php'),('Search','/help/the-verse-browser-in-detail.php'),('Sura Length by Verses','/help/sura-length-chart.php'),('Sura Length by Words','/help/sura-length-by-words-chart.php'),('Sura List','/help/browsing-the-sura-list.php'),('Sura Rhyme Analysis','/help/sura-rhyme-analysis.php'),('Sura Verse Lengths Characteristics Chart','/help/sura-verse-length-characteristics-chart.php'),('Tags Manager','/help/tags.php'),('Verses','/help/the-verse-browser-in-detail.php'),('Word Associations','/help/word-association-tool.php');
/*!40000 ALTER TABLE `HELP-PAGE-LINKS` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-03-26 15:03:59
