<?php

/**
 * Gestion des séquences utilisées par les compteurs paramétrables
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
class Acteur extends AppModel {

    var $name = 'Acteur';
    var $displayField = "nom";
    var $validate = array(
        'nom' => array(
            array(
                'rule' => 'notEmpty',
                'message' => 'Entrer un nom pour l\'acteur'
            )
        ),
        'prenom' => array(
            array(
                'rule' => 'notEmpty',
                'message' => 'Entrer un prénom pour l\'acteur'
            )
        ),
        'email' => array(
            array(
                'rule' => 'email',
                'allowEmpty' => true,
                'message' => 'Adresse email non valide.'
            )
        ),
        'service' => array(
            array(
                'rule' => 'notEmpty',
                'message' => 'Sélectionnez un ou plusieurs services'
            )
        ),
        'typeacteur_id' => array(
            array(
                'rule' => 'notEmpty',
                'message' => 'Selectionner un type d\'acteur'
            )
        )
    );
    var $belongsTo = array('Suppleant' => array('className' => 'Acteur', 'foreignKey' => 'suppleant_id'),
        'Typeacteur' => array('className' => 'Typeacteur', 'foreignKey' => 'typeacteur_id'));

         public $hasMany = array(
			'Acteurseance' => array(
				'className' => 'Acteurseance',
				'foreignKey' => 'acteur_id'
			),
			'Listepresence' => array(
				'className' => 'Listepresence',
				'foreignKey' => 'acteur_id'
			),
			'Vote' => array(
				'className' => 'Vote',
				'foreignKey' => 'acteur_id'
			),
		);

    var $hasAndBelongsToMany = array(
        'Service' => array(
            'classname' => 'Service',
            'joinTable' => 'acteurs_services',
            'foreignKey' => 'acteur_id',
            'associationForeignKey' => 'service_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'unique' => true,
            'finderQuery' => '',
            'deleteQuery' => '')
    );

    /* retourne la liste des acteurs élus [id]=>[prenom et nom] pour utilisation html->selectTag */

    function generateListElus($order_by = null) {
        $generateListElus = array();
        if ($order_by == null)
            $acteurs = $this->find('all', array('conditions' => array('Typeacteur.elu' => 1, 'Acteur.actif' => 1),
                'fields' => array('id', 'nom', 'prenom'),
                'order' => 'Acteur.position ASC'));
        else
            $acteurs = $this->find('all', array('conditions' => array('Typeacteur.elu' => 1, 'Acteur.actif' => 1),
                'fields' => array('id', 'nom', 'prenom'),
                'order' => "$order_by ASC"));
        foreach ($acteurs as $acteur) {
            $generateListElus[$acteur['Acteur']['id']] = $acteur['Acteur']['prenom'] . ' ' . $acteur['Acteur']['nom'];
        }
        return $generateListElus;
    }

    /* retourne la liste des acteurs [id]=>[prenom et nom] pour utilisation html->selectTag */

    function generateList($order_by = null) {
        $generateList = array();
        if ($order_by == null)
            $acteurs = $this->find('all', array('conditions' => array('Acteur.actif' => 1),
                'fields' => array('id', 'nom', 'prenom'),
                'order' => 'Acteur.position ASC'));
        else
            $acteurs = $this->find('all', array('conditions' => array('Acteur.actif' => 1),
                'fields' => array('id', 'nom', 'prenom'),
                'order' => "$order_by ASC"));


        foreach ($acteurs as $acteur) {
            $generateList[$acteur['Acteur']['id']] = $acteur['Acteur']['prenom'] . ' ' . $acteur['Acteur']['nom'];
        }

        return $generateList;
    }

    /* retourne l'id du premier acteur élu associé à la délégation $serviceId */
    /* retourne null si non trouvé                                            */

    function selectActeurEluIdParDelegationId($delegationId) {
        $users = $this->find('all', array('conditions' => array('Typeacteur.elu' => 1, 'Acteur.actif' => 1),
            'fields' => array('id'),
            'order' => 'Acteur.position ASC'));

        foreach ($users as $user) {
            foreach ($user['Service'] as $service) {
                if ($service['id'] == $delegationId)
                    return $user['Acteur']['id'];
            }
        }
        return null;
    }

    /* retourne le numéro de position max pour tous les acteurs élus */
    /* pour rester compatible avec le plus grand nombre de bd, on ne passe pas de requête */
    /* mais on fait le calcul en php */

    function getPostionMaxParActeursElus() {
        $acteur = $this->find('all', array('conditions' => array('Typeacteur.elu' => 1, 'Acteur.actif' => 1),
            'fields' => array('Acteur.position'),
            'order' => 'Acteur.position DESC'));
        return empty($acteur) ? 0 : $acteur[0]['Acteur']['position'];
    }

    /* retourne le libellé correspondant au champ position : = 999 : en dernier, <999 : position */

    function libelleOrdre($ordre = null, $majuscule = false) {
        return ($ordre == 999) ? ($majuscule ? 'En dernier' : 'en dernier') : $ordre;
    }

    /**
     * Données Gedooo :
     * - salutation_$alias/acteur.salutation/text
     * - prenom_$alias/acteur.prenom/text
     * - nom_$alias/acteur.nom/text
     * - titre_$alias/acteur.titre/text
     * - position_$alias/acteur.position/text
     * - email_$alias/acteur.email/text
     * - telmobile_$alias/acteur.telmobile/text
     * - telfixe_$alias/acteur.telfixe/text
     * - date_naissance_$alias/acteur.date_naissance/text
     * - adresse1_$alias/acteur.adresse1/text
     * - adresse2_$alias/acteur.adresse2/text
     * - cp_$alias/acteur.cp/text
     * - ville_$alias/acteur.ville/text
     * - note_$alias/acteur.note/text
     * @param GDO_PartType $oMainPart l'objet GDO_PartType à remplir
     * @param integer $acteur_id, l'identifiant de l'acteur en base
     */
    function makeBalise(&$oMainPart, $acteur_id) {
        if ($this->exists($acteur_id)) {
            $acteur = $this->find('first', array('conditions' => array($this->alias . '.id' => $acteur_id), 'recursive' => -1));
            $alias = trim(strtolower($this->alias));
            $oMainPart->addElement(new GDO_FieldType("salutation_$alias", ($acteur[$this->alias]['salutation']), 'text'));
            $oMainPart->addElement(new GDO_FieldType("prenom_$alias", ($acteur[$this->alias]['prenom']), 'text'));
            $oMainPart->addElement(new GDO_FieldType("nom_$alias", ($acteur[$this->alias]['nom']), 'text'));
            $oMainPart->addElement(new GDO_FieldType("titre_$alias", ($acteur[$this->alias]['titre']), 'text'));
            $oMainPart->addElement(new GDO_FieldType("position_$alias", ($acteur[$this->alias]['position']), 'text'));
            $oMainPart->addElement(new GDO_FieldType("email_$alias", ($acteur[$this->alias]['email']), 'text'));
            $oMainPart->addElement(new GDO_FieldType("telmobile_$alias", ($acteur[$this->alias]['telmobile']), 'text'));
            $oMainPart->addElement(new GDO_FieldType("telfixe_$alias", ($acteur[$this->alias]['telfixe']), 'text'));
            $oMainPart->addElement(new GDO_FieldType("date_naissance_$alias", ($acteur[$this->alias]['date_naissance']), 'text'));
            $oMainPart->addElement(new GDO_FieldType("adresse1_$alias", ($acteur[$this->alias]['adresse1']), 'text'));
            $oMainPart->addElement(new GDO_FieldType("adresse2_$alias", ($acteur[$this->alias]['adresse2']), 'text'));
            $oMainPart->addElement(new GDO_FieldType("cp_$alias", ($acteur[$this->alias]['cp']), 'text'));
            $oMainPart->addElement(new GDO_FieldType("ville_$alias", ($acteur[$this->alias]['ville']), 'text'));
            $oMainPart->addElement(new GDO_FieldType("note_$alias", ($acteur[$this->alias]['note']), 'text'));
        }
    }

    // -------------------------------------------------------------------------

		/**
		 * Normalisation des enregistrements: ajout des valeurs calculées, ...
		 *
		 * @param array $records
		 * @return array
		 */
		public function gedoooNormalizeAll( $category, array $acteurs ) {
			$nombre = count( $acteurs );
			if( $nombre == 0 ) {
				$acteurs = array( array() );
			}

			$return = array();
			foreach( $acteurs as $acteur ) {
				$suffix = $category;
				if( $category === 'mandate' ) {
					$suffix = 'mandataire';
				}
				$foo1 = $this->gedoooNormalize( $suffix, $nombre, 'Acteur', $acteur );

				$foo2 = array();
				if( $category !== 'present' ) {
					$suffix = 'mandate';
                                        // FIXME : l'indice ActeurMandate n'existe pas ! (remplacer par Suppleant?)
					$foo2 = $this->gedoooNormalize( $suffix, false, 'ActeurMandate', $acteur );
				}

				$return[] = array_merge( $foo1, $foo2 );
			}

			return $return;
		}

		/**
		 * Normalisation d'un enregistrement: ajout des valeurs calculées, ...
		 *
		 * @param array $records
		 * @return array
		 */
		public function gedoooNormalize( $suffix, $nombre, $alias, array $item ) {
			$return = array(
				"nombre_acteur_{$suffix}" => $nombre, // Pas tout le temps ?
				"nom_acteur_{$suffix}" => Hash::get( $item, "{$alias}.nom" ),
				"prenom_acteur_{$suffix}" => Hash::get( $item, "{$alias}.prenom" ),
				"salutation_acteur_{$suffix}" => Hash::get( $item, "{$alias}.salutation" ),
				"titre_acteur_{$suffix}" => Hash::get( $item, "{$alias}.titre" ),
				"date_naissance_acteur_{$suffix}" => Hash::get( $item, "{$alias}.date_naissance" ),
				"adresse1_acteur_{$suffix}" => Hash::get( $item, "{$alias}.adresse1" ),
				"adresse2_acteur_{$suffix}" => Hash::get( $item, "{$alias}.adresse2" ),
				"cp_acteur_{$suffix}" => Hash::get( $item, "{$alias}.cp" ),
				"ville_acteur_{$suffix}" => Hash::get( $item, "{$alias}.ville" ),
				"email_acteur_{$suffix}" => Hash::get( $item, "{$alias}.email" ),
				"telfixe_acteur_{$suffix}" => Hash::get( $item, "{$alias}.telfixe" ),
				"telmobile_acteur_{$suffix}" => Hash::get( $item, "{$alias}.telmobile" ),
				"note_acteur_{$suffix}" => Hash::get( $item, "{$alias}.note" ),
			);

			if( $nombre === false ) {
				unset( $return["nombre_acteur_{$suffix}"] );
			}

			return $return;
		}
}

?>
