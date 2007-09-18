-- 17/09/2007 : modification de la table users_listepresences en seances_users pour rattachés des utilisateurs a une seance
DROP TABLE users_listepresences;

CREATE TABLE `seances_users` (
  `seance_id` int(9) NOT NULL,
  `user_id` int(11) NOT NULL ,
  PRIMARY KEY  (`seance_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;