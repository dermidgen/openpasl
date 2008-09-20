-- MySQL Administrator dump 1.4
--
-- ------------------------------------------------------
-- Server version	5.0.51a-3ubuntu5.3


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


--
-- Create schema openpasl_test
--

CREATE DATABASE IF NOT EXISTS openpasl_test;
USE openpasl_test;

--
-- Definition of table `openpasl_test`.`pasl_query_tests`
--

DROP TABLE IF EXISTS `openpasl_test`.`pasl_query_tests`;
CREATE TABLE  `openpasl_test`.`pasl_query_tests` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(25) NOT NULL,
  `sequence` varchar(25) NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `openpasl_test`.`pasl_query_tests`
--

/*!40000 ALTER TABLE `pasl_query_tests` DISABLE KEYS */;
LOCK TABLES `pasl_query_tests` WRITE;
INSERT INTO `openpasl_test`.`pasl_query_tests` VALUES  (1,'db_test','primary','0000-00-00 00:00:00');
UNLOCK TABLES;
/*!40000 ALTER TABLE `pasl_query_tests` ENABLE KEYS */;




/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
