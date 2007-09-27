-- --------------------------------------------------------

-- 
-- Structure de la table `listepresences`
-- 

CREATE TABLE `listepresences` (
  `id` int(11) NOT NULL auto_increment,
  `seance_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `present` tinyint(1) NOT NULL,
  `mandataire` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;
