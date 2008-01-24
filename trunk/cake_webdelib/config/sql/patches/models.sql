-- phpMyAdmin SQL Dump
-- version 2.9.1.1-Debian-2ubuntu1.1
-- http://www.phpmyadmin.net
-- 
-- Serveur: localhost
-- G�n�r� le : Jeudi 24 Janvier 2008 � 11:52
-- Version du serveur: 5.0.38
-- Version de PHP: 5.2.1
-- 
-- Base de donn�es: `webdelib`
-- 

-- --------------------------------------------------------

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
(2, 'Document', 'convocation', 0x3c7020616c69676e3d227269676874223e234c4f474f5f434f4c4c4543544956495445233c2f703e0d0a3c64697620616c69676e3d227269676874223e266e6273703b3c2f6469763e0d0a3c7020616c69676e3d227269676874223e23414452455353455f434f4c4c4543544956495445233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c64697620616c69676e3d226c656674223e3c7374726f6e673e234e4f4d5f454c55233c2f7374726f6e673e3c2f6469763e0d0a3c64697620616c69676e3d226c656674223e23414452455353455f454c55233c2f6469763e0d0a3c7020616c69676e3d226c656674223e2356494c4c455f454c55233c2f703e0d0a3c7020616c69676e3d227269676874223e41202356494c4c455f434f4c4c4543544956495445232c206c652023444154455f44555f4a4f5552233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e3c6272202f3e0d0a3c7374726f6e673e20202020202020202020202020202020202020202020202020202020202020202020202020202020436f6e766f636174696f6e2061752023545950455f5345414e4345233c2f7374726f6e673e3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c64697620616c69676e3d2263656e746572223e0d0a3c64697620616c69676e3d226c656674223e266e6273703b3c2f6469763e0d0a3c64697620616c69676e3d226c656674223e0d0a3c7072653e426f6e6a6f75722c3c2f7072653e0d0a3c2f6469763e0d0a3c2f6469763e0d0a3c703e3c6272202f3e0d0a3c7374726f6e673e4a276169206c27686f6e6e65757220646520766f757320696e76697465722061752023545950455f5345414e434523207175692061757261206c696575206c652023444154455f5345414e4345232064616e733c6272202f3e0d0a234c4945555f5345414e4345232e3c6272202f3e0d0a3c2f7374726f6e673e3c6272202f3e0d0a4a6520766f757320707269652064652063726f6972652c204d6164616d652c204d6f6e73696575722c20656e206c276173737572616e6365206465206d6120636f6e736964266561637574653b726174696f6e2064697374696e6775266561637574653b652e3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e3c7374726f6e673e4f72647265206475206a6f7572203a3c2f7374726f6e673e3c2f703e0d0a3c703e234c495354455f50524f4a4554535f534f4d4d4149524553233c2f703e0d0a3c703e5472266561637574653b7320636f726469616c656d656e742e3c2f703e),
(3, 'Document', 'ordre du jour', 0x3c703e234c4f474f5f434f4c4c4543544956495445233c2f703e0d0a3c703e23414452455353455f434f4c4c4543544956495445233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c7020616c69676e3d227269676874223e3c7374726f6e673e234e4f4d5f454c55233c2f7374726f6e673e3c6272202f3e0d0a23414452455353455f454c55233c6272202f3e0d0a2356494c4c455f454c55233c2f703e0d0a3c7020616c69676e3d227269676874223e41202356494c4c455f434f4c4c4543544956495445232c206c652023444154455f44555f4a4f5552233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e3c7374726f6e673e266e6273703b266e6273703b266e6273703b20266e6273703b266e6273703b266e6273703b20266e6273703b266e6273703b266e6273703b20266e6273703b266e6273703b266e6273703b20266e6273703b266e6273703b266e6273703b3c2f7374726f6e673e3c2f703e0d0a3c703e3c7374726f6e673e266e6273703b266e6273703b266e6273703b20266e6273703b266e6273703b20266e6273703b266e6273703b266e6273703b20266e6273703b266e6273703b266e6273703b20266e6273703b266e6273703b266e6273703b20266e6273703b266e6273703b266e6273703b20266e6273703b20266e6273703b266e6273703b20266e6273703b266e6273703b266e6273703b204f72647265206475206a6f75723c2f7374726f6e673e206475203c7374726f6e673e23545950455f5345414e4345233c2f7374726f6e673e206475203c7374726f6e673e23444154455f5345414e4345233c2f7374726f6e673e3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e),
(1, 'Document', 'projet', 0x3c703e234c4f474f5f434f4c4c4543544956495445233c2f703e0d0a3c703e0d0a3c7461626c652077696474683d22383025222063656c6c73706163696e673d2231222063656c6c70616464696e673d22312220626f726465723d22312220616c69676e3d2263656e746572223e0d0a202020203c74626f64793e0d0a20202020202020203c74723e0d0a2020202020202020202020203c7464206267636f6c6f723d2223393963636666223e4944454e54494649414e542044552050524f4a45543c2f74643e0d0a2020202020202020202020203c7464206267636f6c6f723d2223393963636666223e234944454e54494649414e545f50524f4a4554233c2f74643e0d0a20202020202020203c2f74723e0d0a20202020202020203c74723e0d0a2020202020202020202020203c74643e4c6962656c6c653c2f74643e0d0a2020202020202020202020203c74643e234c4942454c4c455f44454c4942233c2f74643e0d0a20202020202020203c2f74723e0d0a20202020202020203c74723e0d0a2020202020202020202020203c7464206267636f6c6f723d2223393963636666223e54697472653c2f74643e0d0a2020202020202020202020203c7464206267636f6c6f723d2223393963636666223e2354495452455f44454c4942233c2f74643e0d0a20202020202020203c2f74723e0d0a20202020202020203c74723e0d0a2020202020202020202020203c74643e5468266567726176653b6d653c2f74643e0d0a2020202020202020202020203c74643e234c4942454c4c455f5448454d45233c2f74643e0d0a20202020202020203c2f74723e0d0a20202020202020203c74723e0d0a2020202020202020202020203c7464206267636f6c6f723d2223393963636666223e536572766963653c2f74643e0d0a2020202020202020202020203c7464206267636f6c6f723d2223393963636666223e234c4942454c4c455f53455256494345233c2f74643e0d0a20202020202020203c2f74723e0d0a20202020202020203c74723e0d0a2020202020202020202020203c74643e526170706f72746575723c2f74643e0d0a2020202020202020202020203c74643e234e4f4d5f524150504f5254455552233c2f74643e0d0a20202020202020203c2f74723e0d0a20202020202020203c74723e0d0a2020202020202020202020203c7464206267636f6c6f723d2223393963636666223e446174652053266561637574653b616e63653c2f74643e0d0a2020202020202020202020203c7464206267636f6c6f723d2223393963636666223e23444154455f5345414e4345233c2f74643e0d0a20202020202020203c2f74723e0d0a202020203c2f74626f64793e0d0a3c2f7461626c653e0d0a3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e3c753e3c7374726f6e673e20203c2f7374726f6e673e3c2f753e3c2f703e0d0a3c703e3c753e3c7374726f6e673e54455854452050524f4a4554203a203c2f7374726f6e673e3c2f753e3c2f703e0d0a3c703e2354455854455f50524f4a4554233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e3c753e3c7374726f6e673e2054455854452053594e5448455345203a3c2f7374726f6e673e3c2f753e3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e2354455854455f53594e5448455345233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e3c753e3c7374726f6e673e202054455854452044454c494245524154494f4e203a203c2f7374726f6e673e3c2f753e3c2f703e0d0a3c703e2354455854455f44454c4942233c2f703e),
(4, 'Document', 'délibération', 0x3c7020616c69676e3d2263656e746572223e234c4f474f5f434f4c4c4543544956495445233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e3c753e234e4f4d5f524150504f52544555522320235052454e4f4d5f524150504f5254455552233c2f753e3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e23444154455f5345414e4345233c2f703e0d0a3c703e2354455854455f44454c4942233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e3c753e3c7374726f6e673e4c6973746520646573205072266561637574653b73656e74733c2f7374726f6e673e3c2f753e203a3c2f703e0d0a3c703e234c495354455f50524553454e5453233c6272202f3e0d0a3c753e3c7374726f6e673e4c697374652064657320414253454e54533c2f7374726f6e673e3c2f753e203a3c2f703e0d0a3c703e234c495354455f414253454e5453233c2f703e0d0a3c703e3c753e3c7374726f6e673e4c6973746520646573204d616e646174266561637574653b733c2f7374726f6e673e3c2f753e203a203c6272202f3e0d0a234c495354455f4d414e4441544149524553233c2f703e0d0a3c703e3c753e3c7374726f6e673e524553554c54415420564f54414e54203a203c2f7374726f6e673e3c2f753e3c2f703e0d0a3c703e234c495354455f564f54414e54233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c683220616c69676e3d2263656e746572223e524553554c544154203a203c666f6e7420636f6c6f723d2223666636363030223e3c656d3e2023434f4d4d454e54414952455f44454c4942233c2f656d3e3c2f666f6e743e3c2f68323e),
(5, 'Document', 'P.V. sommaire', 0x3c7020616c69676e3d227269676874223e234c4f474f5f434f4c4c4543544956495445233c2f703e0d0a3c7020616c69676e3d227269676874223e266e6273703b3c2f703e0d0a3c683220616c69676e3d2263656e746572223e3c753e266e6273703b505620736f6d6d616972652064752023444154455f5345414e4345233c2f753e3c2f68323e0d0a3c703e266e6273703b3c2f703e0d0a3c703e3c753e3c7374726f6e673e4c69737465206465732070726f6a657473203a203c2f7374726f6e673e3c2f753e3c2f703e0d0a3c703e234c495354455f50524f4a4554535f534f4d4d4149524553233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e),
(6, 'Document', 'P.V. détaillé', 0x3c7020616c69676e3d227269676874223e234c4f474f5f434f4c4c4543544956495445233c2f703e0d0a3c7020616c69676e3d227269676874223e266e6273703b3c2f703e0d0a3c683220616c69676e3d2263656e746572223e3c753e266e6273703b505620436f6d706c65742064752023444154455f5345414e4345233c2f753e3c2f68323e0d0a3c703e266e6273703b3c2f703e0d0a3c703e3c753e3c7374726f6e673e4c69737465206465732070726f6a657473203a203c2f7374726f6e673e3c2f753e3c2f703e0d0a3c703e234c495354455f50524f4a4554535f44455441494c4c4553233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e),
(8, 'Composant documentaire', 'Liste présents', 0x3c703e3c7374726f6e673e234e4f4d5f50524553454e542320235052454e4f4d5f50524553454e54233c2f7374726f6e673e3c656d3e203c6272202f3e0d0a3c2f656d3e3c2f703e),
(9, 'Composant documentaire', 'Liste absents', 0x3c703e3c7374726f6e673e235052454e4f4d5f414253454e54233c2f7374726f6e673e3c7374726f6e673e203c2f7374726f6e673e3c7374726f6e673e234e4f4d5f414253454e54233c2f7374726f6e673e3c656d3e203c2f656d3e3c2f703e),
(10, 'Composant documentaire', 'liste mandatés', 0x3c703e3c7374726f6e673e234e4f4d5f44555f4d414e4441544523203c2f7374726f6e673e3c7374726f6e673e235052454e4f4d5f44555f4d414e4441544523203c2f7374726f6e673e3c7374726f6e673e6d616e646174266561637574653b20706172203a20234e4f4d5f4d414e444154414952452320235052454e4f4d5f4d414e44415441495245232c3c6272202f3e0d0a266e6273703b266e6273703b266e6273703b202823414452455353455f4d414e44415441495245232c3c6272202f3e0d0a266e6273703b266e6273703b266e6273703b202343505f4d414e44415441495245232c202356494c4c455f4d414e4441544149524523293c2f7374726f6e673e3c2f703e),
(11, 'Composant documentaire', 'liste votants', 0x3c703e235052454e4f4d5f564f54414e5423266e6273703b20234e4f4d5f564f54414e5423266e6273703b206120766f74652023524553554c5441545f564f54414e54233c2f703e),
(12, 'Composant documentaire', 'liste projets détaillés', 0x3c703e544f544f203a20234e4f4d5f524544414354455552233c2f703e),
(13, 'Composant documentaire', 'liste projets sommaires', 0x3c703e3c753e3c7374726f6e673e4c6962656c6c653c2f7374726f6e673e3c2f753e3c7374726f6e673e203a203c2f7374726f6e673e234c4942454c4c455f44454c4942233c2f703e0d0a3c703e3c7374726f6e673e5469747265203a203c2f7374726f6e673e2354495452455f44454c4942233c2f703e0d0a3c703e266e6273703b3c2f703e0d0a3c703e266e6273703b3c2f703e);
