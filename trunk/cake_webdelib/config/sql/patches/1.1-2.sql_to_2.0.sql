--
-- Structure de la table `infosupdefs`
--

CREATE TABLE `infosupdefs` (
  `id` int(11) NOT NULL auto_increment,
  `nom` varchar(255) NOT NULL,
  `commentaire` varchar(255) NOT NULL,
  `ordre` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `taille` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY  (`id`)
);


-- --------------------------------------------------------

--
-- Structure de la table `infosups`
--

CREATE TABLE `infosups` (
  `id` int(11) NOT NULL auto_increment,
  `deliberation_id` int(11) NOT NULL,
  `infosupdef_id` int(11) NOT NULL,
  `text` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_size` int(11) NOT NULL,
  `file_type` varchar(255) NOT NULL,
  `content` longblob NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `deliberation_id` (`deliberation_id`),
  KEY `infosupdef_id` (`infosupdef_id`)
);
