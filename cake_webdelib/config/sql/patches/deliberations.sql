-- 14/08/2007 : Changement du format des champs de la BDD dans la table d�lib�rations texte_projet et texte_synth�se afin de pouvoir accueillir de grands textes
ALTER TABLE `deliberations` CHANGE `texte_projet` `texte_projet` LONGBLOB;
ALTER TABLE `deliberations` CHANGE `texte_synthese` `texte_synthese` LONGBLOB;

--14/08/2007 : changement du champs date_sesion en seance_id pour devenir une cl� etrangere de la table seance
ALTER TABLE `deliberations` CHANGE `date_session` `seance_id` INT NULL  