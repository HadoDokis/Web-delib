ALTER TABLE `acos` CHANGE `alias` `alias` VARCHAR( 255 ) NULL;

ALTER TABLE `acteurs` CHANGE `prenom` `prenom` VARCHAR( 50 ) NULL ,
CHANGE `nom` `nom` VARCHAR( 50 ) NULL ,
CHANGE `salutation` `salutation` VARCHAR( 50 ) NULL ,
CHANGE `titre` `titre` VARCHAR( 100 )  NULL ,
CHANGE `position` `position` INT( 11 ) NULL ,
CHANGE `date_naissance` `date_naissance` DATE NULL ,
CHANGE `adresse1` `adresse1` VARCHAR( 100 )  NULL ,
CHANGE `adresse2` `adresse2` VARCHAR( 100 ) NULL ,
CHANGE `cp` `cp` VARCHAR( 20 ) NULL ,
CHANGE `ville` `ville` VARCHAR( 100 ) NULL ,
CHANGE `email` `email` VARCHAR( 100 )  NULL ;

ALTER TABLE `annexes` CHANGE `deliberation_id` `deliberation_id` INT( 11 ) NULL;
ALTER TABLE `annexes` CHANGE `titre` `titre` VARCHAR( 50 ) NULL;
ALTER TABLE `annexes` CHANGE `type` `type` VARCHAR( 1 )  NULL;
ALTER TABLE `annexes` CHANGE `filename` `filename`  VARCHAR( 75 )NULL;
ALTER TABLE `annexes` CHANGE `filetype` `filetype`  VARCHAR( 255 ) NULL;
ALTER TABLE `annexes` CHANGE `size` `size`  INT( 11 ) NULL;
ALTER TABLE `annexes` CHANGE `data` `data` MEDIUMBLOB NULL;

ALTER TABLE `aros` CHANGE `alias` `alias` VARCHAR( 255 ) NULL;

ALTER TABLE `circuits` CHANGE `libelle` `libelle` VARCHAR( 100 ) NULL;

ALTER TABLE `collectivites` CHANGE `nom` `nom` VARCHAR( 30 )   NULL;
ALTER TABLE `collectivites` CHANGE `adresse` `adresse` VARCHAR( 255 )  NULL;
ALTER TABLE `collectivites` CHANGE `CP` `CP` INT( 11 ) NULL;
ALTER TABLE `collectivites` CHANGE `ville` `ville` VARCHAR( 255 )  NULL;
ALTER TABLE `collectivites` CHANGE `telephone` `telephone` VARCHAR( 20 )  NULL;

ALTER TABLE `commentaires` CHANGE `texte` `texte` VARCHAR( 200 ) NULL;

ALTER TABLE `compteurs` CHANGE `nom` `nom` VARCHAR( 255 )   NULL ,
CHANGE `commentaire` `commentaire` VARCHAR( 255 )   NULL ,
CHANGE `def_compteur` `def_compteur` VARCHAR( 255 )   NULL ,
CHANGE `sequence_id` `sequence_id` INT( 11 ) NULL ,
CHANGE `def_reinit` `def_reinit` VARCHAR( 255 )    NULL ,
CHANGE `val_reinit` `val_reinit` VARCHAR( 255 )  NULL ,
CHANGE `created` `created` DATETIME NULL ,
CHANGE `modified` `modified` DATETIME NULL ;

ALTER TABLE `deliberations` CHANGE `position` `position` INT(4) NULL, 
CHANGE `anterieure_id` `anterieure_id` INT(11) NULL,
CHANGE `objet` `objet` VARCHAR(1000) NULL,
CHANGE `titre` `titre` VARCHAR(1000)  NULL,
CHANGE `num_delib` `num_delib` VARCHAR(15)NULL,
CHANGE `num_pref` `num_pref` VARCHAR(10)  NULL, 
CHANGE `texte_projet_name` `texte_projet_name` VARCHAR(75) NULL, 
CHANGE `texte_projet_type` `texte_projet_type` VARCHAR(255) NULL, 
CHANGE `texte_projet_size` `texte_projet_size` INT(11) NULL,
CHANGE `texte_synthese_name` `texte_synthese_name` VARCHAR(75) NULL, 
CHANGE `texte_synthese_type` `texte_synthese_type` VARCHAR(255)  NULL, 
CHANGE `texte_synthese_size` `texte_synthese_size` INT(11) NULL, 
CHANGE `deliberation_name` `deliberation_name` VARCHAR(75) NULL  , 
CHANGE `deliberation_type` `deliberation_type` VARCHAR(255)  NULL, 
CHANGE `deliberation_size` `deliberation_size` INT(11) NULL,
CHANGE `montant` `montant` INT(11) NULL,
CHANGE `debat` `debat` LONGBLOB NULL,
CHANGE `debat_name` `debat_name` VARCHAR(75) NULL, 
CHANGE `debat_type` `debat_type` VARCHAR(255) NULL, 
CHANGE `debat_size` `debat_size` INT(11) NULL ,
CHANGE `commission` `commission` LONGBLOB NULL,
CHANGE `commission_name` `commission_name` VARCHAR(75) NULL, 
CHANGE `commission_type` `commission_type` VARCHAR(255) NULL, 
CHANGE `commission_size` `commission_size` INT(11) NULL,
CHANGE `avis` `avis` INT(1) NULL,
CHANGE `vote_nb_oui` `vote_nb_oui` INT(3) NULL,
CHANGE `vote_nb_non` `vote_nb_non` INT(3) NULL,
CHANGE `vote_nb_abstention` `vote_nb_abstention` INT(3) NULL,
CHANGE `vote_nb_retrait` `vote_nb_retrait` INT(3) NULL,
CHANGE `vote_commentaire` `vote_commentaire` VARCHAR(500) NULL;

ALTER TABLE `infosupdefs` CHANGE `nom` `nom` VARCHAR( 255 )  NULL ,
CHANGE `commentaire` `commentaire` VARCHAR( 255 ) NULL ,
CHANGE `ordre` `ordre` INT( 11 ) NULL ,
CHANGE `code` `code` VARCHAR( 255 ) NULL ,
CHANGE `taille` `taille` INT( 11 ) NULL ,
CHANGE `type` `type` VARCHAR( 255 )  NULL ,
CHANGE `created` `created` DATETIME NULL ,
CHANGE `modified` `modified` DATETIME NULL;

ALTER TABLE `infosups` CHANGE `deliberation_id` `deliberation_id` INT( 11 ) NULL ,
CHANGE `infosupdef_id` `infosupdef_id` INT( 11 ) NULL ,
CHANGE `text` `text` VARCHAR( 255 ) NULL ,
CHANGE `date` `date` DATE NULL ,
CHANGE `file_name` `file_name` VARCHAR( 255 )  NULL ,
CHANGE `file_size` `file_size` INT( 11 ) NULL ,
CHANGE `file_type` `file_type` VARCHAR( 255 ) NULL ,
CHANGE `content` `content` LONGBLOB NULL ;

ALTER TABLE `listepresences` CHANGE `delib_id` `delib_id` INT( 11 ) NULL ,
CHANGE `acteur_id` `acteur_id` INT( 11 ) NULL ,
CHANGE `present` `present` TINYINT( 1 ) NULL ;

ALTER TABLE `models` CHANGE `modele` `modele` VARCHAR( 100 ) NULL ,
CHANGE `type` `type` VARCHAR( 100 )  NULL ,
CHANGE `size` `size` INT( 11 ) NULL ,
CHANGE `content` `content` LONGBLOB NULL;

ALTER TABLE `profils` CHANGE `libelle` `libelle` VARCHAR( 100 ) NULL;

ALTER TABLE `seances` CHANGE `debat_global` `debat_global` LONGBLOB NULL ,
CHANGE `debat_global_name` `debat_global_name` VARCHAR( 75 ) NULL ,
CHANGE `debat_global_size` `debat_global_size` INT( 11 ) NULL ,
CHANGE `debat_global_type` `debat_global_type` VARCHAR( 255 )  NULL;

ALTER TABLE `sequences` CHANGE `nom` `nom` VARCHAR( 255 ) NULL ,
CHANGE `commentaire` `commentaire` VARCHAR( 255 ) NULL ,
CHANGE `num_sequence` `num_sequence` INT( 11 ) NULL ,
CHANGE `created` `created` DATETIME NULL ,
CHANGE `modified` `modified` DATETIME NULL ;

ALTER TABLE `services` CHANGE `order` `order` VARCHAR( 50 ) NULL ,
CHANGE `libelle` `libelle` VARCHAR( 100 )  NULL ,
CHANGE `circuit_defaut_id` `circuit_defaut_id` INT( 11 ) NULL;

ALTER TABLE `themes` CHANGE `order` `order` VARCHAR( 50 )  NULL ,
CHANGE `libelle` `libelle` VARCHAR( 100 ) NULL;

ALTER TABLE `typeacteurs` CHANGE `nom` `nom` VARCHAR( 255 ) NULL ,
CHANGE `commentaire` `commentaire` VARCHAR( 255 ) NULL ,
CHANGE `elu` `elu` TINYINT( 1 ) NULL ,
CHANGE `created` `created` DATETIME NULL ,
CHANGE `modified` `modified` DATETIME NULL;

ALTER TABLE `typeseances` CHANGE `libelle` `libelle` VARCHAR( 100 ) NULL ,
CHANGE `action` `action` TINYINT( 1 ) NULL ,
CHANGE `compteur_id` `compteur_id` INT( 11 ) NULL ,
CHANGE `modelprojet_id` `modelprojet_id` INT( 11 ) NULL ,
CHANGE `modeldeliberation_id` `modeldeliberation_id` INT( 11 ) NULL ,
CHANGE `modelconvocation_id` `modelconvocation_id` INT( 11 ) NULL ,
CHANGE `modelordredujour_id` `modelordredujour_id` INT( 11 ) NULL ,
CHANGE `modelpvsommaire_id` `modelpvsommaire_id` INT( 11 ) NULL ,
CHANGE `modelpvdetaille_id` `modelpvdetaille_id` INT( 11 ) NULL ;

ALTER TABLE `typeseances_acteurs` CHANGE `typeseance_id` `typeseance_id` INT( 11 ) NULL ,
CHANGE `acteur_id` `acteur_id` INT( 11 ) NULL ;

ALTER TABLE `typeseances_typeacteurs` CHANGE `typeseance_id` `typeseance_id` INT( 11 ) NULL ,
CHANGE `typeacteur_id` `typeacteur_id` INT( 11 ) NULL;

ALTER TABLE `users` CHANGE `login` `login` VARCHAR( 50 ) NULL ,
CHANGE `note` `note` VARCHAR( 25 )  NULL ,
CHANGE `circuit_defaut_id` `circuit_defaut_id` INT( 11 ) NULL ,
CHANGE `password` `password` VARCHAR( 100 ) NULL ,
CHANGE `nom` `nom` VARCHAR( 50 )  NULL ,
CHANGE `prenom` `prenom` VARCHAR( 50 ) NULL ,
CHANGE `email` `email` VARCHAR( 255 ) NULL;

ALTER TABLE `votes` CHANGE `resultat` `resultat` INT( 1 ) NULL;

ALTER TABLE `deliberations` CHANGE `rapporteur_id` `rapporteur_id` INT( 11 ) NULL DEFAULT '0';

