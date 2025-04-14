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
-- Table structure for table `userstbl`
--

DROP TABLE IF EXISTS `userstbl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `userstbl` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `fname` varchar(255) DEFAULT NULL,
  `mname` varchar(255) DEFAULT NULL,
  `lname` varchar(255) DEFAULT NULL,
  `contact_no` varchar(45) DEFAULT NULL,
  `user_type` varchar(45) DEFAULT NULL,
  `status` varchar(45) DEFAULT NULL,
  `login_ip` varchar(45) DEFAULT NULL,
  `last_login` date DEFAULT NULL,
  `v_id` int(11) DEFAULT NULL,
  `imgname` varchar(45) DEFAULT NULL,
  `remember` varchar(45) DEFAULT NULL,
  `login_token` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=92 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `userstbl`
--

LOCK TABLES `userstbl` WRITE;
/*!40000 ALTER TABLE `userstbl` DISABLE KEYS */;
INSERT INTO `userstbl` VALUES (18,'Delta','111111','Director General',NULL,'',NULL,'Administrator','offline','127.0.0.1','0000-00-00',257027,NULL,'no',NULL),(30,'jom','123123','Error',NULL,'',NULL,'Administrator','active','::1','0000-00-00',216301,'file-1743591943750.jpeg','no',NULL),(33,'joyce','111111','Cleo',NULL,'',NULL,'Administrator','offline','192.168.10.118','2022-11-11',218811,'file-1713930004678.jpg','no','628ef3a25b48a'),(34,'won','111111','Kheywon',NULL,'Gianan',NULL,'User','offline','192.168.10.134','0000-00-00',251857,NULL,'no',NULL),(35,'atom','111111','Atom',NULL,'Pastor',NULL,'User','offline','192.168.0.118','0000-00-00',171000,NULL,'no',NULL),(36,'leslie','111111','LESLIE ANN',NULL,'MANLAGNIT',NULL,'User','offline','11.0.0.15','2018-12-03',NULL,NULL,'no',NULL),(37,'ken','111111','KEN',NULL,'KOZMA',NULL,'User','offline','11.0.0.4','2019-05-10',NULL,NULL,'no',NULL),(38,'shiela','111111','Sheila',NULL,'Romero',NULL,'User','offline','11.0.0.168','0000-00-00',NULL,'file-1714352932737.jpg','yes',NULL),(39,'jam','111111','Jamie Rose',NULL,'Interior',NULL,'User','offline','11.0.0.82','0000-00-00',NULL,'jami.jpg','no',NULL),(40,'tin','111111','Justine',NULL,'Mendez',NULL,'User','offline','11.0.0.5','2018-11-25',NULL,NULL,'no',NULL),(41,'prince','111111','PRINCE',NULL,'SUBION',NULL,'Administrator','offline','11.0.0.12','0000-00-00',NULL,NULL,'no',NULL),(42,'server','111111','Admin',NULL,'Server',NULL,'Administrator','offline','::1','0000-00-00',NULL,NULL,'no',NULL),(44,'earlflue','111111','Alastor',NULL,'',NULL,'User','offline','11.0.0.104','0000-00-00',NULL,NULL,'no',NULL),(43,'julieann','111111','Ava',NULL,'',NULL,'User','offline','11.0.0.135','0000-00-00',NULL,NULL,'no',NULL),(45,'pola','111111','Apollo',NULL,'',NULL,'Administrator','offline','192.168.10.146','2023-03-16',NULL,NULL,'no',NULL),(46,'leslie','111111','Leslie Ann',NULL,'Manlagnit',NULL,'User','offline','11.0.0.34','2019-02-01',NULL,NULL,'no',NULL),(47,'che','111111','Cherry',NULL,'Joy',NULL,'User','offline','11.0.0.134','2019-05-29',NULL,NULL,'no',NULL),(48,'japhz','111111','Adam',NULL,'',NULL,'User','offline','11.0.0.139','0000-00-00',NULL,NULL,'no',NULL),(49,'angie11','111111','Angelica',NULL,'Buena',NULL,'User','offline','192.168.137.200','2020-11-30',NULL,NULL,'no',NULL),(50,'joanne','111111','Joanne',NULL,'Adille',NULL,'User','offline','11.0.0.134','0000-00-00',NULL,NULL,'no',NULL),(51,'japs','111111','Adam',NULL,'',NULL,'User','offline','192.168.10.147','2023-09-19',NULL,'file-1713947122601.jpg','no','628f1429c796c'),(52,'seresa','111111','Cherry Joy',NULL,'Tribiana',NULL,'Administrator','offline','192.168.10.106','0000-00-00',NULL,NULL,'no',NULL),(53,'jolee','111111','Ava',NULL,'',NULL,'User','offline','192.168.10.125','2023-09-19',NULL,'file-1713934039160.jpg','no','6290266ec293d'),(54,'cymon','111111','John Cymon',NULL,'Vargas',NULL,'User','offline','11.0.0.24','0000-00-00',NULL,NULL,'no',NULL),(55,'aliya','111111','Aliya',NULL,'Tabor',NULL,'User','offline','11.0.0.57','0000-00-00',NULL,NULL,'no',NULL),(56,'kring','111111','Kring',NULL,'Alberto',NULL,'User','offline','192.168.10.122','2021-06-14',NULL,NULL,'no',NULL),(57,'dana','111111','Athena',NULL,'',NULL,'User','offline','192.168.10.139','0000-00-00',NULL,NULL,'no','628d8b4d6398d'),(58,'pol','111111','Pol',NULL,'',NULL,'User','offline','0.0.0.0','0000-00-00',NULL,NULL,NULL,NULL),(59,'paul','111111','Paul',NULL,'',NULL,'Administrator','offline','192.168.10.113','0000-00-00',NULL,NULL,'no',NULL),(60,'diana','111111','Diana',NULL,'',NULL,'User','offline','192.168.1.54','2024-02-22',NULL,'file-1714375304020.jpg','no',NULL),(61,'genelyn','111111','Genelyn',NULL,'Domingo',NULL,'User','offline','192.168.10.119','0000-00-00',NULL,NULL,'no',NULL),(62,'mike','111111','Raven',NULL,'',NULL,'User','offline','192.168.10.127','2022-09-13',NULL,NULL,'no','628d8f90c665f'),(63,'danvie','111111','Danvie',NULL,'Arcilla',NULL,'User','offline','192.168.10.111','0000-00-00',NULL,NULL,'no',NULL),(64,'sheryll','111111','Sheryll Lynne',NULL,'Vargas',NULL,'User','offline','192.168.10.118','2024-02-22',NULL,'file-1713931345932.jpg','no',NULL),(65,'adam','111111','Cubic',NULL,'',NULL,'User','offline','0.0.0.0','0000-00-00',NULL,NULL,NULL,NULL),(66,'justine','111111','Falloon',NULL,'',NULL,'User','offline','192.168.10.100','0000-00-00',NULL,NULL,'no',NULL),(67,'adamb','111111','Cubic',NULL,'',NULL,'User','offline','192.168.10.120','0000-00-00',NULL,NULL,'no',NULL),(68,'raymond','111111','Red Alert',NULL,'',NULL,'User','offline','192.168.10.135','0000-00-00',NULL,NULL,'no','628d8dc4bb8cd'),(69,'rutcheriel','111111','Hermes',NULL,'',NULL,'User','offline','192.168.10.136','0000-00-00',NULL,NULL,'no',NULL),(70,'richel','111111','Hades',NULL,'',NULL,'User','offline','192.168.10.124','0000-00-00',NULL,NULL,'no','628f199812424'),(71,'erika','111111','Helios',NULL,'',NULL,'User','offline','192.168.10.120','0000-00-00',NULL,NULL,'no','629026fc93935'),(72,'kenneth','111111','Peach',NULL,'',NULL,'User','offline','192.168.10.124','0000-00-00',NULL,'file-1713933447608.jpg','no','628f12c248c28'),(73,'rcamille','111111','Hera',NULL,'',NULL,'User','offline','192.168.1.15','0000-00-00',NULL,NULL,'no',NULL),(74,'norvin','111111','Ares',NULL,'',NULL,'User','offline','192.168.1.23','0000-00-00',NULL,NULL,'no',NULL),(75,'dave','111111','Achelous',NULL,'',NULL,'User','offline','192.168.1.22','0000-00-00',NULL,NULL,'no',NULL),(76,'carlo','111111','Atlas',NULL,'',NULL,'User','offline','192.168.10.132','0000-00-00',NULL,NULL,'no',NULL),(77,'user1','111111','User',NULL,'',NULL,'User','offline','192.168.1.17','0000-00-00',NULL,NULL,NULL,NULL),(78,'camille','111111','MIlle',NULL,'',NULL,'User','offline','192.168.1.76','0000-00-00',NULL,NULL,NULL,NULL),(82,'jai','111111','Jirah',NULL,'null',NULL,NULL,NULL,NULL,NULL,NULL,'file-1715905631094.png',NULL,NULL),(83,'nikko','111111','Nikko',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(84,'jojie','111111','Jojie',NULL,NULL,NULL,'User',NULL,NULL,NULL,NULL,NULL,'yes',NULL),(85,'angie','111111','Angie',NULL,NULL,NULL,'User',NULL,NULL,NULL,NULL,NULL,'no',NULL),(79,'mitch','111111','Hestia',NULL,'',NULL,'User','offline','0.0.0.0','2024-02-02',NULL,'file-1714099240751.jpg','no',NULL),(81,'shiela','111111','Aleihs',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(80,'angela','111111','Artemis',NULL,'',NULL,'User','offline','0.0.0.0','0000-00-00',NULL,'file-1713933775809.jpeg','no',NULL),(86,'joymae','111111','Joy Mae',NULL,NULL,NULL,'User',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(87,'vianca','111111','Vianca Marie',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(88,'kenny','111111','Kenneth',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(90,'turay','111111','Black',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(91,'carolmae','111111','Carol Mae',NULL,NULL,NULL,'User',NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `userstbl` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-04-14 15:12:22
