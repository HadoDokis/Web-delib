-- 21/12/2007

--
-- Structure de la table `compteurs`
--

CREATE TABLE `compteurs` (
  `id` int(11) NOT NULL auto_increment COMMENT 'Identifiant interne',
  `nom` varchar(255) NOT NULL COMMENT 'Nom du compteur',
  `commentaire` varchar(255) NOT NULL COMMENT 'Description du compteur',
  `def_compteur` varchar(255) NOT NULL COMMENT 'Expression formatee du compteur',
  `num_sequence` mediumint(11) NOT NULL COMMENT 'Sequence du compteur qui s''incremente de 1 en 1',
  `def_reinit` varchar(255) NOT NULL COMMENT 'Expression formatee du critere de reinitialisation de la sequence',
  `val_reinit` varchar(255) NOT NULL COMMENT 'Dernière valeur calculee de la réinitialisation',
  `created` datetime NOT NULL COMMENT 'Date et heure de creation du compteur',
  `modified` datetime NOT NULL COMMENT 'Date et heure de modification du compteur',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `nom` (`nom`)
) DEFAULT CHARSET=latin1 ;

--
-- Contenu de la table `compteurs`
--

INSERT INTO `compteurs` (`id`, `nom`, `commentaire`, `def_compteur`, `num_sequence`, `def_reinit`, `val_reinit`, `created`, `modified`) VALUES
(1, 'Deliberations', 'Numero des deliberations votees dans l''ordre du jour des seances', 'DELIB_#0000#', 0, '', '', now(), now());
