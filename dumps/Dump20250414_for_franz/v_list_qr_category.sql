-- MySQL dump 10.13  Distrib 8.0.40, for macos14 (arm64)
--
-- Host: localhost    Database: v_list
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.28-MariaDB

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
-- Table structure for table `qr_category`
--

DROP TABLE IF EXISTS `qr_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `qr_category` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(45) DEFAULT NULL,
  `show` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`category_id`),
  KEY `category` (`category_name`),
  KEY `idx_v_remarks_v_id_category` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=200 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `qr_category`
--

LOCK TABLES `qr_category` WRITE;
/*!40000 ALTER TABLE `qr_category` DISABLE KEYS */;
INSERT INTO `qr_category` VALUES (1,'Civil Status',1),(3,'Survey',2),(4,'CUA SUPPORTER',2),(5,'CS tagging',2),(6,'Social Media',1),(7,'Others',1),(8,'Position',2),(9,'Warding',2),(10,'Leader',2),(11,'CONG tagging',2),(12,'NEW S.O',2),(13,'Distribution',2),(14,'Gender Orientation',1),(15,'TODA',2),(16,'NOT NEEDED',1),(17,'Special Operations Warding',2),(18,'Household',2),(19,'Voter`s Location',2),(20,'Farmer',2),(21,'Fisher Folks',2),(22,'OCCUPATION',2),(23,'CUA HATER',2),(24,'CHED',2),(25,'FATERNITY/SORORITY',2),(26,'DEPED',2),(27,'DEPED SECONDARY',2),(28,'DEPED SUPERVISOR',2),(29,'SURVEY UPDATE',2),(30,'OCCUPATION 2',2),(31,'ESTABLISHMENTS',2),(32,'DSWD',2),(33,'Leader 2022',2),(34,'Hotline Survey',2),(35,'Poll 2022',2),(36,'SK FED',2),(37,'ABUNDO TAGGING',2),(38,'SURVEY WARDING 2022',2),(39,'S.O',2),(40,'WITH LPG',2),(41,'PHOENIX SURVEY',2),(42,'VIRAC PUBLIC MARKET PEDICAB DRIVER',2),(43,'PEDICAB DRIVER',2),(44,'FISHER FOLKS 2023 UPDATE',2),(45,'BRGY ELECTION 2023',2),(46,'JOB ORDER 2023',2),(47,'BUS ISSUE',2),(48,'MINING ISSUE',1),(49,'ASANZA',0),(50,'RODRIGUEZ',0),(51,'COC FILING 2024',0),(52,'Household Warding',0),(54,'Cua Supporter SocMed',0),(55,'Head of Household Survey 2025 Congressman',0),(56,'Head of Household Survey 2025 Governor',0),(199,'Position Leader\'s Meeting 2025',0);
/*!40000 ALTER TABLE `qr_category` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-04-14 15:12:23
