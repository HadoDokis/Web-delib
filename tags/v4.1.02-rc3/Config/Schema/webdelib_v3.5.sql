-- MySQL dump 10.13  Distrib 5.1.54, for debian-linux-gnu (i686)
--
-- Host: localhost    Database: webdelib_3.5
-- ------------------------------------------------------
-- Server version	5.1.54-1ubuntu4-log

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
-- Table structure for table `acos`
--

DROP TABLE IF EXISTS `acos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alias` varchar(255) NOT NULL DEFAULT '',
  `lft` int(10) DEFAULT NULL,
  `rght` int(10) DEFAULT NULL,
  `parent_id` int(10) NOT NULL DEFAULT '0',
  `model` varchar(255) DEFAULT NULL,
  `foreign_key` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acos`
--

LOCK TABLES `acos` WRITE;
/*!40000 ALTER TABLE `acos` DISABLE KEYS */;
INSERT INTO `acos` VALUES (1,'Pages:home',1,2,0,NULL,0),(2,'Pages:mes_projets',3,16,0,NULL,0),(3,'Deliberations:add',14,15,2,NULL,0),(4,'Deliberations:mesProjetsRedaction',12,13,2,NULL,0),(5,'Deliberations:mesProjetsValidation',10,11,2,NULL,0),(6,'Deliberations:mesProjetsATraiter',8,9,2,NULL,0),(7,'Deliberations:mesProjetsValides',6,7,2,NULL,0),(8,'Deliberations:mesProjetsRecherche',4,5,2,NULL,0),(9,'Pages:tous_les_projets',17,26,0,NULL,0),(10,'Deliberations:tousLesProjetsSansSeance',24,25,9,NULL,0),(11,'Deliberations:tousLesProjetsValidation',22,23,9,NULL,0),(12,'Deliberations:tousLesProjetsAFaireVoter',20,21,9,NULL,0),(13,'Deliberations:tousLesProjetsRecherche',18,19,9,NULL,0),(14,'Seances:listerFuturesSeances',27,34,0,NULL,0),(15,'Seances:add',32,33,14,NULL,0),(16,'Seances:listerAnciennesSeances',30,31,14,NULL,0),(17,'Seances:afficherCalendrier',28,29,14,NULL,0),(22,'Pages:gestion_utilisateurs',35,44,0,NULL,0),(23,'Profils:index',40,41,22,NULL,0),(25,'Services:index',38,39,22,NULL,0),(26,'Users:index',36,37,22,NULL,0),(28,'Pages:gestion_acteurs',45,50,0,NULL,0),(29,'Typeacteurs:index',48,49,28,NULL,0),(30,'Acteurs:index',46,47,28,NULL,0),(31,'Pages:administration',51,66,0,NULL,0),(32,'Collectivites:index',64,65,31,NULL,0),(33,'Themes:index',62,63,31,NULL,0),(34,'Models:index',60,61,31,NULL,0),(35,'Sequences:index',58,59,31,NULL,0),(36,'Compteurs:index',56,57,31,NULL,0),(37,'Typeseances:index',54,55,31,NULL,0),(38,'Infosupdefs:index',52,53,31,NULL,0),(39,'Module:Deliberations',67,78,0,NULL,0),(40,'Deliberations:editerProjetValide',68,69,39,NULL,0),(41,'Postseances:index',79,90,0,NULL,0),(42,'Postseances:index',80,89,41,NULL,0),(43,'Deliberations:sendToParapheur',81,82,42,NULL,0),(44,'Deliberations:toSend',83,84,42,NULL,0),(45,'Deliberations:transmit',85,86,42,NULL,0),(46,'Deliberations:verserAsalae',87,88,42,NULL,0),(47,'Cakeflow:circuits',42,43,22,NULL,0),(48,'Deliberations:edit',70,71,39,NULL,0),(49,'Deliberations:goNext',72,73,39,NULL,0),(50,'Deliberations:validerEnUrgence',74,75,39,NULL,0),(51,'Deliberations:rebond',76,77,39,NULL,0),(52,'Module:Circuits',91,94,0,NULL,0),(53,'Circuits:index',92,93,52,NULL,0),(54,'Module:Etapes',95,98,0,NULL,0),(55,'Etapes:index',96,97,54,NULL,0),(56,'Module:Compositions',99,108,0,NULL,0),(57,'Compositions:setCreatedModifiedUser',100,101,56,NULL,0),(58,'Compositions:formatUser',102,103,56,NULL,0),(59,'Compositions:formatLinkedModel',104,105,56,NULL,0),(60,'Compositions:listLinkedModel',106,107,56,NULL,0);
/*!40000 ALTER TABLE `acos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `acteurs`
--

DROP TABLE IF EXISTS `acteurs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acteurs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `typeacteur_id` int(11) NOT NULL DEFAULT '0',
  `nom` varchar(50) NOT NULL DEFAULT '',
  `prenom` varchar(50) NOT NULL DEFAULT '',
  `salutation` varchar(50) NOT NULL,
  `titre` varchar(250) DEFAULT NULL,
  `position` int(11) NOT NULL,
  `date_naissance` date DEFAULT NULL,
  `adresse1` varchar(100) NOT NULL,
  `adresse2` varchar(100) NOT NULL,
  `cp` varchar(20) NOT NULL,
  `ville` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telfixe` varchar(20) DEFAULT NULL,
  `telmobile` varchar(20) DEFAULT NULL,
  `note` varchar(255) NOT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `index` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acteurs`
--

LOCK TABLES `acteurs` WRITE;
/*!40000 ALTER TABLE `acteurs` DISABLE KEYS */;
/*!40000 ALTER TABLE `acteurs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `acteurs_services`
--

DROP TABLE IF EXISTS `acteurs_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acteurs_services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `acteur_id` int(11) NOT NULL DEFAULT '0',
  `service_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acteurs_services`
--

LOCK TABLES `acteurs_services` WRITE;
/*!40000 ALTER TABLE `acteurs_services` DISABLE KEYS */;
/*!40000 ALTER TABLE `acteurs_services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ados`
--

DROP TABLE IF EXISTS `ados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ados` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alias` varchar(255) NOT NULL DEFAULT '',
  `lft` int(10) DEFAULT NULL,
  `rght` int(10) DEFAULT NULL,
  `parent_id` int(10) NOT NULL DEFAULT '0',
  `model` varchar(255) DEFAULT NULL,
  `foreign_key` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ados`
--

LOCK TABLES `ados` WRITE;
/*!40000 ALTER TABLE `ados` DISABLE KEYS */;
INSERT INTO `ados` VALUES (1,'Nature:Délibérations',1,2,0,'Nature',1),(2,'Nature:Arrêtés Réglementaires',3,4,0,'Nature',2),(3,'Nature:Arrêtés Individuels',5,6,0,'Nature',3),(4,'Nature:Contrats et conventions',7,8,0,'Nature',4);
/*!40000 ALTER TABLE `ados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `annexes`
--

DROP TABLE IF EXISTS `annexes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `annexes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model` varchar(255) NOT NULL,
  `foreign_key` int(11) NOT NULL,
  `joindre_ctrl_legalite` tinyint(1) NOT NULL DEFAULT '0',
  `titre` varchar(50) NOT NULL,
  `filename` varchar(75) NOT NULL,
  `filetype` varchar(255) NOT NULL,
  `size` int(11) NOT NULL,
  `data` mediumblob NOT NULL,
  `filename_pdf` varchar(75) NOT NULL,
  `data_pdf` mediumblob NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `model_foreign_key` (`model`,`foreign_key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `annexes`
--

LOCK TABLES `annexes` WRITE;
/*!40000 ALTER TABLE `annexes` DISABLE KEYS */;
/*!40000 ALTER TABLE `annexes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aros`
--

DROP TABLE IF EXISTS `aros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aros` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `foreign_key` int(10) unsigned DEFAULT NULL,
  `alias` varchar(255) NOT NULL DEFAULT '',
  `lft` int(10) DEFAULT NULL,
  `rght` int(10) DEFAULT NULL,
  `parent_id` int(10) NOT NULL DEFAULT '0',
  `model` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aros`
--

LOCK TABLES `aros` WRITE;
/*!40000 ALTER TABLE `aros` DISABLE KEYS */;
INSERT INTO `aros` VALUES (1,2,'Administrateur',1,4,0,'Profil'),(2,1,'admin',2,3,1,'Utilisateur'),(3,1,'D?faut',5,6,0,'Profil'),(4,3,'R?dacteur',7,8,0,'Profil'),(5,5,'Service assembl',9,10,0,'Profil'),(6,4,'Valideur',11,12,0,'Profil');
/*!40000 ALTER TABLE `aros` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aros_acos`
--

DROP TABLE IF EXISTS `aros_acos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aros_acos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `aro_id` int(10) unsigned NOT NULL,
  `aco_id` int(10) unsigned NOT NULL,
  `_create` char(2) NOT NULL DEFAULT '0',
  `_read` char(2) NOT NULL DEFAULT '0',
  `_update` char(2) NOT NULL DEFAULT '0',
  `_delete` char(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aros_acos`
--

LOCK TABLES `aros_acos` WRITE;
/*!40000 ALTER TABLE `aros_acos` DISABLE KEYS */;
INSERT INTO `aros_acos` VALUES (1,1,1,'1','1','1','1'),(2,1,2,'-1','-1','-1','-1'),(3,1,9,'-1','-1','-1','-1'),(4,1,14,'-1','-1','-1','-1'),(6,1,22,'1','1','1','1'),(7,1,28,'1','1','1','1'),(8,1,31,'1','1','1','1'),(9,1,39,'-1','-1','-1','-1'),(10,3,1,'1','1','1','1'),(11,3,2,'-1','-1','-1','-1'),(12,3,9,'-1','-1','-1','-1'),(13,3,14,'-1','-1','-1','-1'),(15,3,22,'-1','-1','-1','-1'),(16,3,28,'-1','-1','-1','-1'),(17,3,31,'-1','-1','-1','-1'),(18,3,39,'-1','-1','-1','-1'),(19,4,1,'1','1','1','1'),(20,4,2,'1','1','1','1'),(21,4,9,'-1','-1','-1','-1'),(22,4,14,'-1','-1','-1','-1'),(24,4,22,'-1','-1','-1','-1'),(25,4,28,'-1','-1','-1','-1'),(26,4,31,'-1','-1','-1','-1'),(27,4,39,'-1','-1','-1','-1'),(28,5,1,'1','1','1','1'),(29,5,2,'1','1','1','1'),(30,5,9,'1','1','1','1'),(31,5,14,'1','1','1','1'),(33,5,22,'-1','-1','-1','-1'),(34,5,28,'1','1','1','1'),(35,5,31,'-1','-1','-1','-1'),(36,5,39,'-1','-1','-1','-1'),(37,6,1,'1','1','1','1'),(38,6,2,'1','1','1','1'),(39,6,3,'-1','-1','-1','-1'),(40,6,4,'-1','-1','-1','-1'),(41,6,7,'-1','-1','-1','-1'),(42,6,9,'-1','-1','-1','-1'),(43,6,14,'-1','-1','-1','-1'),(45,6,22,'-1','-1','-1','-1'),(46,6,28,'-1','-1','-1','-1'),(47,6,31,'-1','-1','-1','-1'),(48,6,39,'-1','-1','-1','-1'),(49,2,1,'1','1','1','1'),(50,2,2,'-1','-1','-1','-1'),(51,2,3,'-1','-1','-1','-1'),(52,2,4,'-1','-1','-1','-1'),(53,2,5,'-1','-1','-1','-1'),(54,2,6,'-1','-1','-1','-1'),(55,2,7,'-1','-1','-1','-1'),(56,2,8,'-1','-1','-1','-1'),(57,2,9,'-1','-1','-1','-1'),(58,2,10,'-1','-1','-1','-1'),(59,2,11,'-1','-1','-1','-1'),(60,2,12,'-1','-1','-1','-1'),(61,2,13,'-1','-1','-1','-1'),(62,2,14,'-1','-1','-1','-1'),(63,2,15,'-1','-1','-1','-1'),(64,2,16,'-1','-1','-1','-1'),(65,2,17,'-1','-1','-1','-1'),(66,2,42,'-1','-1','-1','-1'),(67,2,43,'-1','-1','-1','-1'),(68,2,44,'-1','-1','-1','-1'),(69,2,45,'-1','-1','-1','-1'),(70,2,46,'-1','-1','-1','-1'),(71,2,22,'1','1','1','1'),(72,2,23,'1','1','1','1'),(73,2,25,'1','1','1','1'),(74,2,26,'1','1','1','1'),(75,2,47,'1','1','1','1'),(76,2,28,'1','1','1','1'),(77,2,29,'1','1','1','1'),(78,2,30,'1','1','1','1'),(79,2,31,'1','1','1','1'),(80,2,32,'1','1','1','1'),(81,2,33,'1','1','1','1'),(82,2,34,'1','1','1','1'),(83,2,35,'1','1','1','1'),(84,2,36,'1','1','1','1'),(85,2,37,'1','1','1','1'),(86,2,38,'1','1','1','1'),(87,2,39,'-1','-1','-1','-1'),(88,2,48,'-1','-1','-1','-1'),(89,2,40,'-1','-1','-1','-1'),(90,2,49,'-1','-1','-1','-1'),(91,2,50,'-1','-1','-1','-1'),(92,2,51,'-1','-1','-1','-1'),(93,2,52,'1','1','1','1'),(94,2,53,'1','1','1','1'),(95,2,54,'-1','-1','-1','-1'),(96,2,55,'-1','-1','-1','-1'),(97,2,56,'-1','-1','-1','-1'),(98,2,57,'-1','-1','-1','-1'),(99,2,58,'-1','-1','-1','-1'),(100,2,59,'-1','-1','-1','-1'),(101,2,60,'-1','-1','-1','-1');
/*!40000 ALTER TABLE `aros_acos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aros_ados`
--

DROP TABLE IF EXISTS `aros_ados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aros_ados` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `aro_id` int(10) unsigned NOT NULL,
  `ado_id` int(10) unsigned NOT NULL,
  `_create` char(2) NOT NULL DEFAULT '0',
  `_read` char(2) NOT NULL DEFAULT '0',
  `_update` char(2) NOT NULL DEFAULT '0',
  `_delete` char(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aros_ados`
--

LOCK TABLES `aros_ados` WRITE;
/*!40000 ALTER TABLE `aros_ados` DISABLE KEYS */;
INSERT INTO `aros_ados` VALUES (1,2,1,'1','1','1','1'),(2,1,1,'1','1','1','1'),(3,1,1,'1','1','1','1'),(4,3,1,'1','1','1','1'),(5,5,1,'1','1','1','1'),(6,4,1,'1','1','1','1'),(7,1,2,'-1','-1','-1','-1'),(8,1,3,'-1','-1','-1','-1'),(9,1,4,'-1','-1','-1','-1');
/*!40000 ALTER TABLE `aros_ados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `collectivites`
--

DROP TABLE IF EXISTS `collectivites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `collectivites` (
  `id` int(11) NOT NULL,
  `nom` varchar(30) NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `CP` int(11) NOT NULL,
  `ville` varchar(255) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `collectivites`
--

LOCK TABLES `collectivites` WRITE;
/*!40000 ALTER TABLE `collectivites` DISABLE KEYS */;
INSERT INTO `collectivites` VALUES (1,'Adullact','335, Cour Messier',34000,'Montpellier','0467650588');
/*!40000 ALTER TABLE `collectivites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commentaires`
--

DROP TABLE IF EXISTS `commentaires`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commentaires` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `delib_id` int(11) NOT NULL DEFAULT '0',
  `agent_id` int(11) NOT NULL DEFAULT '0',
  `texte` varchar(1000) DEFAULT NULL,
  `pris_en_compte` tinyint(4) NOT NULL DEFAULT '0',
  `commentaire_auto` tinyint(1) NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `deliberation_id` (`delib_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commentaires`
--

LOCK TABLES `commentaires` WRITE;
/*!40000 ALTER TABLE `commentaires` DISABLE KEYS */;
/*!40000 ALTER TABLE `commentaires` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `compteurs`
--

DROP TABLE IF EXISTS `compteurs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `compteurs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `commentaire` varchar(255) NOT NULL,
  `def_compteur` varchar(255) NOT NULL,
  `sequence_id` int(11) NOT NULL,
  `def_reinit` varchar(255) NOT NULL,
  `val_reinit` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nom` (`nom`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `compteurs`
--

LOCK TABLES `compteurs` WRITE;
/*!40000 ALTER TABLE `compteurs` DISABLE KEYS */;
/*!40000 ALTER TABLE `compteurs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `deliberations`
--

DROP TABLE IF EXISTS `deliberations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deliberations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nature_id` int(11) NOT NULL DEFAULT '1',
  `circuit_id` int(11) DEFAULT '0',
  `theme_id` int(11) NOT NULL DEFAULT '0',
  `service_id` int(11) NOT NULL DEFAULT '0',
  `vote_id` int(11) NOT NULL DEFAULT '0',
  `redacteur_id` int(11) NOT NULL DEFAULT '0',
  `rapporteur_id` int(11) DEFAULT '0',
  `seance_id` int(11) DEFAULT NULL,
  `position` int(4) NOT NULL,
  `anterieure_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `objet` varchar(1000) NOT NULL,
  `titre` varchar(1000) NOT NULL,
  `num_delib` varchar(15) NOT NULL,
  `num_pref` varchar(100) NOT NULL,
  `tdt_id` int(11) DEFAULT NULL,
  `dateAR` varchar(100) DEFAULT NULL,
  `texte_projet` longblob,
  `texte_projet_name` varchar(75) NOT NULL,
  `texte_projet_type` varchar(255) NOT NULL,
  `texte_projet_size` int(11) NOT NULL,
  `texte_synthese` longblob,
  `texte_synthese_name` varchar(75) NOT NULL,
  `texte_synthese_type` varchar(255) NOT NULL,
  `texte_synthese_size` int(11) NOT NULL,
  `deliberation` longblob,
  `deliberation_name` varchar(75) NOT NULL,
  `deliberation_type` varchar(255) NOT NULL,
  `deliberation_size` int(11) NOT NULL,
  `date_limite` date DEFAULT NULL,
  `date_envoi` datetime DEFAULT NULL,
  `etat` int(11) NOT NULL DEFAULT '0',
  `etat_parapheur` tinyint(4) DEFAULT NULL,
  `etat_asalae` tinyint(1) DEFAULT NULL,
  `reporte` tinyint(1) NOT NULL DEFAULT '0',
  `montant` int(10) NOT NULL,
  `debat` longblob NOT NULL,
  `debat_name` varchar(255) NOT NULL,
  `debat_size` int(11) NOT NULL,
  `debat_type` varchar(255) NOT NULL,
  `avis` int(1) NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `vote_nb_oui` int(3) NOT NULL,
  `vote_nb_non` int(3) NOT NULL,
  `vote_nb_abstention` int(3) NOT NULL,
  `vote_nb_retrait` int(3) NOT NULL,
  `vote_commentaire` varchar(500) NOT NULL,
  `delib_pdf` longblob,
  `signature` blob,
  `signee` tinyint(1) DEFAULT NULL,
  `commission` longblob NOT NULL,
  `commission_size` int(11) NOT NULL,
  `commission_type` varchar(255) NOT NULL,
  `commission_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `deliberations`
--

LOCK TABLES `deliberations` WRITE;
/*!40000 ALTER TABLE `deliberations` DISABLE KEYS */;
/*!40000 ALTER TABLE `deliberations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `historiques`
--

DROP TABLE IF EXISTS `historiques`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `historiques` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `delib_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `circuit_id` int(11) NOT NULL,
  `commentaire` varchar(1000) NOT NULL,
  `modified` datetime NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `deliberation_id` (`delib_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historiques`
--

LOCK TABLES `historiques` WRITE;
/*!40000 ALTER TABLE `historiques` DISABLE KEYS */;
/*!40000 ALTER TABLE `historiques` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `infosupdefs`
--

DROP TABLE IF EXISTS `infosupdefs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `infosupdefs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `commentaire` varchar(255) NOT NULL,
  `ordre` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `taille` int(11) DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `val_initiale` varchar(255) DEFAULT NULL,
  `recherche` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `infosupdefs`
--

LOCK TABLES `infosupdefs` WRITE;
/*!40000 ALTER TABLE `infosupdefs` DISABLE KEYS */;
/*!40000 ALTER TABLE `infosupdefs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `infosuplistedefs`
--

DROP TABLE IF EXISTS `infosuplistedefs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `infosuplistedefs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `infosupdef_id` int(11) NOT NULL,
  `ordre` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `INFOSUPDEF_ID_ORDRE` (`infosupdef_id`,`ordre`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `infosuplistedefs`
--

LOCK TABLES `infosuplistedefs` WRITE;
/*!40000 ALTER TABLE `infosuplistedefs` DISABLE KEYS */;
/*!40000 ALTER TABLE `infosuplistedefs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `infosups`
--

DROP TABLE IF EXISTS `infosups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `infosups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deliberation_id` int(11) NOT NULL,
  `infosupdef_id` int(11) NOT NULL,
  `text` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_size` int(11) NOT NULL,
  `file_type` varchar(255) NOT NULL,
  `content` longblob NOT NULL,
  PRIMARY KEY (`id`),
  KEY `deliberation_id` (`deliberation_id`),
  KEY `infosupdef_id` (`infosupdef_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `infosups`
--

LOCK TABLES `infosups` WRITE;
/*!40000 ALTER TABLE `infosups` DISABLE KEYS */;
/*!40000 ALTER TABLE `infosups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `listepresences`
--

DROP TABLE IF EXISTS `listepresences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `listepresences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `delib_id` int(11) NOT NULL,
  `acteur_id` int(11) NOT NULL,
  `present` tinyint(1) NOT NULL,
  `mandataire` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `deliberation_id` (`delib_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `listepresences`
--

LOCK TABLES `listepresences` WRITE;
/*!40000 ALTER TABLE `listepresences` DISABLE KEYS */;
/*!40000 ALTER TABLE `listepresences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `models`
--

DROP TABLE IF EXISTS `models`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `models` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `modele` varchar(100) NOT NULL,
  `type` varchar(100) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `size` int(11) NOT NULL,
  `extension` varchar(255) DEFAULT NULL,
  `content` longblob NOT NULL,
  `recherche` tinyint(1) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `models`
--

LOCK TABLES `models` WRITE;
/*!40000 ALTER TABLE `models` DISABLE KEYS */;
INSERT INTO `models` VALUES (1,'Mod?le d?faut','Document','projet.odt',7908,'application/vnd.oasis.opendocument.text','PK\0\0\0\0\0j�:^�2\'\0\0\0\'\0\0\0\0\0\0mimetypeapplication/vnd.oasis.opendocument.textPK\0\0\0\0\0j�:\0\0\0\0\0\0\0\0\0\0\0\0\Z\0\0\0Configurations2/statusbar/PK\0\0\0j�:\0\0\0\0\0\0\0\0\0\0\0\0\'\0\0\0Configurations2/accelerator/current.xml\0PK\0\0\0\0\0\0\0\0\0\0\0PK\0\0\0\0\0j�:\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0Configurations2/floater/PK\0\0\0\0\0j�:\0\0\0\0\0\0\0\0\0\0\0\0\Z\0\0\0Configurations2/popupmenu/PK\0\0\0\0\0j�:\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0Configurations2/progressbar/PK\0\0\0\0\0j�:\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0Configurations2/menubar/PK\0\0\0\0\0j�:\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0Configurations2/toolbar/PK\0\0\0\0\0j�:\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0Configurations2/images/Bitmaps/PK\0\0\0j�:\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0content.xmlśMo�6�����)��6n�Ţ�6����E�L)R%)��IY��8���DԐ9$�����]-&�\rWr�M���	�TU\\>-�?����>��p�V+NټR����9U���	Ԗf�.�V˹\"���$53sK�a�Pk[�=+����{㸶e;;����%�x�7�kW�l�Vv��Ը�J���3\"_)�z��Oz�\\�����6��n�Wۛ+������m�����]�j�*Z0��ӫiq���%c��l�.ɶ.��\Zb�٬�����y��\Z�&z�����齩�O�M׭�]_�����?�ׂ�ǲ���UT�f�0�u\\_)�w�U�wwv}�S�#���[�-ӑ9}՜A{���%��ݴ\0��m�2��s��PaV�۽��.6��×G�f59\Z�s.�%��g�`��vs�v\r��M�7U��	p�j�Qaf�ʑ2O����)4E_�9�W���bT�������I�v]Yd_y]�f򇪉�|U�X����5�E�#i����4�g��Uɟ���\\kg=�h���6Ds\'�Y1���D����Ft�l�1���g�L�j=�b�\"�1]����T\\�䮜�<n9�};Ѳ(U��/\\軿�а[��}C�_Tq���Uk!��\\����������\Z:����\Z�v��Z�?������\\�Zk����}����D�,��������e�-٠\'R�K��`��\rhk?��J!�fư$�$�l8p,$O�Z�,H���Y�\ZX�&��`�z#ԲV��h��0Pf�$�b,�����K��e\Z��N\0�) 3Tf�AZ0	�]r��r�X��(���\0^A>�W�pi�O��6a\"�R0�bp@k�ܡ8{T��Q�,�\Z��E(lL��Q!RaSN�6��X�@3tD.lD�^ؔ��WuŰ9>��٢�V�)�W%4�Jh��^�0!q���ȶfZ-+&x�N^p|�]/�10 \Zb>�ڀɒՐl��khu�ܟ������b�D�\r�!�)��(�|�Hi�� �8�$��{d1f�m9�\r��W�?�в�(��G�$6�:7&��$�;k�4J����Ƥ`��a�,\0�Lt�1Q��|��Yl��s\Z6�NK����9\r���E�&ЃN@Y�����\r.$�tPP\\̩�`���8�7�	rOQv����0K� �%V!n��2xl�G*�3��#.���9�^<{�E��A&�͒��3I\\L|6�K��(�W���������3�R�I�J�1I�\Z�IH<&�e��$�\n|�,�(��c�⎺ԇڎ�\ZJ;&,VvLN\'��$����tʑ2�����h���8)ذ��\n6�4�$��R�!�43ذA�A��p�\079��8��N�2�4\'��t��?(rR9N�ŉ�8�\n������V|�)o�x�%��+�&4�?��C3���Օ��Q���*|�w�:�\"��?PK��U�\0\0�?\0\0PK\0\0\0j�:\0\0\0\0\0\0\0\0\0\0\0\0\n\0\0\0styles.xml�YK��6��W*�m˻������\"h�$�$�5�%�b\"�I��_�R�h[�j)����|�\'9~�fW�\rS�Kq��i4b\"�)���o�m�����r��	[�2�\n&�f�3=��B/�.��XH��^Z0�0�B�L�M��{aU�+l�v��6lg�nFރ�t9\\�ew��n�nF^�i�}%�n�霬$IdQRÏP�r.��E�1�b2�n����X��$���Km\0\'\r_Y��r�Ʉ���I<�\'��`�Ň�!$QK���\Zz�U�Y��ͺ�4IF��ذ̇�J���*\r��d=>�������]��yL�(^>���K)���%��;�N�\'�;�ޞe�*n�\nؓ��	͓���2\Z��� l�a\Z����8��5j%�>�h�Hʒ\\߿v��,��7��.���e�GeA�胜��#O�����.���R�~��֣с�B�L0���\n�8Jn�\rU�I4��al�k\0,��Z?֟�+���\n{A<C �a�C�&}N��]_��S��U^w+/�ƸV��xy����\n�T�\rk�Bg4�[�53dwM�W	�� \n\n��ˈ.i՟dR��\0���:�=˼A�)+��P�\'�2k��p�-7q�tEsDAI�\n��H�Ohe$���)����eF�c��^�\r��x\n�V��犘�Ap�2�cx��Az�П�Ӳ�\'��v�}r�J30�@�Z��%t2�*��+�i��Ƴ�ص��uEװ�Rv!��0\n�������@U$ߘ��e����t|S6���=��n�I�\"ORt�ޘ��|$��v�mhVpkփ�\Z�l������e��r)HN�lf� �~���������-�������(�rHO�J���&n��0tK�g�2O���s���G!j��tX�p�Z�M��U�R�F��8+(�>g\'Le��#�g�}f�E-ga�W�R*L\r�;��D9-5�s%�G�a�(I�1V#��dx��$|Hq����\'H���4��}9�\Z�A>��u*�/F� �{��B�%�P�n���̦_�2�w�z��TA����x�g���Ki^}�\'ǳ�dml��\r��[�������o�Ge�͝�e~���f0\ni灀�rT�2���M���� x���v�q�A�y�9��j�\"Ё�g����Ē�����>2��,�K0;rc\n{��M�{\'�S�i���b��Z����8k��5IM������\r�0��5{����V�Yｐ+�V���{Nt��l��K:�\0�Q1Jss>̯z�����%P��$<o�$�i�kv���.���⺹P\\�.�o���Bq�/W<����B�B\Z����_W�>�F\r�ԭm%���.�qݼ�ToC�\nQՋ~�&����	��{\\�����3|<�p�L�}\0y7@/-�\"�R�ۯ�,�>s��vR�e�ZHk���LM�\"Q�W)�����Jk����<!��o\Zk�6݃w�7eG<Gw:K��ÙM�swO�_g�ʟ�_�9b�,h�T�Bk_KWZn��kϥ�h�t��jH}�����@R�]s\Z|��3��A�ҋs֘���m�ħY28��G�x\Zw����Xh����y�ŀ[W���\r7���;2�yj��p���P��Q�؏Ix�`�TPy��V\n���z%��z���\r�H�������\0PK%�}\0\0�\0\0PK\0\0\0\0\0j�:��^$R\0\0R\0\0\0\0\0meta.xml<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<office:document-meta xmlns:office=\"urn:oasis:names:tc:opendocument:xmlns:office:1.0\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" xmlns:dc=\"http://purl.org/dc/elements/1.1/\" xmlns:meta=\"urn:oasis:names:tc:opendocument:xmlns:meta:1.0\" xmlns:ooo=\"http://openoffice.org/2004/office\" office:version=\"1.1\"><office:meta><meta:generator>OpenOffice.org/2.4$Linux OpenOffice.org_project/680m17$Build-9310</meta:generator><meta:initial-creator>francois</meta:initial-creator><meta:creation-date>2009-04-07T14:15:40</meta:creation-date><dc:creator>francois</dc:creator><dc:date>2009-04-07T15:16:46</dc:date><meta:editing-cycles>4</meta:editing-cycles><meta:editing-duration>PT55M49S</meta:editing-duration><meta:user-defined meta:name=\"Info 1\"/><meta:user-defined meta:name=\"Info 2\"/><meta:user-defined meta:name=\"Info 3\"/><meta:user-defined meta:name=\"Info 4\"/><meta:document-statistic meta:table-count=\"0\" meta:image-count=\"0\" meta:object-count=\"0\" meta:page-count=\"1\" meta:paragraph-count=\"0\" meta:word-count=\"0\" meta:character-count=\"0\"/></office:meta></office:document-meta>PK\0\0\0j�:\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0Thumbnails/thumbnail.png��s���b``���p	�[8؀������{�8�T�y{i#\'�ρ\r|?�?���t�C��Ûw��~�2��9K&xrrV��o�ʓ����Ԏ_y2cTpTp�����3\n�*L���~.��\0PK�׃�|\0\0\0�\0\0PK\0\0\0j�:\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0settings.xml�Z]s�:}��\"�w\n$i�0	 �7-\r�fn߄��n��#���]Ɇ!|$�F��֖�jW�{���|Y���(�Q����d�����q�.}������P�0Lf MI�14D��t�k��� Q��Ls]�l�f�\Z� W�j��k�X�d!�|�\r��ĵry>��_|@5)W������jh�r�\'ǚJGo�Bĵ!;!]�3v^�\\���g�\"7BS\r�8�ܯ�dҟ70��9�ۥ�d���a��Z�o��9�h|Cb�ޘeLo�4A�z��rS�9�c��tu}q^��Gf������Ű�>��]x��zY�>�����LN@o!\n`2��@>���p��\'Fp}̄>\Z�4cq����j��9Tjy\\��j�(���M�Yr(�.*��j���#^J�!{�n��?T$���sI�Dcp�w՟.�9��q6$��l��\"��m\ZT�a�����z\0BQ[уռ��fiz�U��D.��Q� Q���	/5���2J�A�B�\rN�+{�dL,��v��,_�wc{}�,�8�H?$��7�)`�C��1�����W�X��Q�x�$\nS�����!2L�dg��U���0�����9S�����ߟ߄	��Y�=ᫌޜ_�쏪����,{̏��HT��J��=-L�{�z����Y�@[5~r�p�����`�\n��6��\"��Y,�ߞ�f�Š���d[]��b\0G{��x�G��Vty��L��\Zbqʧ�݋FL��e�KFm��wKv��a��B��o�S�i$����-���Kt��t��]L�m�nȨ)�|�t��ZL��pj�W�7�D�,�	9�[Y��;�~��`5a&�����n����n�D�<�\"?q,��\Z�3���m�8}܀����ު+[�i.v�|8b�.�r�:�����\rM⮗��$�ʰ!�DR��=��-3�w�%x��kYӐ!�6DO����X:r��V�m�Mj���\0�׻w����b�(:��h8��ɴ;�ׇ���[3]��=���c}��\0�!�2�zؽB8~F�-\n�7k�\Z��<)w%��\n����Q��O�%�����u�6�u��)+��ɏwnZ�s��}N�I��m��*�b?>�&�:�X�\Z�(+���F/[����T�/�o���R@��sg�ξ-&CX�5��a~ƈ,v��*9��u�=ц���l�7ӟL&L4�g?��`O/0��B�c�Pc�[�?�ug���j�[br�b�>�6[R�Q$vMEx��=Rv92�&�\'N��_Y^�ݭ�q��~N�t�\r�mc�B�rAy�oPHǶwNl���}�(�|�/�[��PK&�y�6\0\0� \0\0PK\0\0\0j�:\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0META-INF/manifest.xml��Kj�0@�=���V�U1q-��&���f��W��6��X;	��F#�h��[S�0���Oͣ��)�k7v�c�^����aa����Ӡ�����HѵHS��\"��Z��^%��ۯ��ɴ|�.�A���x�.2�5�|�	�h��;�7GWs�h�,.��dL����B�%�My�n�c�� �Y\'�@,���`��(U�q:b�bqW�`<0�R�O �G?F�r7=�^�ޛbpmaD���-*긓��_PrS�4I7�Z��O�HN�z����b��K|0H�c-2��x��d7�!ɧa�87|��\"s�ϩ]����PK5b�9>\0\0J\0\0PK\0\0\0\0\0\0j�:^�2\'\0\0\0\'\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0mimetypePK\0\0\0\0\0\0j�:\0\0\0\0\0\0\0\0\0\0\0\0\Z\0\0\0\0\0\0\0\0\0\0\0\0\0M\0\0\0Configurations2/statusbar/PK\0\0\0\0j�:\0\0\0\0\0\0\0\0\0\0\0\'\0\0\0\0\0\0\0\0\0\0\0\0\0�\0\0\0Configurations2/accelerator/current.xmlPK\0\0\0\0\0\0j�:\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0�\0\0\0Configurations2/floater/PK\0\0\0\0\0\0j�:\0\0\0\0\0\0\0\0\0\0\0\0\Z\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0Configurations2/popupmenu/PK\0\0\0\0\0\0j�:\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0J\0\0Configurations2/progressbar/PK\0\0\0\0\0\0j�:\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0�\0\0Configurations2/menubar/PK\0\0\0\0\0\0j�:\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0�\0\0Configurations2/toolbar/PK\0\0\0\0\0\0j�:\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0�\0\0Configurations2/images/Bitmaps/PK\0\0\0\0j�:��U�\0\0�?\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0-\0\0content.xmlPK\0\0\0\0j�:%�}\0\0�\0\0\n\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0styles.xmlPK\0\0\0\0\0\0j�:��^$R\0\0R\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0�\0\0meta.xmlPK\0\0\0\0j�:�׃�|\0\0\0�\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0-\0\0Thumbnails/thumbnail.pngPK\0\0\0\0j�:&�y�6\0\0� \0\0\0\0\0\0\0\0\0\0\0\0\0\0\0�\0\0settings.xmlPK\0\0\0\0j�:5b�9>\0\0J\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0_\0\0META-INF/manifest.xmlPK\0\0\0\0\0\0�\0\0�\Z\0\0\0\0',NULL,'0000-00-00 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `models` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `natures`
--

DROP TABLE IF EXISTS `natures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `natures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(100) NOT NULL,
  `code` varchar(3) NOT NULL,
  `dua` varchar(50) DEFAULT NULL,
  `sortfinal` varchar(50) DEFAULT NULL,
  `communicabilite` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `natures`
--

LOCK TABLES `natures` WRITE;
/*!40000 ALTER TABLE `natures` DISABLE KEYS */;
INSERT INTO `natures` VALUES (1,'D?lib?rations','DE',NULL,NULL,NULL),(2,'Arr?t?s R?glementaires','AR',NULL,NULL,NULL),(3,'Arr?t?s Individuels','AI',NULL,NULL,NULL),(4,'Contrats et conventions','CC',NULL,NULL,NULL);
/*!40000 ALTER TABLE `natures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profils`
--

DROP TABLE IF EXISTS `profils`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `profils` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT '0',
  `libelle` varchar(100) NOT NULL DEFAULT '',
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profils`
--

LOCK TABLES `profils` WRITE;
/*!40000 ALTER TABLE `profils` DISABLE KEYS */;
INSERT INTO `profils` VALUES (1,0,'D?faut',1,'2009-04-06 11:06:17','2009-04-06 11:06:17'),(2,0,'Administrateur',1,'2009-04-06 11:06:39','2009-04-06 11:06:39'),(3,0,'R?dacteur',1,'2009-04-06 11:06:48','2009-04-06 11:06:48'),(4,0,'Valideur',1,'2009-04-06 11:06:54','2009-04-06 11:06:54'),(5,0,'Service assembl',1,'2009-04-06 11:07:03','2009-04-06 11:07:03');
/*!40000 ALTER TABLE `profils` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seances`
--

DROP TABLE IF EXISTS `seances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `seances` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_convocation` datetime DEFAULT NULL,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `traitee` int(1) NOT NULL DEFAULT '0',
  `commentaire` varchar(500) DEFAULT NULL,
  `secretaire_id` int(11) DEFAULT NULL,
  `president_id` int(10) DEFAULT NULL,
  `debat_global` longblob NOT NULL,
  `debat_global_name` varchar(75) NOT NULL,
  `debat_global_size` int(11) NOT NULL,
  `debat_global_type` varchar(255) NOT NULL,
  `pv_figes` tinyint(4) DEFAULT NULL,
  `pv_sommaire` longblob,
  `pv_complet` longblob,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seances`
--

LOCK TABLES `seances` WRITE;
/*!40000 ALTER TABLE `seances` DISABLE KEYS */;
/*!40000 ALTER TABLE `seances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sequences`
--

DROP TABLE IF EXISTS `sequences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sequences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `commentaire` varchar(255) NOT NULL,
  `num_sequence` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nom` (`nom`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sequences`
--

LOCK TABLES `sequences` WRITE;
/*!40000 ALTER TABLE `sequences` DISABLE KEYS */;
/*!40000 ALTER TABLE `sequences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `order` varchar(50) NOT NULL,
  `libelle` varchar(100) NOT NULL,
  `circuit_defaut_id` int(11) NOT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lft` int(11) DEFAULT '0',
  `rght` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `services`
--

LOCK TABLES `services` WRITE;
/*!40000 ALTER TABLE `services` DISABLE KEYS */;
INSERT INTO `services` VALUES (1,0,'','D?faut',0,1,'2009-04-06 08:35:48','2010-10-15 18:00:31',1,2);
/*!40000 ALTER TABLE `services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tdt_messages`
--

DROP TABLE IF EXISTS `tdt_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tdt_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `delib_id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `type_message` int(3) NOT NULL,
  `reponse` int(3) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tdt_messages`
--

LOCK TABLES `tdt_messages` WRITE;
/*!40000 ALTER TABLE `tdt_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `tdt_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `themes`
--

DROP TABLE IF EXISTS `themes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `themes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `order` varchar(50) NOT NULL,
  `libelle` varchar(500) DEFAULT NULL,
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lft` int(11) DEFAULT '0',
  `rght` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `index` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `themes`
--

LOCK TABLES `themes` WRITE;
/*!40000 ALTER TABLE `themes` DISABLE KEYS */;
/*!40000 ALTER TABLE `themes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `typeacteurs`
--

DROP TABLE IF EXISTS `typeacteurs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `typeacteurs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `commentaire` varchar(255) NOT NULL,
  `elu` tinyint(1) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nom` (`nom`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `typeacteurs`
--

LOCK TABLES `typeacteurs` WRITE;
/*!40000 ALTER TABLE `typeacteurs` DISABLE KEYS */;
/*!40000 ALTER TABLE `typeacteurs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `typeseances`
--

DROP TABLE IF EXISTS `typeseances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `typeseances` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(100) NOT NULL,
  `retard` int(11) DEFAULT '0',
  `action` tinyint(2) NOT NULL,
  `compteur_id` int(11) NOT NULL,
  `modelprojet_id` int(11) NOT NULL,
  `modeldeliberation_id` int(11) NOT NULL,
  `modelconvocation_id` int(11) NOT NULL,
  `modelordredujour_id` int(11) NOT NULL,
  `modelpvsommaire_id` int(11) NOT NULL,
  `modelpvdetaille_id` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `typeseances`
--

LOCK TABLES `typeseances` WRITE;
/*!40000 ALTER TABLE `typeseances` DISABLE KEYS */;
/*!40000 ALTER TABLE `typeseances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `typeseances_acteurs`
--

DROP TABLE IF EXISTS `typeseances_acteurs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `typeseances_acteurs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `typeseance_id` int(11) NOT NULL,
  `acteur_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `typeseances_acteurs`
--

LOCK TABLES `typeseances_acteurs` WRITE;
/*!40000 ALTER TABLE `typeseances_acteurs` DISABLE KEYS */;
/*!40000 ALTER TABLE `typeseances_acteurs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `typeseances_natures`
--

DROP TABLE IF EXISTS `typeseances_natures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `typeseances_natures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `typeseance_id` int(11) NOT NULL,
  `nature_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `typeseances_natures`
--

LOCK TABLES `typeseances_natures` WRITE;
/*!40000 ALTER TABLE `typeseances_natures` DISABLE KEYS */;
/*!40000 ALTER TABLE `typeseances_natures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `typeseances_typeacteurs`
--

DROP TABLE IF EXISTS `typeseances_typeacteurs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `typeseances_typeacteurs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `typeseance_id` int(11) NOT NULL,
  `typeacteur_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `typeseances_typeacteurs`
--

LOCK TABLES `typeseances_typeacteurs` WRITE;
/*!40000 ALTER TABLE `typeseances_typeacteurs` DISABLE KEYS */;
/*!40000 ALTER TABLE `typeseances_typeacteurs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `profil_id` int(11) NOT NULL DEFAULT '0',
  `statut` int(11) NOT NULL DEFAULT '0',
  `login` varchar(50) NOT NULL DEFAULT '',
  `note` varchar(25) NOT NULL,
  `circuit_defaut_id` int(11) DEFAULT NULL,
  `password` varchar(100) NOT NULL DEFAULT '',
  `nom` varchar(50) NOT NULL DEFAULT '',
  `prenom` varchar(50) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `telfixe` varchar(20) DEFAULT NULL,
  `telmobile` varchar(20) DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `accept_notif` tinyint(1) DEFAULT NULL,
  `mail_refus` tinyint(1) NOT NULL,
  `mail_traitement` tinyint(1) NOT NULL,
  `mail_insertion` tinyint(1) NOT NULL,
  `position` int(3) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,2,0,'admin','',NULL,'21232f297a57a5a743894a0e4a801fc3','admin','admin','francois.desmaretz@adullact.org','0000000000','0000000000','1999-11-30',0,0,0,0,0,'0000-00-00 00:00:00','2010-10-15 18:05:03');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_services`
--

DROP TABLE IF EXISTS `users_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `service_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_services`
--

LOCK TABLES `users_services` WRITE;
/*!40000 ALTER TABLE `users_services` DISABLE KEYS */;
INSERT INTO `users_services` VALUES (2,1,1);
/*!40000 ALTER TABLE `users_services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `votes`
--

DROP TABLE IF EXISTS `votes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `votes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `acteur_id` int(11) NOT NULL DEFAULT '0',
  `delib_id` int(11) NOT NULL DEFAULT '0',
  `resultat` int(1) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `deliberation_id` (`delib_id`),
  KEY `acteur_id` (`acteur_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `votes`
--

LOCK TABLES `votes` WRITE;
/*!40000 ALTER TABLE `votes` DISABLE KEYS */;
/*!40000 ALTER TABLE `votes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wkf_circuits`
--

DROP TABLE IF EXISTS `wkf_circuits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wkf_circuits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `actif` tinyint(1) NOT NULL DEFAULT '1',
  `defaut` tinyint(1) NOT NULL DEFAULT '0',
  `created_user_id` int(11) NOT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nom` (`nom`),
  KEY `created_user_id` (`created_user_id`),
  KEY `modified_user_id` (`modified_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wkf_circuits`
--

LOCK TABLES `wkf_circuits` WRITE;
/*!40000 ALTER TABLE `wkf_circuits` DISABLE KEYS */;
/*!40000 ALTER TABLE `wkf_circuits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wkf_compositions`
--

DROP TABLE IF EXISTS `wkf_compositions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wkf_compositions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `etape_id` int(11) NOT NULL,
  `type_validation` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  `trigger_id` int(10) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `etape_id` (`etape_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wkf_compositions`
--

LOCK TABLES `wkf_compositions` WRITE;
/*!40000 ALTER TABLE `wkf_compositions` DISABLE KEYS */;
/*!40000 ALTER TABLE `wkf_compositions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wkf_etapes`
--

DROP TABLE IF EXISTS `wkf_etapes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wkf_etapes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `circuit_id` int(11) NOT NULL,
  `nom` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `type` int(10) NOT NULL,
  `ordre` int(11) NOT NULL,
  `created_user_id` int(11) NOT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `circuit_id` (`circuit_id`),
  KEY `nom` (`nom`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wkf_etapes`
--

LOCK TABLES `wkf_etapes` WRITE;
/*!40000 ALTER TABLE `wkf_etapes` DISABLE KEYS */;
/*!40000 ALTER TABLE `wkf_etapes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wkf_signatures`
--

DROP TABLE IF EXISTS `wkf_signatures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wkf_signatures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_signature` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `signature` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wkf_signatures`
--

LOCK TABLES `wkf_signatures` WRITE;
/*!40000 ALTER TABLE `wkf_signatures` DISABLE KEYS */;
/*!40000 ALTER TABLE `wkf_signatures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wkf_traitements`
--

DROP TABLE IF EXISTS `wkf_traitements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wkf_traitements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `circuit_id` int(11) NOT NULL,
  `target_id` int(11) NOT NULL,
  `numero_traitement` int(11) NOT NULL DEFAULT '1',
  `treated` tinyint(4) NOT NULL DEFAULT '0',
  `created_user_id` int(11) DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `target` (`target_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wkf_traitements`
--

LOCK TABLES `wkf_traitements` WRITE;
/*!40000 ALTER TABLE `wkf_traitements` DISABLE KEYS */;
/*!40000 ALTER TABLE `wkf_traitements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wkf_visas`
--

DROP TABLE IF EXISTS `wkf_visas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wkf_visas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `traitement_id` int(11) NOT NULL,
  `trigger_id` int(11) NOT NULL,
  `signature_id` int(11) DEFAULT NULL,
  `etape_nom` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `etape_type` int(10) NOT NULL,
  `action` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `commentaire` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `numero_traitement` int(11) NOT NULL,
  `type_validation` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wkf_visas`
--

LOCK TABLES `wkf_visas` WRITE;
/*!40000 ALTER TABLE `wkf_visas` DISABLE KEYS */;
/*!40000 ALTER TABLE `wkf_visas` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2011-07-20 17:09:15
