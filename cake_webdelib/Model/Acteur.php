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
		if ($order_by==null)
			$acteurs = $this->find('all', array('conditions' => array('Typeacteur.elu'=> 1, 'Acteur.actif' => 1), 
                                                             'fields'    => array('id', 'nom', 'prenom'),
                                                             'order'     => 'Acteur.position ASC'));
		else
			$acteurs = $this->find('all', array('conditions' => array('Typeacteur.elu'=> 1,  'Acteur.actif' => 1), 
                                                             'fields'    => array('id', 'nom', 'prenom'),
                                                             'order'     => "$order_by ASC"));
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
  
        function makeBalise(&$oMainPart, $acteur_id) {
                if ($this->exists($acteur_id)){
                        $acteur = $this->find('first', 
                                              array('conditions' => array($this->alias.'.id' => $acteur_id),
                                                    'recursive'  => -1));
                        $alias = trim(strtolower($this->alias));
                        $oMainPart->addElement(new GDO_FieldType("salutation_$alias",     ($acteur[$this->alias]['salutation']), 'text'));
                        $oMainPart->addElement(new GDO_FieldType("prenom_$alias",         ($acteur[$this->alias]['prenom']),     'text'));
                        $oMainPart->addElement(new GDO_FieldType("nom_$alias",            ($acteur[$this->alias]['nom']),        'text'));
                        $oMainPart->addElement(new GDO_FieldType("titre_$alias",          ($acteur[$this->alias]['titre']),      'text'));
                        $oMainPart->addElement(new GDO_FieldType("position_$alias",       ($acteur[$this->alias]['position']),   'text'));
                        $oMainPart->addElement(new GDO_FieldType("email_$alias",          ($acteur[$this->alias]['email']),      'text'));
                        $oMainPart->addElement(new GDO_FieldType("telmobile_$alias",      ($acteur[$this->alias]['telmobile']),  'text'));
                        $oMainPart->addElement(new GDO_FieldType("telfixe_$alias",        ($acteur[$this->alias]['telfixe']),    'text'));
                        $oMainPart->addElement(new GDO_FieldType("date_naissance_$alias", ($acteur[$this->alias]['date_naissance']), 'text'));
                        $oMainPart->addElement(new GDO_FieldType("adresse1_$alias",       ($acteur[$this->alias]['adresse1']),   'text'));
                        $oMainPart->addElement(new GDO_FieldType("adresse2_$alias",       ($acteur[$this->alias]['adresse2']),   'text'));
                        $oMainPart->addElement(new GDO_FieldType("cp_$alias",             ($acteur[$this->alias]['cp']),         'text'));
                        $oMainPart->addElement(new GDO_FieldType("ville_$alias",          ($acteur[$this->alias]['ville']),      'text'));
                        $oMainPart->addElement(new GDO_FieldType("note_$alias",           ($acteur[$this->alias]['note']),       'text'));
                }
        }

    /**
     * fonction d'initialisation des variables de fusion pour l'allias utilisé pour la liaison (Rapporteur, President, ...)
     * les bibliothèques Gedooo doivent être inclues par avance
     * génère une exception en cas d'erreur
     * @param object_by_ref $oMainPart variable Gedooo de type maintPart du document à fusionner
     * @param object_by_ref $modelOdtInfos objet PhpOdtApi du fichier odt du modèle d'édition
     * @param integer $id id du modèle lié
     * @param string $suffixe suffixe des variables de fusion
     */
    function setVariablesFusion(&$oMainPart, &$modelOdtInfos, $id, $suffixe='') {
        // initialisations
        if (empty($suffixe))
            $suffixe = trim(strtolower($this->alias));
        $fields = array();
        if ($modelOdtInfos->hasUserField('salutation_'.$suffixe)) $fields[]= 'salutation';
        if ($modelOdtInfos->hasUserField('prenom_'.$suffixe)) $fields[]= 'prenom';
        if ($modelOdtInfos->hasUserField('nom_'.$suffixe)) $fields[]= 'nom';
        if ($modelOdtInfos->hasUserField('titre_'.$suffixe)) $fields[]= 'titre';
        if ($modelOdtInfos->hasUserField('position_'.$suffixe)) $fields[]= 'position';
        if ($modelOdtInfos->hasUserField('email_'.$suffixe)) $fields[]= 'email';
        if ($modelOdtInfos->hasUserField('telmobile_'.$suffixe)) $fields[]= 'telmobile';
        if ($modelOdtInfos->hasUserField('telfixe_'.$suffixe)) $fields[]= 'telfixe';
        if ($modelOdtInfos->hasUserField('date_naissance_'.$suffixe)) $fields[]= 'date_naissance';
        if ($modelOdtInfos->hasUserField('adresse1_'.$suffixe)) $fields[]= 'adresse1';
        if ($modelOdtInfos->hasUserField('adresse2_'.$suffixe)) $fields[]= 'adresse2';
        if ($modelOdtInfos->hasUserField('cp_'.$suffixe)) $fields[]= 'cp';
        if ($modelOdtInfos->hasUserField('ville_'.$suffixe)) $fields[]= 'ville';
        if ($modelOdtInfos->hasUserField('note_'.$suffixe)) $fields[]= 'note';
        if (empty($fields)) return;

        // lecture en base de données
        $acteur = $this->find('first', array(
            'recursive' => -1,
            'fields' => $fields,
            'conditions' => array('id' => $id)));
        if (empty($acteur))
            throw new Exception('acteur '.$suffixe.' id:'.$id.' non trouvé en base de données');
        foreach($acteur[$this->alias] as $field => $val)
            $oMainPart->addElement(new GDO_FieldType($field.'_'.$suffixe, $val, 'text'));
    }

}
?>
