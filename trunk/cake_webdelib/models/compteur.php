<?php
/**
* Gestion des compteurs param�trables.
*
* Utilise la table compteurs qui a la structure suivante :
* CREATE TABLE `compteurs` (
* `id` int(11) NOT NULL auto_increment COMMENT 'Identifiant interne',
* `nom` varchar(255) NOT NULL COMMENT 'Nom du compteur utilis� dans l''application',
* `commentaire` varchar(255) NOT NULL COMMENT 'Description du compteur',
* `defcompteur` varchar(255) NOT NULL COMMENT 'Expression format�e du compteur',
* `numsequence` mediumint(11) NOT NULL COMMENT 'S�quence du compteur qui s''incr�mente de 1 en 1',
* `defrupture` varchar(255) NOT NULL COMMENT 'Expression format�e de la rupture qui d�clanche une r�initialisation de la s�quence',
* `valrupture` varchar(255) NOT NULL COMMENT 'Valeur de la rupture calcul�e lors de la derni�re g�n�ration du compteur',
* `created` datetime NOT NULL COMMENT 'Date et heure de cr�ation du compteur',
* `modified` datetime NOT NULL COMMENT 'Date et heure de modification du compteur',
* PRIMARY KEY  (`id`),
* UNIQUE KEY `nom` (`nom`)
* );
*
*
* PHP versions 4 and 5
* @filesource
* @copyright
* @link			http://www.adullact.org
* @package			web-delib
* @subpackage
* @since
* @version			1.0
* @modifiedby
* @lastmodified	$Date: 2007-10-14
* @license
*/

class Compteur extends AppModel
{
	var $name = 'Compteur';

	var $validate = array(
		'nom' => VALID_NOT_EMPTY,
		'defcompteur' => VALID_NOT_EMPTY
	);
}
?>