-- 21/12/2007

--
-- Structure de la table `compteurs`
--

CREATE TABLE IF NOT EXISTS `compteurs` (
  `id` int(11) NOT NULL auto_increment COMMENT 'Identifiant interne',
  `nom` varchar(255) NOT NULL COMMENT 'Nom du compteur utilise dans l''application',
  `commentaire` varchar(255) NOT NULL COMMENT 'Description du compteur',
  `defcompteur` varchar(255) NOT NULL COMMENT 'Expression formatee du compteur',
  `numsequence` mediumint(11) NOT NULL COMMENT 'Sequence du compteur qui s''incremente de 1 en 1',
  `defrupture` varchar(255) NOT NULL COMMENT 'Expression formatee de la rupture qui declanche une reinitialisation de la sequence',
  `valrupture` varchar(255) NOT NULL COMMENT 'Valeur de la rupture calculee lors de la derniere generation du compteur',
  `created` datetime NOT NULL COMMENT 'Date et heure de creation du compteur',
  `modified` datetime NOT NULL COMMENT 'Date et heure de modification du compteur',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `nom` (`nom`)
) DEFAULT CHARSET=latin1 ;


--
-- Contenu de la table `compteurs`
--

INSERT INTO `compteurs` (`id`, `nom`, `commentaire`, `defcompteur`, `numsequence`, `defrupture`, `valrupture`, `created`, `modified`) VALUES
(1, 'Deliberations', 'Num des deliberations dans l''ordre du jour des seances', 'DELIB_#s#', 0, '', '', now(), now());
