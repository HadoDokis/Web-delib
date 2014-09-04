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

class Acteur extends AppModel
{
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

	var $belongsTo = array('Suppleant' => array( 'className' => 'Acteur', 'foreignKey' => 'suppleant_id'),
                               'Typeacteur' => array( 'className' => 'Typeacteur', 'foreignKey' => 'typeacteur_id'));


	var $hasAndBelongsToMany = array(
		'Service' => array(
			'classname'=>'Service',
			'joinTable'=>'acteurs_services',
			'foreignKey'=>'acteur_id',
			'associationForeignKey'=>'service_id',
			'conditions'=>'',
			'order'=>'',
			'limit'=>'',
			'unique'=>true,
			'finderQuery'=>'',
			'deleteQuery'=>'')
		);

	/* retourne la liste des acteurs élus [id]=>[prenom et nom] pour utilisation html->selectTag */
	function generateListElus($order_by=null) {
            $generateListElus = array();
            $acteurs = $this->find('all', array(
                                                'fields'    => array('id', 'nom', 'prenom'),
                                                'conditions' => array('Typeacteur.elu'=> true,  'Acteur.actif' => true), 
                                                'order'     => (empty($order_by)?'Acteur.position':$order_by).' ASC',
                                                'joins' => array($this->join('Typeacteur',array( 'type' => 'INNER' ))),
                                                'recursive' => -1
                    ));
            foreach($acteurs as $acteur) {
                $generateListElus[$acteur['Acteur']['id']] = $acteur['Acteur']['prenom'].' '.$acteur['Acteur']['nom'];
            }
            return $generateListElus;
	}

	/* retourne la liste des acteurs [id]=>[prenom et nom] pour utilisation html->selectTag */
	function generateList($order_by=null) {
		$generateList = array();
                if ($order_by==null)
                        $acteurs = $this->find('all', array('conditions' => array('Acteur.actif' => 1),
                                                             'fields'    => array('id', 'nom', 'prenom'),
                                                             'order'     => 'Acteur.position ASC'));
                else    
                        $acteurs = $this->find('all', array('conditions' => array('Acteur.actif' => 1), 
                                                             'fields'    => array('id', 'nom', 'prenom'),
                                                             'order'     => "$order_by ASC"));


		foreach($acteurs as $acteur) {
			$generateList[$acteur['Acteur']['id']] = $acteur['Acteur']['prenom'].' '.$acteur['Acteur']['nom'];
		}

		return $generateList;
	}

	/* retourne l'id du premier acteur élu associé à la délégation $serviceId */
	/* retourne null si non trouvé                                            */
	function selectActeurEluIdParDelegationId($delegationId) {
		$users = $this->find('all', array('conditions' => array('Typeacteur.elu'=>1, 'Acteur.actif'=>1 ),
                                                  'fields'     => array ('id'),
                                                  'order' => 'Acteur.position ASC'));
             
		foreach($users as $user) {
			foreach($user['Service'] as $service) {
				if ($service['id'] == $delegationId) return $user['Acteur']['id'];
			}
		}
		return null;
	}


	/* retourne le numéro de position max pour tous les acteurs élus */
	/* pour rester compatible avec le plus grand nombre de bd, on ne passe pas de requête */
	/* mais on fait le calcul en php */
	function getPostionMaxParActeursElus() {
		$acteur = $this->find('all', array ('conditions'=> array('Typeacteur.elu'=>1, 'Acteur.actif'=>1), 
                                                    'fields'    => array('Acteur.position'),
                                                    'order'     => 'Acteur.position DESC'));
		return empty($acteur) ? 0 : $acteur[0]['Acteur']['position'];
	}

	/* retourne le libellé correspondant au champ position : = 999 : en dernier, <999 : position */
	function libelleOrdre($ordre = null, $majuscule = false) {
		return ($ordre == 999) ? ($majuscule ? 'En dernier' : 'en dernier') : $ordre;
	}
  
    /**
     * fonction d'initialisation des variables de fusion pour l'allias utilisé pour la liaison (Rapporteur, President, ...)
     * les bibliothèques Gedooo doivent être inclues par avance
     * génère une exception en cas d'erreur
     * @param object_by_ref $oMainPart variable Gedooo de type maintPart du document à fusionner
     * @param object_by_ref $modelOdtInfos objet PhpOdtApi du fichier odt du modèle d'édition
     * @param integer $id id du modèle lié
     * @param string $suffixe suffixe des variables de fusion
     * @throws Exception
     */
    function setVariablesFusion(&$aData, &$modelOdtInfos, $id, $suffixe='') {
        // initialisations
        if (empty($suffixe))
            $suffixe = trim(strtolower($this->alias));
        $fields = array();
        $variables = array(
            'salutation',
            'prenom',
            'nom',
            'titre',
            'position',
            'email',
            'telmobile',
            'telfixe',
            'date_naissance',
            'adresse1',
            'adresse2',
            'cp',
            'ville',
            'note'
        );

        // liste des variables présentes dans le modèle d'édition
        foreach ($variables as $variable){
            if ($modelOdtInfos->hasUserFieldDeclared($variable.'_'.$suffixe)) 
                    $fields[]= $variable;
        }   
        if (empty($fields)) return;

        // lecture en base de données
        $acteur = $this->find('first', array(
            'recursive' => -1,
            'fields' => $fields,
            'conditions' => array('id' => $id)));
        if (empty($acteur))
            throw new Exception('acteur '.$suffixe.' id:'.$id.' non trouvé en base de données');
        foreach($acteur[$this->alias] as $field => $val)
            $aData[$field.'_'.$suffixe]=$val;//, 'text'));
    }

}