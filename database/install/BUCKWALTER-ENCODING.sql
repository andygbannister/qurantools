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
-- Table structure for table `BUCKWALTER-ENCODING`
--

DROP TABLE IF EXISTS `BUCKWALTER-ENCODING`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `BUCKWALTER-ENCODING` (
  `GLYPH` varchar(1) CHARACTER SET utf16 COLLATE utf16_bin NOT NULL,
  `ASCII` varchar(1) CHARACTER SET utf16 COLLATE utf16_bin NOT NULL,
  `DESCRIPTION` varchar(40) CHARACTER SET utf16 COLLATE utf16_bin NOT NULL,
  `UNICODE` varchar(10) CHARACTER SET utf16 COLLATE utf16_bin NOT NULL,
  UNIQUE KEY `GLYPH` (`GLYPH`)
) ENGINE=InnoDB DEFAULT CHARSET=utf16 COLLATE=utf16_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `BUCKWALTER-ENCODING`
--

LOCK TABLES `BUCKWALTER-ENCODING` WRITE;
/*!40000 ALTER TABLE `BUCKWALTER-ENCODING` DISABLE KEYS */;
INSERT INTO `BUCKWALTER-ENCODING` VALUES ('ء','\'','Hamza','0621'),('أ','>','Alif + HamzaAbove','0623'),('ؤ','&','Waw + HamzaAbove','0624'),('إ','<','Alif + HamzaBelow','0625'),('ئ','}','Ya + HamzaAbove','0626'),('ا','A','Alif','0627'),('ب','b','Ba','0628'),('ة','p','Ta Marbuta','0629'),('ت','t','Ta','062A'),('ث','v','Tha','062B'),('ج','j','Jeem','062C'),('ح','H','Hha','062D'),('خ','x','Kha','062E'),('د','d','Dal','062F'),('ذ','*','Thal','0630'),('ر','r','Ra','0631'),('ز','z','Zain','0632'),('س','s','Seen','0633'),('ش','$','Sheen','0634'),('ص','S','Sad','0635'),('ض','D','DDad','0636'),('ط','T','TTa','0637'),('ظ','Z','ZZa','0638'),('ع','E','Ayn','ع'),('غ','g','Ghayn','063A'),('ـ','_','Tatweel','0640'),('ف','f','Fa','0641'),('ق','q','Qaf','0642'),('ك','k','Kaf','0643'),('ل','l','Lam','0644'),('م','m','Meem','0645'),('ن','n','Nun','0646'),('ه','h','Ha','0647'),('و','w','Waw','0648'),('ى','Y','Alif Maksura','0649'),('ي','y','Ya','064A'),('ً','F','Fathatan','064B'),('ٌ','N','Dammatan','064C'),('ٍ','K','Kasratan','064D'),('َ','a','Fatha','064E'),('ُ','u','Damma','064F'),('ِ','i','Kasra','0650'),('ّ','~','Shadda','0651'),('ْ','o','Sukun','0652'),('ٓ','^','Maddah','0653'),('ٔ','#','Hamza Above','0654'),('ٰ','`','AlifKhanjareeya','0670'),('ٱ','{','Alif + Hamzat Wasl','0671'),('ۜ',':','Small High Seen','06DC'),('۟','@','Small High Rounded Zero','06DF'),('۠','\"','Small High Upright Rectangular Zero','06E0'),('ۢ','[','Small High Meem Isolated Form','06E2'),('ۣ',';','Small Low Seen','06E3'),('ۥ',',','Small Waw','06E5'),('ۦ','.','Small Ya','06E6'),('ۨ','!','Small High Nun','06E8'),('۪','-','Empty Centre Low Stop','06EA'),('۫','+','Empty Centre High Stop','06EB'),('۬','%','Rounded High Stop With Filled Centre','06EC'),('ۭ',']','Small Low Meem','06ED');
/*!40000 ALTER TABLE `BUCKWALTER-ENCODING` ENABLE KEYS */;
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
