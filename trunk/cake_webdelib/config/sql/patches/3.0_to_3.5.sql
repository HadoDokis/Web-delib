alter table acteurs                 type=MyISAM;
alter table acos                    type=MyISAM;
alter table acteurs_services        type=MyISAM;
alter table ados                    type=MyISAM;
alter table annexes                 type=MyISAM;
alter table aros                    type=MyISAM;
alter table aros_acos               type=MyISAM;
alter table aros_ados               type=MyISAM;           
/* 
    alter table circuits                type=MyISAM;            
    alter table traitements             type=MyISAM;       
    alter table users_circuits          type=MyISAM;       
    alter table localisations           type=MyISAM;           
*/     
alter table collectivites           type=MyISAM;          
alter table commentaires            type=MyISAM;             
alter table compteurs               type=MyISAM;             
alter table deliberations           type=MyISAM;         
alter table historiques             type=MyISAM;            
alter table infosupdefs             type=MyISAM;           
alter table infosuplistedefs        type=MyISAM;        
alter table infosups                type=MyISAM;          
alter table listepresences          type=MyISAM;          
alter table models                  type=MyISAM;             
alter table natures                 type=MyISAM;                
alter table profils                 type=MyISAM;                
alter table seances                 type=MyISAM;                 
alter table sequences               type=MyISAM;               
alter table services                type=MyISAM;                
alter table themes                  type=MyISAM;                 
alter table typeacteurs             type=MyISAM;             
alter table typeseances             type=MyISAM;             
alter table typeseances_acteurs     type=MyISAM;   
alter table typeseances_natures     type=MyISAM;   
alter table typeseances_typeacteurs type=MyISAM;   
alter table users                   type=MyISAM;      
alter table users_services          type=MyISAM;           
alter table votes                   type=MyISAM;  
alter table  wkf_circuits           type=MyISAM; 
alter table  wkf_compositions       type=MyISAM;
alter table  wkf_etapes             type=MyISAM;
alter table  wkf_signatures         type=MyISAM;
alter table  wkf_traitements        type=MyISAM;
alter table  wkf_visas              type=MyISAM;

alter table acteurs                 type=INNODB;
alter table acos                    type=INNODB;
alter table acteurs_services        type=INNODB;
alter table ados                    type=INNODB;
alter table annexes                 type=INNODB;
alter table aros                    type=INNODB;
alter table aros_acos               type=INNODB;
alter table aros_ados               type=INNODB;           
alter table collectivites           type=INNODB;          
alter table commentaires            type=INNODB;             
alter table compteurs               type=INNODB;             
alter table deliberations           type=INNODB;         
alter table historiques             type=INNODB;            
alter table infosupdefs             type=INNODB;           
alter table infosuplistedefs        type=INNODB;        
alter table infosups                type=INNODB;          
alter table listepresences          type=INNODB;          
alter table models                  type=INNODB;             
alter table natures                 type=INNODB;                
alter table profils                 type=INNODB;                
alter table seances                 type=INNODB;                 
alter table sequences               type=INNODB;               
alter table services                type=INNODB;                
alter table themes                  type=INNODB;                 
alter table typeacteurs             type=INNODB;             
alter table typeseances             type=INNODB;             
alter table typeseances_acteurs     type=INNODB;   
alter table typeseances_natures     type=INNODB;   
alter table typeseances_typeacteurs type=INNODB;   
alter table users                   type=INNODB;      
/*
    alter table localisations           type=INNODB;           
    alter table circuits                type=INNODB;                 
    alter table traitements             type=INNODB;              
    alter table users_circuits          type=INNODB;       
*/
alter table users_services          type=INNODB;           
alter table votes                   type=INNODB;  
alter table wkf_circuits            type=INNODB; 
alter table wkf_compositions        type=INNODB;
alter table wkf_etapes              type=INNODB;
alter table wkf_signatures          type=INNODB;
alter table wkf_traitements         type=INNODB;
alter table wkf_visas               type=INNODB;

ALTER TABLE `users` ADD `mail_insertion` tinyint(1) NOT NULL AFTER  accept_notif;
ALTER TABLE `users` ADD `mail_traitement` tinyint(1) NOT NULL AFTER  accept_notif;
ALTER TABLE `users` ADD `mail_refus` tinyint(1) NOT NULL AFTER  accept_notif;
ALTER TABLE `acteurs` ADD  `actif` tinyint(1) NOT NULL DEFAULT 1  AFTER note; 

ALTER TABLE `webdelib`.`users` ADD UNIQUE `login` ( `login` );

CREATE TABLE `tdt_messages` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`delib_id` INT( 11 ) NOT NULL ,
`message_id` INT( 11 ) NOT NULL ,
`type_message` INT( 3 ) NOT NULL ,
`created` DATETIME NOT NULL ,
`modified` DATETIME NOT NULL
) ENGINE = InnoDB;
ALTER TABLE `tdt_messages` ADD `reponse` INT( 3 ) NOT NULL AFTER `type_message` ;

ALTER TABLE `models` ADD `recherche` TINYINT( 1 ) NULL ,
ADD `created` DATETIME NOT NULL ,
ADD `modified` DATETIME NOT NULL ;

ALTER TABLE `deliberations` ADD `signee` TINYINT( 1 ) NULL AFTER `signature`;
ALTER TABLE `acteurs` CHANGE `titre` `titre` VARCHAR( 250 );


ALTER TABLE `seances` ADD `president_id` INT( 10 ) NULL AFTER `secretaire_id`;
ALTER TABLE `seances` ADD `date_convocation` DATETIME NULL AFTER `modified` ;
