ALTER TABLE `users` CHANGE `teldom` `teldom` VARCHAR( 20 ) NULL DEFAULT
NULL ,
CHANGE `telmobile` `telmobile` VARCHAR( 20 ) NULL DEFAULT NULL;

ALTER TABLE `collectivites` CHANGE `telephone` `telephone` VARCHAR( 20 )
NOT NULL;