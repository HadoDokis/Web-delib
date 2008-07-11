TRUNCATE TABLE `profils`;
INSERT INTO `profils` (`id`, `parent_id`, `libelle`, `created`, `modified`) VALUES
(3, 0, 'Valideur', '2007-09-03 14:39:20', '2007-09-03 14:39:20'),
(4, 0, 'Redacteur', '2007-09-03 14:39:36', '2007-10-18 11:06:21'),
(1, 0, 'Administrateur', '2007-09-03 14:40:53', '2007-09-03 14:40:53'),
(2, 0, 'Assemblee', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- Structure de la table `compteurs`
CREATE TABLE IF NOT EXISTS `compteurs` (
  `id` int(11) NOT NULL auto_increment COMMENT 'Identifiant interne',
  `nom` varchar(255) NOT NULL COMMENT 'Nom du compteur',
  `commentaire` varchar(255) NOT NULL COMMENT 'Description du compteur',
  `def_compteur` varchar(255) NOT NULL COMMENT 'Expression formatee du compteur',
  `num_sequence` mediumint(11) NOT NULL COMMENT 'Sequence du compteur qui s''incremente de 1 en 1',
  `def_reinit` varchar(255) NOT NULL COMMENT 'Expression formatee du critere de reinitialisation de la sequence',
  `val_reinit` varchar(255) NOT NULL COMMENT 'Derniere valeur calculee de la reinitialisation',
  `created` datetime NOT NULL COMMENT 'Date et heure de creation du compteur',
  `modified` datetime NOT NULL COMMENT 'Date et heure de modification du compteur',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `nom` (`nom`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

INSERT INTO `compteurs` (`id`, `nom`, `commentaire`, `def_compteur`, `num_sequence`, `def_reinit`, `val_reinit`, `created`, `modified`) VALUES
(1, 'Delibérations', 'Numéro des déliberations votées dans l''ordre du jour des séances', 'DELIB_#MM##0000#', 1, '#M#', '10', '2008-01-07 12:04:25', '2008-01-11 10:48:59');


-- Structure de la table `deliberations`
ALTER TABLE `deliberations` ADD `vote_id` INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE `deliberations` CHANGE `seance_id` `seance_id` INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE `deliberations` CHANGE `num_delib` `num_delib` VARCHAR( 15 ) NOT NULL;
ALTER TABLE `deliberations` ADD `montant` INT( 10 ) NULL AFTER `localisation3_id`;


--
-- Structure de la table `models`
--

DROP TABLE IF EXISTS `models`;
CREATE TABLE IF NOT EXISTS `models` (
  `id` int(11) NOT NULL auto_increment,
  `type` varchar(100) NOT NULL,
  `libelle` varchar(100) NOT NULL,
  `texte` longblob NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

--
-- Contenu de la table `models`
--

INSERT INTO `models` (`id`, `type`, `libelle`, `texte`) VALUES
(2, 'Document', 'convocation', 0x3c7020616c69676e3d227269676874223e234c4f474f5f434f4c4c4543544956495445233c6272202f3e0d0a23414452455353455f434f4c4c4543544956495445233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c64697620616c69676e3d226c656674223e3c7374726f6e673e234e4f4d5f454c55233c2f7374726f6e673e3c2f6469763e0d0a3c64697620616c69676e3d226c656674223e23414452455353455f454c55233c2f6469763e0d0a3c64697620616c69676e3d226c656674223e2356494c4c455f454c55233c2f6469763e0d0a3c7020616c69676e3d227269676874223e41202356494c4c455f434f4c4c4543544956495445232c206c652023444154455f44555f4a4f5552233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e3c6272202f3e0d0a3c7374726f6e673e20202020202020202020202020202020202020202020202020202020202020202020202020202020436f6e766f636174696f6e2061752023545950455f5345414e4345233c2f7374726f6e673e3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c64697620616c69676e3d226c656674223e266e6273703b3c2f6469763e0d0a3c64697620616c69676e3d226c656674223e0d0a3c7072653e426f6e6a6f75722c3c2f7072653e0d0a3c2f6469763e0d0a3c703e3c6272202f3e0d0a3c7374726f6e673e4a276169206c27686f6e6e65757220646520766f757320696e76697465722061752023545950455f5345414e434523207175692061757261206c696575206c652023444154455f5345414e4345232064616e7320234c4945555f5345414e4345232e3c6272202f3e0d0a3c2f7374726f6e673e3c6272202f3e0d0a4a6520766f757320707269652064652063726f6972652c204d6164616d652c204d6f6e73696575722c20656e206c276173737572616e6365206465206d6120636f6e736964266561637574653b726174696f6e2064697374696e6775266561637574653b652e3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e3c7374726f6e673e4f72647265206475206a6f7572203a3c2f7374726f6e673e3c2f703e0d0a3c703e234c495354455f50524f4a4554535f534f4d4d4149524553233c2f703e0d0a3c703e5472266561637574653b7320636f726469616c656d656e742e3c2f703e),
(3, 'Document', 'ordre du jour', 0x3c703e234c4f474f5f434f4c4c4543544956495445233c2f703e0d0a3c703e23414452455353455f434f4c4c4543544956495445233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c7020616c69676e3d227269676874223e3c7374726f6e673e234e4f4d5f454c55233c2f7374726f6e673e3c6272202f3e0d0a23414452455353455f454c55233c6272202f3e0d0a2356494c4c455f454c55233c2f703e0d0a3c7020616c69676e3d227269676874223e41202356494c4c455f434f4c4c4543544956495445232c206c652023444154455f44555f4a4f5552233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e3c7374726f6e673e266e6273703b266e6273703b266e6273703b20266e6273703b266e6273703b266e6273703b20266e6273703b266e6273703b266e6273703b20266e6273703b266e6273703b266e6273703b20266e6273703b266e6273703b266e6273703b3c2f7374726f6e673e3c2f703e0d0a3c703e3c7374726f6e673e266e6273703b266e6273703b266e6273703b20266e6273703b266e6273703b20266e6273703b266e6273703b266e6273703b20266e6273703b266e6273703b266e6273703b20266e6273703b266e6273703b266e6273703b20266e6273703b266e6273703b266e6273703b20266e6273703b20266e6273703b266e6273703b20266e6273703b266e6273703b266e6273703b204f72647265206475206a6f75723c2f7374726f6e673e206475203c7374726f6e673e23545950455f5345414e4345233c2f7374726f6e673e206475203c7374726f6e673e23444154455f5345414e4345233c2f7374726f6e673e3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e),
(1, 'Document', 'projet', 0x3c703e234c4f474f5f434f4c4c4543544956495445233c2f703e0d0a3c703e0d0a3c7461626c652077696474683d22383025222063656c6c73706163696e673d2231222063656c6c70616464696e673d22312220626f726465723d22312220616c69676e3d2263656e746572223e0d0a202020203c74626f64793e0d0a20202020202020203c74723e0d0a2020202020202020202020203c7464206267636f6c6f723d2223393963636666223e4944454e54494649414e542044552050524f4a45543c2f74643e0d0a2020202020202020202020203c7464206267636f6c6f723d2223393963636666223e234944454e54494649414e545f50524f4a4554233c2f74643e0d0a20202020202020203c2f74723e0d0a20202020202020203c74723e0d0a2020202020202020202020203c74643e4c6962656c6c653c2f74643e0d0a2020202020202020202020203c74643e234c4942454c4c455f44454c4942233c2f74643e0d0a20202020202020203c2f74723e0d0a20202020202020203c74723e0d0a2020202020202020202020203c7464206267636f6c6f723d2223393963636666223e54697472653c2f74643e0d0a2020202020202020202020203c7464206267636f6c6f723d2223393963636666223e2354495452455f44454c4942233c2f74643e0d0a20202020202020203c2f74723e0d0a20202020202020203c74723e0d0a2020202020202020202020203c74643e5468266567726176653b6d653c2f74643e0d0a2020202020202020202020203c74643e234c4942454c4c455f5448454d45233c2f74643e0d0a20202020202020203c2f74723e0d0a20202020202020203c74723e0d0a2020202020202020202020203c7464206267636f6c6f723d2223393963636666223e536572766963653c2f74643e0d0a2020202020202020202020203c7464206267636f6c6f723d2223393963636666223e234c4942454c4c455f53455256494345233c2f74643e0d0a20202020202020203c2f74723e0d0a20202020202020203c74723e0d0a2020202020202020202020203c74643e526170706f72746575723c2f74643e0d0a2020202020202020202020203c74643e234e4f4d5f524150504f5254455552233c2f74643e0d0a20202020202020203c2f74723e0d0a20202020202020203c74723e0d0a2020202020202020202020203c7464206267636f6c6f723d2223393963636666223e446174652053266561637574653b616e63653c2f74643e0d0a2020202020202020202020203c7464206267636f6c6f723d2223393963636666223e23444154455f5345414e4345233c2f74643e0d0a20202020202020203c2f74723e0d0a202020203c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e3c753e3c7374726f6e673e20203c2f7374726f6e673e3c2f753e3c2f703e0d0a3c703e3c753e3c7374726f6e673e54455854452050524f4a4554203a203c2f7374726f6e673e3c2f753e3c2f703e0d0a3c703e2354455854455f50524f4a4554233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e3c753e3c7374726f6e673e2054455854452053594e5448455345203a3c2f7374726f6e673e3c2f753e3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e2354455854455f53594e5448455345233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e3c753e3c7374726f6e673e202054455854452044454c494245524154494f4e203a203c2f7374726f6e673e3c2f753e3c2f703e0d0a3c703e2354455854455f44454c4942233c2f703e),
(4, 'Document', 'délibération', 0x3c7020616c69676e3d2263656e746572223e234c4f474f5f434f4c4c4543544956495445233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e3c753e234e4f4d5f524150504f52544555522320235052454e4f4d5f524150504f5254455552233c2f753e3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e23444154455f5345414e4345233c2f703e0d0a3c703e2354455854455f44454c4942233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e3c753e3c7374726f6e673e4c6973746520646573205072266561637574653b73656e74733c2f7374726f6e673e3c2f753e203a3c6272202f3e0d0a234c495354455f50524553454e5453233c2f703e0d0a3c703e3c753e3c7374726f6e673e4c697374652064657320416273656e74733c2f7374726f6e673e3c2f753e203a3c6272202f3e0d0a234c495354455f414253454e5453233c2f703e0d0a3c703e3c753e3c7374726f6e673e4c6973746520646573204d616e646174266561637574653b733c2f7374726f6e673e3c2f753e203a203c6272202f3e0d0a234c495354455f4d414e4441544149524553233c2f703e0d0a3c703e3c7374726f6e673e3c753e524553554c54415420564f54414e54203c2f753e3c2f7374726f6e673e3a203c6272202f3e0d0a234c495354455f564f54414e54233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c683220616c69676e3d2263656e746572223e524553554c544154203a203c666f6e7420636f6c6f723d2223666636363030223e3c656d3e2023434f4d4d454e54414952455f44454c4942233c2f656d3e3c2f666f6e743e3c2f68323e),
(5, 'Document', 'P.V. sommaire', 0x3c7020616c69676e3d227269676874223e234c4f474f5f434f4c4c4543544956495445233c2f703e0d0a3c7020616c69676e3d227269676874223e266e6273703b3c2f703e0d0a3c683220616c69676e3d2263656e746572223e3c753e266e6273703b505620736f6d6d616972652064752023444154455f5345414e4345233c2f753e3c2f68323e0d0a3c703e266e6273703b3c2f703e0d0a3c703e3c753e3c7374726f6e673e4c69737465206465732070726f6a657473203a203c2f7374726f6e673e3c2f753e3c2f703e0d0a3c703e234c495354455f50524f4a4554535f534f4d4d4149524553233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e),
(6, 'Document', 'P.V. détaillé', 0x3c7020616c69676e3d227269676874223e234c4f474f5f434f4c4c4543544956495445233c2f703e0d0a3c7020616c69676e3d227269676874223e266e6273703b3c2f703e0d0a3c683220616c69676e3d2263656e746572223e3c753e20505620436f6d706c65742064752023444154455f5345414e4345233c2f753e3c2f68323e0d0a3c703e266e6273703b3c2f703e0d0a3c703e3c753e3c7374726f6e673e203c2f7374726f6e673e3c2f753e3c2f703e0d0a3c703e234c495354455f50524f4a4554535f44455441494c4c4553233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e),
(8, 'Composant documentaire', 'Liste présents', 0x3c6469763e3c7374726f6e673e234e4f4d5f50524553454e542320235052454e4f4d5f50524553454e54233c2f7374726f6e673e3c2f6469763e),
(9, 'Composant documentaire', 'Liste absents', 0x3c6469763e3c7374726f6e673e235052454e4f4d5f414253454e542320234e4f4d5f414253454e54233c2f7374726f6e673e3c2f6469763e),
(10, 'Composant documentaire', 'liste mandatés', 0x3c6469763e3c7374726f6e673e234e4f4d5f4d414e444154452320235052454e4f4d5f4d414e4441544523206d616e646174266561637574653b20706172203a20234e4f4d5f4d414e444154414952452320235052454e4f4d5f4d414e44415441495245233c2f7374726f6e673e3c2f6469763e),
(11, 'Composant documentaire', 'liste votants', 0x3c6469763e235052454e4f4d5f564f54414e542320234e4f4d5f564f54414e5423206120766f74652023524553554c5441545f564f54414e54233c2f6469763e),
(12, 'Composant documentaire', 'liste projets détaillés', 0x3c703e50524553454e5453203a203c6272202f3e0d0a234c495354455f50524553454e5453233c2f703e0d0a3c703e4f4e5420444f4e4e4520504f55564f4952203a203c6272202f3e0d0a234c495354455f4d414e4441544149524553233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e3c7374726f6e673e4f52445245204455204a4f55523c2f7374726f6e673e203a203c6272202f3e0d0a3c6272202f3e0d0a3c753e3c7374726f6e673e4c6962656c6c653c2f7374726f6e673e3c2f753e203a20234c4942454c4c455f44454c4942233c6272202f3e0d0a3c7374726f6e673e5469747265203a203c2f7374726f6e673e2354495452455f44454c4942233c2f703e0d0a3c703e3c7374726f6e673e44656261743c2f7374726f6e673e3c6272202f3e0d0a2344454241545f44454c4942233c2f703e0d0a3c703e3c7374726f6e673e52266561637574653b73756c74617420647520766f74653c2f7374726f6e673e203a3c6272202f3e0d0a23434f4d4d454e54414952455f44454c4942233c2f703e),
(13, 'Composant documentaire', 'liste projets sommaires', 0x3c703e23504f534954494f4e5f44454c49422329203c753e3c7374726f6e673e4c6962656c6c653c2f7374726f6e673e3c2f753e3c7374726f6e673e203a203c2f7374726f6e673e234c4942454c4c455f44454c4942233c6272202f3e0d0a3c7374726f6e673e5469747265203a203c2f7374726f6e673e2354495452455f44454c4942233c2f703e0d0a3c7020616c69676e3d227269676874223e3c7374726f6e673e3c666f6e7420636f6c6f723d2223666636363030223e23434f4d4d454e54414952455f44454c4942233c2f666f6e743e3c2f7374726f6e673e3c2f703e0d0a3c7020616c69676e3d227269676874223e266e6273703b3c2f703e0d0a3c7020616c69676e3d227269676874223e266e6273703b3c2f703e);

-- Structure de la table `services`
ALTER TABLE `services` CHANGE `libelle` `libelle` VARCHAR( 100 );

-- Structure de la table `themes`
ALTER TABLE `themes` CHANGE `libelle` `libelle` VARCHAR( 100 );

-- Structure de la table `typeseances`
ALTER TABLE  `typeseances` CHANGE `libelle` `libelle` VARCHAR( 100 );

TRUNCATE `acos`;
TRUNCATE `aros`;
TRUNCATE `aros_acos`;

-- Contenu de la table `acos`
--
INSERT INTO `acos` (`id`, `object_id`, `alias`, `lft`, `rght`) VALUES
(1, 0, 'Typeseances', 1, 12),
(2, 1, 'Typeseances:index', 10, 11),
(3, 2, 'Typeseances:view', 8, 9),
(4, 3, 'Typeseances:add', 6, 7),
(5, 4, 'Typeseances:edit', 4, 5),
(6, 5, 'Typeseances:delete', 2, 3),
(7, 0, 'Compteurs', 13, 22),
(8, 1, 'Compteurs:suivant', 20, 21),
(9, 2, 'Compteurs:index', 18, 19),
(10, 3, 'Compteurs:view', 16, 17),
(11, 4, 'Compteurs:edit', 14, 15),
(12, 0, 'Commentaires', 23, 32),
(13, 1, 'Commentaires:add', 30, 31),
(14, 2, 'Commentaires:edit', 28, 29),
(15, 3, 'Commentaires:delete', 26, 27),
(16, 4, 'Commentaires:view', 24, 25),
(17, 0, 'Localisations', 33, 48),
(18, 1, 'Localisations:getLibelle', 46, 47),
(19, 2, 'Localisations:index', 44, 45),
(20, 3, 'Localisations:view', 42, 43),
(21, 4, 'Localisations:add', 40, 41),
(22, 5, 'Localisations:edit', 38, 39),
(23, 6, 'Localisations:delete', 36, 37),
(24, 7, 'Localisations:changeParentId', 34, 35),
(25, 0, 'Deliberations', 49, 172),
(26, 1, 'Deliberations:index', 170, 171),
(27, 2, 'Deliberations:listerMesProjets', 168, 169),
(28, 3, 'Deliberations:listerProjetsAttribues', 166, 167),
(29, 4, 'Deliberations:listerProjetsNonAttribues', 164, 165),
(30, 5, 'Deliberations:listerProjetsDansMesCircuits', 162, 163),
(31, 6, 'Deliberations:listerProjetsATraiter', 160, 161),
(32, 7, 'Deliberations:getPosition', 158, 159),
(33, 8, 'Deliberations:view', 156, 157),
(34, 9, 'Deliberations:getFileData', 154, 155),
(35, 10, 'Deliberations:saveLocation', 152, 153),
(36, 11, 'Deliberations:getParent', 150, 151),
(37, 12, 'Deliberations:changeLocation', 148, 149),
(38, 13, 'Deliberations:add', 146, 147),
(39, 14, 'Deliberations:textsynthese', 144, 145),
(40, 15, 'Deliberations:deliberation', 142, 143),
(41, 16, 'Deliberations:textprojet', 140, 141),
(42, 17, 'Deliberations:edit', 138, 139),
(43, 18, 'Deliberations:recapitulatif', 136, 137),
(44, 19, 'Deliberations:getTextSynthese', 134, 135),
(45, 20, 'Deliberations:getTextProjet', 132, 133),
(46, 21, 'Deliberations:getField', 130, 131),
(47, 22, 'Deliberations:delete', 128, 129),
(48, 23, 'Deliberations:convert', 126, 127),
(49, 24, 'Deliberations:addIntoCircuit', 124, 125),
(50, 25, 'Deliberations:attribuercircuit', 122, 123),
(51, 26, 'Deliberations:traiter', 120, 121),
(52, 27, 'Deliberations:chercherVersionAnterieure', 118, 119),
(53, 28, 'Deliberations:transmit', 116, 117),
(54, 29, 'Deliberations:getNatureListe', 114, 115),
(55, 30, 'Deliberations:classification', 112, 113),
(56, 31, 'Deliberations:getMatiereListe', 110, 111),
(57, 32, 'Deliberations:object2array', 108, 109),
(58, 33, 'Deliberations:sendActe', 106, 107),
(59, 34, 'Deliberations:changeEtat', 104, 105),
(60, 35, 'Deliberations:changeClassification', 102, 103),
(61, 36, 'Deliberations:getDateClassification', 100, 101),
(62, 37, 'Deliberations:getClassification', 98, 99),
(63, 38, 'Deliberations:positionner', 96, 97),
(64, 39, 'Deliberations:sortby', 94, 95),
(65, 40, 'Deliberations:getCurrentPosition', 92, 93),
(66, 41, 'Deliberations:getCurrentSeance', 90, 91),
(67, 42, 'Deliberations:getLastPosition', 88, 89),
(68, 43, 'Deliberations:getNextId', 86, 87),
(69, 44, 'Deliberations:listerProjetsServicesAssemblees', 84, 85),
(70, 45, 'Deliberations:convertDoc2Html', 82, 83),
(71, 46, 'Deliberations:updateword', 80, 81),
(72, 47, 'Deliberations:getRapporteur', 78, 79),
(73, 48, 'Deliberations:textprojetvue', 76, 77),
(74, 49, 'Deliberations:textsynthesevue', 74, 75),
(75, 50, 'Deliberations:deliberationvue', 72, 73),
(76, 51, 'Deliberations:notifierDossierAtraiter', 70, 71),
(77, 52, 'Deliberations:notifierDossierRefuse', 68, 69),
(78, 53, 'Deliberations:notifierInsertionCircuit', 66, 67),
(79, 54, 'Deliberations:getListPresent', 64, 65),
(80, 55, 'Deliberations:listerPresents', 62, 63),
(81, 56, 'Deliberations:effacerListePresence', 60, 61),
(82, 57, 'Deliberations:isFirstDelib', 58, 59),
(83, 58, 'Deliberations:buildFirstList', 56, 57),
(84, 59, 'Deliberations:copyFromPreviousList', 54, 55),
(85, 60, 'Deliberations:getDelibIdByPosition', 52, 53),
(86, 61, 'Deliberations:afficherListePresents', 50, 51),
(213, 4, 'Models:delete', 500, 501),
(212, 3, 'Models:edit', 502, 503),
(211, 2, 'Models:add', 504, 505),
(210, 1, 'Models:index', 506, 507),
(209, 0, 'Models', 417, 508),
(93, 0, 'Seances', 185, 242),
(94, 1, 'Seances:index', 240, 241),
(95, 2, 'Seances:view', 238, 239),
(96, 3, 'Seances:add', 236, 237),
(97, 4, 'Seances:edit', 234, 235),
(98, 5, 'Seances:delete', 232, 233),
(99, 6, 'Seances:listerFuturesSeances', 230, 231),
(100, 7, 'Seances:listerAnciennesSeances', 228, 229),
(101, 8, 'Seances:afficherCalendrier', 226, 227),
(102, 9, 'Seances:afficherProjets', 224, 225),
(103, 10, 'Seances:changeRapporteur', 222, 223),
(104, 11, 'Seances:getDate', 220, 221),
(105, 12, 'Seances:getType', 218, 219),
(106, 13, 'Seances:addListUsers', 216, 217),
(107, 14, 'Seances:effacerListe', 214, 215),
(108, 15, 'Seances:generateConvocationList', 212, 213),
(109, 16, 'Seances:sendConvoc', 210, 211),
(110, 17, 'Seances:generateConvocationAnonyme', 208, 209),
(111, 18, 'Seances:generateOrdresDuJour', 206, 207),
(112, 19, 'Seances:checkLists', 204, 205),
(113, 20, 'Seances:delUserFromList', 202, 203),
(114, 21, 'Seances:addUserFromList', 200, 201),
(115, 22, 'Seances:isInList', 198, 199),
(116, 23, 'Seances:details', 196, 197),
(117, 24, 'Seances:effacerVote', 194, 195),
(118, 25, 'Seances:voter', 192, 193),
(119, 26, 'Seances:saisirDebat', 190, 191),
(120, 27, 'Seances:getFileData', 188, 189),
(121, 28, 'Seances:saisirDebatGlobal', 186, 187),
(122, 0, 'Postseances', 243, 276),
(123, 1, 'Postseances:index', 274, 275),
(124, 2, 'Postseances:afficherProjets', 272, 273),
(125, 3, 'Postseances:getVote', 270, 271),
(126, 4, 'Postseances:getPresence', 268, 269),
(127, 5, 'Postseances:generatePvSommaire', 266, 267),
(128, 6, 'Postseances:generatePvComplet', 264, 265),
(129, 7, 'Postseances:generateDeliberation', 262, 263),
(130, 8, 'Postseances:getNom', 260, 261),
(131, 0, 'Postseances', 277, 278),
(132, 1, 'Postseances:index', 258, 259),
(133, 2, 'Postseances:afficherProjets', 256, 257),
(134, 3, 'Postseances:getVote', 254, 255),
(135, 4, 'Postseances:getPresence', 252, 253),
(136, 5, 'Postseances:generatePvSommaire', 250, 251),
(137, 6, 'Postseances:generatePvComplet', 248, 249),
(138, 7, 'Postseances:generateDeliberation', 246, 247),
(139, 8, 'Postseances:getNom', 244, 245),
(140, 0, 'Votes', 279, 290),
(141, 1, 'Votes:index', 288, 289),
(142, 2, 'Votes:view', 286, 287),
(143, 3, 'Votes:add', 284, 285),
(144, 4, 'Votes:edit', 282, 283),
(145, 5, 'Votes:delete', 280, 281),
(262, 7, 'Users:getPrenom', 520, 521),
(261, 6, 'Users:getNom', 522, 523),
(260, 5, 'Users:delete', 524, 525),
(259, 4, 'Users:edit', 526, 527),
(258, 3, 'Users:add', 528, 529),
(257, 2, 'Users:view', 530, 531),
(256, 1, 'Users:index', 532, 533),
(255, 0, 'Users', 509, 534),
(156, 0, 'Services', 311, 332),
(157, 1, 'Services:changeService', 330, 331),
(158, 2, 'Services:getLibelle', 328, 329),
(159, 3, 'Services:index', 326, 327),
(160, 4, 'Services:view', 324, 325),
(161, 5, 'Services:add', 322, 323),
(162, 6, 'Services:edit', 320, 321),
(163, 7, 'Services:delete', 318, 319),
(164, 8, 'Services:changeParentId', 316, 317),
(165, 9, 'Services:doList', 314, 315),
(166, 10, 'Services:getParentList', 312, 313),
(167, 0, 'Collectivites', 333, 342),
(168, 1, 'Collectivites:index', 340, 341),
(169, 2, 'Collectivites:edit', 338, 339),
(170, 3, 'Collectivites:setLogo', 336, 337),
(171, 4, 'Collectivites:synchronize', 334, 335),
(172, 0, 'Profils', 343, 356),
(173, 1, 'Profils:index', 354, 355),
(174, 2, 'Profils:view', 352, 353),
(175, 3, 'Profils:add', 350, 351),
(176, 4, 'Profils:edit', 348, 349),
(177, 5, 'Profils:delete', 346, 347),
(178, 6, 'Profils:changeParentId', 344, 345),
(179, 0, 'Circuits', 357, 380),
(180, 1, 'Circuits:view', 378, 379),
(181, 2, 'Circuits:edit', 376, 377),
(182, 3, 'Circuits:delete', 374, 375),
(183, 4, 'Circuits:add', 372, 373),
(184, 5, 'Circuits:index', 370, 371),
(185, 6, 'Circuits:addUser', 368, 369),
(186, 7, 'Circuits:intervertirPosition', 366, 367),
(187, 8, 'Circuits:supprimerUser', 364, 365),
(188, 9, 'Circuits:getCurrentPosition', 362, 363),
(189, 10, 'Circuits:getCurrentCircuit', 360, 361),
(190, 11, 'Circuits:getLastPosition', 358, 359),
(191, 0, 'Annexes', 381, 394),
(192, 1, 'Annexes:delete', 392, 393),
(193, 2, 'Annexes:download', 390, 391),
(194, 3, 'Annexes:getFileType', 388, 389),
(195, 4, 'Annexes:getFileName', 386, 387),
(196, 5, 'Annexes:getSize', 384, 385),
(197, 6, 'Annexes:getData', 382, 383),
(198, 0, 'Themes', 395, 410),
(199, 1, 'Themes:getLibelle', 408, 409),
(200, 2, 'Themes:index', 406, 407),
(201, 3, 'Themes:view', 404, 405),
(202, 4, 'Themes:add', 402, 403),
(203, 5, 'Themes:edit', 400, 401),
(204, 6, 'Themes:delete', 398, 399),
(205, 7, 'Themes:changeParentId', 396, 397),
(206, 1, 'Pages:home', 411, 412),
(207, 1, 'Pages:administration', 413, 414),
(208, 1, 'Pages:display', 415, 416),
(214, 5, 'Models:view', 498, 499),
(215, 6, 'Models:getUserNom', 496, 497),
(216, 7, 'Models:getUserPrenom', 494, 495),
(217, 8, 'Models:getUserAdresse', 492, 493),
(218, 9, 'Models:getUserCP', 490, 491),
(219, 10, 'Models:getUserVille', 488, 489),
(220, 11, 'Models:getCollectiviteNom', 486, 487),
(221, 12, 'Models:getCollectiviteAdresse', 484, 485),
(222, 13, 'Models:getCollectiviteCP', 482, 483),
(223, 14, 'Models:getCollectiviteVille', 480, 481),
(224, 15, 'Models:getCollectiviteTelephone', 478, 479),
(225, 16, 'Models:getLibelleTheme', 476, 477),
(226, 17, 'Models:getLibelleService', 474, 475),
(227, 18, 'Models:getLibelleTypeSeance', 472, 473),
(228, 19, 'Models:getDateSeance', 470, 471),
(229, 20, 'Models:getTypeIdFromSeanceId', 468, 469),
(230, 21, 'Models:getCommentaireDelib', 466, 467),
(231, 22, 'Models:getDebatDelib', 464, 465),
(232, 23, 'Models:getTexteProjet', 462, 463),
(233, 24, 'Models:getTexteSynthese', 460, 461),
(234, 25, 'Models:getTexteDeliberation', 458, 459),
(235, 26, 'Models:getDelibLibelle', 456, 457),
(236, 27, 'Models:getDelibEtat', 454, 455),
(237, 28, 'Models:getDelibTitre', 452, 453),
(238, 29, 'Models:getSeanceId', 450, 451),
(239, 30, 'Models:getRapporteurId', 448, 449),
(240, 31, 'Models:getThemeId', 446, 447),
(241, 32, 'Models:getServiceId', 444, 445),
(242, 33, 'Models:getRedacteurId', 442, 443),
(243, 34, 'Models:getDateDuJour', 440, 441),
(244, 35, 'Models:listeProjets', 438, 439),
(245, 36, 'Models:listeUsersPresents', 436, 437),
(246, 37, 'Models:listeUsersAbsents', 434, 435),
(247, 38, 'Models:listeUsersMandates', 432, 433),
(248, 39, 'Models:listeUsersVotant', 430, 431),
(249, 40, 'Models:replaceBalisesSeance', 428, 429),
(250, 41, 'Models:replaceBalises', 426, 427),
(251, 42, 'Models:generateDeliberation', 424, 425),
(252, 43, 'Models:generateProjet', 422, 423),
(253, 44, 'Models:generatePVSommaire', 420, 421),
(254, 45, 'Models:generatePVDetaille', 418, 419),
(263, 8, 'Users:getAdresse', 518, 519),
(264, 9, 'Users:getCP', 516, 517),
(265, 10, 'Users:getVille', 514, 515),
(266, 11, 'Users:login', 512, 513),
(267, 12, 'Users:logout', 510, 511);

--
-- Contenu de la table `aros`
--

INSERT INTO `aros` (`id`, `foreign_key`, `alias`, `lft`, `rght`) VALUES
(1, 0, 'Valideur', 1, 2),
(2, 0, 'Redacteur', 3, 4),
(4, 0, 'Administrateur', 5, 8),
(3, 0, 'Assemblee', 9, 10),
(20, 1, 'admin', 6, 7);
--
-- Contenu de la table `aros_acos`
--

INSERT INTO `aros_acos` (`id`, `aro_id`, `aco_id`, `_create`, `_read`, `_update`, `_delete`) VALUES
(1, 4, 1, '1', '1', '1', '1'),
(2, 4, 7, '1', '1', '1', '1'),
(3, 4, 17, '1', '1', '1', '1'),
(4, 4, 87, '1', '1', '1', '1'),
(5, 4, 146, '1', '1', '1', '1'),
(6, 4, 156, '1', '1', '1', '1'),
(7, 4, 167, '1', '1', '1', '1'),
(8, 4, 172, '1', '1', '1', '1'),
(9, 4, 179, '1', '1', '1', '1'),
(10, 4, 198, '1', '1', '1', '1'),
(11, 4, 206, '1', '1', '1', '1'),
(12, 4, 207, '1', '1', '1', '1'),
(13, 4, 208, '1', '1', '1', '1'),
(14, 3, 25, '1', '1', '1', '1'),
(15, 3, 93, '1', '1', '1', '1'),
(16, 3, 122, '1', '1', '1', '1'),
(17, 3, 140, '1', '1', '1', '1'),
(18, 3, 191, '1', '1', '1', '1'),
(19, 3, 206, '1', '1', '1', '1'),
(20, 3, 208, '1', '1', '1', '1'),
(21, 2, 25, '1', '1', '1', '1'),
(22, 2, 191, '1', '1', '1', '1'),
(23, 2, 206, '1', '1', '1', '1'),
(24, 2, 208, '1', '1', '1', '1'),
(25, 1, 25, '1', '1', '1', '1'),
(26, 1, 38, '-1', '-1', '-1', '-1'),
(27, 1, 191, '1', '1', '1', '1'),
(28, 1, 206, '1', '1', '1', '1'),
(29, 1, 208, '1', '1', '1', '1'),
(30, 4, 165, '1', '1', '1', '1'),
(31, 2, 165, '1', '1', '1', '1'),
(32, 1, 199, '1', '1', '1', '1'),
(33, 2, 199, '1', '1', '1', '1'),
(34, 3, 199, '1', '1', '1', '1'),
(35, 1, 158, '1', '1', '1', '1'),
(36, 2, 158, '1', '1', '1', '1'),
(37, 3, 158, '1', '1', '1', '1'),
(38, 1, 152, '1', '1', '1', '1'),
(39, 2, 152, '1', '1', '1', '1'),
(40, 3, 152, '1', '1', '1', '1'),
(41, 1, 153, '1', '1', '1', '1'),
(42, 2, 153, '1', '1', '1', '1'),
(43, 3, 153, '1', '1', '1', '1'),
(44, 1, 104, '1', '1', '1', '1'),
(45, 2, 104, '1', '1', '1', '1'),
(46, 3, 104, '1', '1', '1', '1'),
(47, 1, 105, '1', '1', '1', '1'),
(48, 2, 105, '1', '1', '1', '1'),
(49, 3, 105, '1', '1', '1', '1'),
(50, 1, 155, '1', '1', '1', '1'),
(51, 2, 155, '1', '1', '1', '1'),
(52, 3, 155, '1', '1', '1', '1'),
(55, 3, 165, '1', '1', '1', '1'),
(54, 1, 165, '1', '1', '1', '1'),
(89, 3, 255, '1', '1', '1', '1'),
(88, 4, 255, '1', '1', '1', '1'),
(87, 1, 209, '1', '1', '1', '1'),
(86, 2, 209, '1', '1', '1', '1'),
(85, 3, 209, '1', '1', '1', '1'),
(84, 4, 209, '1', '1', '1', '1'),
(91, 1, 255, '1', '1', '1', '1'),
(90, 2, 255, '1', '1', '1', '1');

INSERT INTO `aros_acos` ( `id` , `aro_id` , `aco_id` , `_create` , `_read` , `_update` , `_delete` )
VALUES (NULL , '3', '7', '1', '1', '1', '1');

-- Ajout des commentaires pour tous ces roles
INSERT INTO `aros_acos` (`id` , `aro_id` , `aco_id` , `_create` , `_read` , `_update` , `_delete` )
VALUES (NULL , '1', '12', '1', '1', '1', '1');
INSERT INTO `aros_acos` (`id` , `aro_id` , `aco_id` , `_create` , `_read` , `_update` , `_delete` )
VALUES (NULL , '2', '12', '1', '1', '1', '1');
INSERT INTO `aros_acos` (`id` , `aro_id` , `aco_id` , `_create` , `_read` , `_update` , `_delete` )
VALUES (NULL , '3', '12', '1', '1', '1', '1');

-- Ajout du changement de services
INSERT INTO `aros_acos` (`id` , `aro_id` , `aco_id` , `_create` , `_read` , `_update` , `_delete` )
VALUES (NULL , '1', '157', '1', '1', '1', '1');
INSERT INTO `aros_acos` (`id` , `aro_id` , `aco_id` , `_create` , `_read` , `_update` , `_delete` )
VALUES (NULL , '2', '157', '1', '1', '1', '1');
INSERT INTO `aros_acos` (`id` , `aro_id` , `aco_id` , `_create` , `_read` , `_update` , `_delete` )
VALUES (NULL , '3', '157', '1', '1', '1', '1');
INSERT INTO `aros_acos` (`id` , `aro_id` , `aco_id` , `_create` , `_read` , `_update` , `_delete` )
VALUES (NULL , '4', '157', '1', '1', '1', '1');