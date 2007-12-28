<?php
/**
* Gestion des compteurs paramétrables.
*
* Utilise la table compteurs qui a la structure suivante :
* CREATE TABLE `compteurs` (
* `id` int(11) NOT NULL auto_increment COMMENT 'Identifiant interne',
* `nom` varchar(255) NOT NULL COMMENT 'Nom du compteur utilisé dans l''application',
* `commentaire` varchar(255) NOT NULL COMMENT 'Description du compteur',
* `defcompteur` varchar(255) NOT NULL COMMENT 'Expression formatée du compteur',
* `numsequence` mediumint(11) NOT NULL COMMENT 'Séquence du compteur qui s''incrémente de 1 en 1',
* `defrupture` varchar(255) NOT NULL COMMENT 'Expression formatée de la rupture qui déclanche une réinitialisation de la séquence',
* `valrupture` varchar(255) NOT NULL COMMENT 'Valeur de la rupture calculée lors de la dernière génération du compteur',
* `created` datetime NOT NULL COMMENT 'Date et heure de création du compteur',
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