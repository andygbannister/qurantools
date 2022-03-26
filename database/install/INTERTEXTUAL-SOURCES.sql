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
-- Table structure for table `INTERTEXTUAL SOURCES`
--

DROP TABLE IF EXISTS `INTERTEXTUAL SOURCES`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `INTERTEXTUAL SOURCES` (
  `SOURCE ID` varchar(20) CHARACTER SET utf16 COLLATE utf16_bin NOT NULL,
  `SOURCE NAME` varchar(250) CHARACTER SET utf16 COLLATE utf16_bin NOT NULL,
  `SOURCE ALTERNATIVE NAME` varchar(250) CHARACTER SET utf16 COLLATE utf16_bin NOT NULL,
  `SOURCE LANGUAGE` varchar(40) CHARACTER SET utf16 COLLATE utf16_bin NOT NULL,
  `SOURCE DATE` varchar(50) CHARACTER SET utf16 COLLATE utf16_bin NOT NULL,
  `SOURCE DATE NUMERIC` mediumint NOT NULL,
  `SOURCE URL` varchar(250) CHARACTER SET utf16 COLLATE utf16_bin NOT NULL,
  `PUBLISHED SOURCE` varchar(250) CHARACTER SET utf16 COLLATE utf16_bin NOT NULL,
  `VERSE REFERENCES` text CHARACTER SET utf16 COLLATE utf16_bin NOT NULL,
  UNIQUE KEY `SOURCE ID` (`SOURCE ID`),
  UNIQUE KEY `SOURCE INDEX` (`SOURCE ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `INTERTEXTUAL SOURCES`
--

LOCK TABLES `INTERTEXTUAL SOURCES` WRITE;
/*!40000 ALTER TABLE `INTERTEXTUAL SOURCES` DISABLE KEYS */;
INSERT INTO `INTERTEXTUAL SOURCES` VALUES ('1SAMUEL','1 Samuel','','Hebrew','6th-8th Centuries BC',-700,'https://www.biblegateway.com/passage/?search=1samuel&version=NIV','','2:246-247;2:250-251'),('2ENOCH','2 Enoch','','Greek (Semitic Origi','Late 1st Century AD',70,'http://www.earlyjewishwritings.com/2enoch.html','','82:10-12'),('2MACC','2 Maccabees','','Koine Greek','124BC',-124,'https://www.biblegateway.com/passage/?search=2%20Maccabees+1&version=NRSV','','105:1-5'),('3MACC','3 Maccabees','','Greek','50BC-50AD',0,'https://www.biblegateway.com/passage/?search=3%20Maccabees+1&version=NRSV','','105:1-5'),('4BARUCH','4 Baruch','Paralipomena of Jeremiah','Greek, Ethiopic, Armenian, and Slavic','2nd Century AD',130,'http://ccat.sas.upenn.edu/rak//publics/pseudepig/ParJer-Eng.html','','2:259'),('AHIKAR','The Story of Ahikar','The Words of Ahikar','Aramaic / Slavonic',' 5th Century BC',-450,'https://web.qurangateway.org/texts/story_of_akihar.PDF','F. C. Conybeare, J. Rendel Harris and Agnes Smith (editors), <i>The Story of Aḥiḳar from the Aramaic, Syriac, Arabic, Armenian, Ethiopic, Old Turkish, Greek and Slavonic Versions</i> (Cambridge: Cambridge University Press, 1913)','28:38;40:36-37'),('AIG','The Arabic Infancy Gospel','The Syriac Infancy Gospel','Arabic/Syriac','6th Century AD',550,'http://gnosis.org/library/infarab.htm','','3:46;5:110;19:29-30'),('ALEXANDER','The Alexander Legend','A Christian Legend Concerning Alexander','Syriac','629-630AD',629,'/texts/Budge-Alexander.PDF','E. A. Wallis Budge, <i>The History of Alenxader the Great (Being the Syriac Version of the Pseudo-Callisthenes) (Cambridge: Cambridge University Press, 1889)','18:83;18:86;18:93-97;18:94;18:94;18:97;21:96'),('APOCALYPSEABRAHAM','The Apocalypse of Abraham','','Slavonic (Originally Hebrew)','Late 1st or Early 2nd Century AD',100,'http://www.pseudepigrapha.com/pseudepigrapha/Apocalypse_of_Abraham.html','','6:74-83;9:114;56:4-11;90:18-20'),('BABTALMUD','Babylonian Talmud','','Hebrew','5th/6th Century AD',500,'https://halakhah.com/index.html','','2:30;2:63;2:187;5:32;7:163;7:166;7:171;21:98;28:76;34:13;38:34-35;38:41-44;71:23'),('CAVE','The Book of the Cave of Treasures','The Treasure','Syriac','6th Century AD (Some Parts 4th Century)',525,'https://sacred-texts.com/chr/bct/index.htm','E. A. Wallis Budge, _The Book of the Cave of Treasures_ (London: The Religious Tract Society, 1927)','2:34;7:11-18;7:20-22;15:29-43;17:61-64;18:50;20:116-117;38:71-83'),('DEUTERONOMY','Deuteronomy','','Hebrew','Circa 650BC',-650,'https://www.biblegateway.com/passage/?search=deuteronomy&version=RSV','','5:45;17:22-38'),('DIDASCALIA','Didascalia','Didascalia Apostolorum','Syriac (Originally Greek)','Early 3rd Century',240,'http://www.earlychristianwritings.com/didascalia.html','','4:155;4:160'),('EPHREM-HYMNS','St. Ephrem the Syrian\'s Hymns on Paradise','','Syriac','4th century AD',350,'','St. Ephrem the Syrian, <i>Hymns on Paradise</i>, Translation by Sebastian Brock (New York: St. Vladimir\'s Seminary Press, 1990)','7:22;9:72'),('EXODUS','Exodus','','Hebrew','Circa 650BC',-650,'https://www.biblegateway.com/passage/?search=exodus&version=RSV','','2:60;4:153-155;5:45;7:103-108;7:109-118;7:127;7:136;10:75-86;10:88-89;10:90-92;17:22-38;28:21-28;40:25'),('EZEKIEL','Ezekiel','','Hebrew','Circa 6th Century BC',550,'https://www.biblegateway.com/passage/?search=Ezekiel+1&version=NIV','','18:94;18:97;21:96'),('GENESIS','Genesis','','Hebrew','Circa 1000-650BC',-900,'https://www.biblegateway.com/passage/?search=genesis&version=RSV','','2:31-33;2:35-36;2:260;7:80-83;11:69-73;11:74-83;12:3-111;15:51-60;15:61-77;28:24;28:38;29:14;37:73-82;37:133-138'),('GENESIS-RABBAH','Genesis Rabbah','B\'reshith Rabba','Hebrew','Circa 500AD (Possibly Earlier)',500,'https://sacred-texts.com/jud/tmm/tmm07.htm','','2:258;6:74;12:67'),('GOSBART','The Gospel of Bartholomew','The Questions of Bartholomew','Greek/Latin/Slavonic','Third Century AD',350,'http://www.gnosis.org/library/gosbart.htm','','2:34;7:11-18;15:29-43;17:61-64;18:50;20:116-117;38:71-83'),('IGJ','The Infancy Gospel of James','The Protevangelium of James','Greek','Mid 2nd Century AD',150,'https://www.gospels.net/infancyjames','','3:35-36;3:37;3:44;19:22'),('IGT','The Infancy Gospel of Thomas','','Greek/Syriac','Mid to Late 2nd Century AD',175,'http://www.earlychristianwritings.com/text/infancythomas.html','','3:49;5:110'),('ISAAC_CAIN','Isaac of Antioch: \'Homily on Cain and Abel\'','','Syriac','Fifth Century AD',450,'','','5:28'),('JONAH','Jonah','','Hebrew','4th/5th Century BC',-450,'https://www.biblegateway.com/passage/?search=Jonah+1-4&version=RSV','','37:139-148'),('JOSEPHUS-ANTIQUITIES','Josephus (Jewish Antiquities)','Antiquities of the Jews','Greek','Late First Century AD',94,'https://www.sacred-texts.com/jud/josephus/','','34:15-19'),('JUDGES','Judges','','Hebrew','c. 600BC',-600,'https://www.biblegateway.com/passage/?search=judges&version=NIV','','2:249'),('LEVITICUS','Leviticus','','Hebrew','Circa 600BC',-600,'https://www.biblegateway.com/passage/?search=leviticus&version=RSV','','5:45'),('LIFE','The Life of Adam and Eve','Vita Adae et Evae','Latin','Originally Circa 100BC',-100,'https://www.ccel.org/c/charles/otpseudepig/adamnev.htm','R.H. Charles, \"The Apocrypha and Pseudepigrapha of the Old Testament\" (Oxford: Clarendon Press, 1913)','2:34;7:11-18;15:29-43;17:61-64;18:50;20:116-117;38:71-83'),('LUKE','Gospel of Luke','','Greek','circa 60AD',60,'https://www.biblegateway.com/passage/?search=Luke+1&version=NIV','','3:38-41;7:44-50;19:2-11'),('MATTHEW','Gospel of Matthew','','Greek','circa 60AD',60,'https://www.biblegateway.com/passage/?search=Matthew+1&version=NIV','','90:7-20'),('MEKHILTA','The Mekhilta de-Rabbi Ishmael','','Hebrew','Late 4th Century AD',450,'https://www.sefaria.org/Mekhilta_d\'Rabbi_Yishmael?lang=en','','10:92'),('NUMBERS','Numbers','','Hebrew','6th century BC (or earlier)',-550,'https://www.biblegateway.com/passage/?search=numbers&version=NIV','','3:33;28:76-81;66:12'),('PSEUDOMAT','The Gospel of Pseudo-Matthew','The Book About the Origin of the Blessed Mary and the Childhood of the Savior','Latin','Early Seventh Century AD',630,'http://www.gnosis.org/library/psudomat.htm','','3:46;3:49;5:110;19:22-26'),('PSN','Pseudo-Narsai, \'Homilies on Joseph\'','','Syriac','Fifth Century AD',450,'','','12:75'),('REVELATION','Revelation','','Greek','Late First Century AD',85,'https://www.biblegateway.com/passage/?search=Revelation+1&version=NIV','','18:94;18:97;21:96'),('ROMANOS-JOSEPH','Romanos the Melodist, \'De Joseph\'','','Greek','Sixth Century AD',560,'','José Grosdidier de Matons, <i>Romanos le Mélode: Hymns I</i> (Paris: Éditions du Cerf, 1964)','12:65'),('SERUGH-ABRAHAM','Jacob of Serugh: On Abraham and His Types','','Syriac','Early 6th Century AD',520,'http://bit.ly/2Skemhk','','2:125-127'),('SERUGH-SLEEPERS','Jacob of Serugh’s Mēmrā on the Sleepers of Ephesus','','Syriac','Early 6th Century AD',520,'http://bit.ly/2Skemhk','Brock, Sebastian, ‘Jacob of Serugh\'s Poem on the Sleepers of Ephesus’ in Pauline Allen, Majella Franzmann & Rick Strelan, eds., \"I Sowed Fruit Into Hearts: Festschrift for Professor Michael Lattke\" (Strathfield: St. Paul\'s Publications, 2007) pp13-30','18:9-26'),('TABARI-HISTORY-2','The History of Al-Ṭabarī: Volume II: Prophets and Kings','Taʾrīkh al-Rusūl wa al-Mulūk','Arabic','Early 10th Century',915,'https://ghayb.com/the-history-of-tabari/','Brinner, William (editor), The History of al-Ṭabarī, Volume II: Prophets and Patriarchs. SUNY Series in Near Eastern Studies (Albany, New York: State University of New York Press, 1986)','7:73-79;11:62-68;26:141-159;54:23-31'),('TARGUM-ESTHER','Targum Sheni','Second Targum of Esther','Aramaic','5th-7th Century AD',600,'/texts/Targum-Sheni.pdf','','2:102;34:12-13'),('TESTAMENTSOLOMON','The Testament of Solomon','','Greek','First to Third Century AD',150,'http://www.earlyjewishwritings.com/testsolomon.html','','37:6-10'),('ZACHARIAS','The Syriac Chronicle','Pseudo-Zacharias Rhetor','Syriac','Mid 6th Century AD',550,'http://www.tertullian.org/fathers/zachariah00.htm','F. J. Hamilton & E. W. Brooks (translators), <i>The Syriac Chronicle known as that of Zachariah of Mitylene</i> (London, 1899); G. Greatrex  (Editor), <i>The Chronicle of Pseudo-Zachariah Rhetor: Church and War in Late Antiquity</i> (Liverpool 2011)','18:9-26');
/*!40000 ALTER TABLE `INTERTEXTUAL SOURCES` ENABLE KEYS */;
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
