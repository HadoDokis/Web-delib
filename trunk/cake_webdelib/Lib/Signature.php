<?php

/**
 * @author Florian Ajir <florian.ajir@adullact.org>
 * @editor Adullact
 *
 * @created 21 janvier 2014
 *
 * Interface controllant les actions de signature électronique
 * Utilise les connecteurs/components PastellComponent et IparapheurComponent
 *
 */
App::uses('IparapheurComponent', 'Controller/Component');
App::uses('ConnecteurLib', 'Lib');

class Signature extends ConnecteurLib  {

    /**
     * @var Composant IparapheurComponent
     */
    private $Iparapheur;

    /**
     * @var string type technique dans le parapheur
     */
    private $parapheur_type;

    /**
     * @var string visibilité du document parapheur
     */
    private $visibility;


    /**
     * Appelée lors de l'initialisation de la librairie
     * Charge le bon protocol de signature et initialise le composant correspondant
     */
    public function __construct() {
        //FIXME cas où vide => exception levée lors de la tache planifiée
        parent::__construct(Configure::read('PARAPHEUR'));

        if (Configure::read('USE_PARAPHEUR')) {
            if (!$this->getType())
                throw new Exception("Aucun parapheur désigné");

            if (!Configure::read("USE_".$this->getType()))
                throw new Exception("Le connecteur parapheur désigné n'est pas activé : USE_".$this->getType());

            if ($this->getType() == 'IPARAPHEUR') {
                $this->Iparapheur = new IparapheurComponent($this->collection);
                $this->visibility = Configure::read('IPARAPHEUR_VISIBILITY');
            }
            $this->parapheur_type = Configure::read('IPARAPHEUR_TYPE');
        }
        else
            $this->active = false;
    }

    /**
     * @param array $delib
     * @param int|string $circuit_id
     * @param string $document contenu du fichier du document principal
     * @param array $annexes (content, filename, mimetype)
     * @return bool|int false si echec sinon identifiant iparapheur
     */
    public function sendIparapheur($delib, $circuit_id, $document, $annexes = array()) {

        if (is_numeric($circuit_id)) {
            $circuits = $this->listCircuitsIparapheur();
            $libelleSousType = $circuits[$circuit_id];
        } else {
            $libelleSousType = $circuit_id;
        }
        $targetName = $delib['Deliberation']['objet_delib'];
        $date_limite = !empty($delib['Deliberation']['date_limite']) ? $this->Time->i18nFormat($deliberation['Deliberation']['date_limite'], '%A %d %B %G à %k:%M'): null;

        $ret = $this->Iparapheur->creerDossierWebservice(
            $targetName,
            $this->parapheur_type,
            $libelleSousType,
            $this->visibility,
            $document,
            $annexes,
            $date_limite
        );

        if ($ret['messageretour']['coderetour'] == 'OK') {
            return $ret['dossierID'];
        } else {
            CakeLog::write($ret['messageretour']['message'], 'parapheur');
            return false;
        }
    }

    /**
     * @param array $delib
     * @param int|string $circuit_id
     * @param string $document contenu du fichier du document principal
     * @param array $annexes (content, filename)
     * @return bool|int false si echec sinon identifiant pastell
     */
    public function sendPastell($acte, $circuit_id, $document = null, $annexes = array()) {
        //Vérifications
        if (empty($acte['Deliberation']['num_pref']))
            return false;
        //Présence document ? delib_pdf ?
        if (empty($document))
            if (!empty($acte['Deliberation']['delib_pdf']))
                $document = $acte['Deliberation']['delib_pdf'];
            else
                return false;

            
        try {
            $id_d = $this->Pastell->createDocument($this->id_e, $this->pastell_type);
        
            if (!$this->Pastell->editTransmission($this->id_e, $id_d))
                throw new Exception('Error editTransmission');
            
            if (!$this->Pastell->modifDocument($this->id_e, $id_d, $acte, $document, $annexes))
                throw new Exception('Error modifDocument');

            if (is_numeric($circuit_id)) {
                $circuits = $this->Pastell->getInfosField($this->id_e, $id_d, $this->config['field']['iparapheur_sous_type']);
                $sousType = $circuits[$circuit_id];
            } else {
                $sousType = $circuit_id;
            }

            $this->Pastell->selectCircuit($this->id_e, $id_d, $sousType);
            if ($this->Pastell->action($this->id_e, $id_d, $this->config['action']['send-iparapheur']))
                return $id_d;
            else
                throw new Exception('Error action');
            
        } catch (Exception $e) {
            $this->Pastell->action($this->id_e, $id_d, $this->config['action']['supression']);
            throw new Exception($e->getMessage());
            
        }
        
        return false;
    }

    public function getEtatIparapheur($options) {
    }

    /**
     * @param int $id_d
     * @param bool $get_details
     * @return array(
     * 'action' => 'rejet-iparapheur',
     * 'message' => 'Le document a été rejeté dans le parapheur : 23/01/2014 16:54:52 : [RejetSignataire] vu',
     * 'date' => '2014-01-23 16:55:07'
     * )
     */
    public function getLastActionPastell($id_d, $get_details = false) {
        $details = $this->Pastell->detailDocument($this->id_e, $id_d);
        if ($get_details)
            return $details['last_action'];
        $last_action = $details['last_action']['action'];
        return $last_action;
    }

    /**
     * @param int $id_d
     * @return string flux du fichier bordereau
     */
    public function getBordereauPastell($id_d) {
        return $this->Pastell->getFile($this->id_e, $id_d, $this->config['field']['document_signe']);
    }

    /**
     * @param int $id_d
     * @return string flux du fichier signature (zip)
     */
    public function getSignaturePastell($id_d) {
        return $this->Pastell->getFile($this->id_e, $id_d, $this->config['field']['signature']);
    }

    /**
     * @param int $id_d
     * @return array
     */
    public function getDetailsPastell($id_d) {
        return $this->Pastell->detailDocument($this->id_e, $id_d);
    }

    /**
     * @param int $id_d
     * @return bool
     */
    public function isSignePastell($id_d) {
        $details = $this->Pastell->detailDocument($this->id_e, $id_d);
        return !empty($details['data'][$this->config['field']['has_signature']]);
    }

    /**
     * @param int $id
     */
    public function deleteIparapheur($id) {
        $this->Iparapheur->effacerDossierRejeteWebservice($id);
    }

    /**
     * @param int $id
     */
    public function archiveIparapheur($id) {
        $this->Iparapheur->archiverDossierWebservice($id, 'EFFACER');
    }

    /**
     * @param int $id_d
     * @return array
     */
    public function deletePastell($id_d) {
        return $this->Pastell->action($this->id_e, $id_d, $this->config['action']['supression']);
    }

    /**
     * @link listCircuits()
     * @return array
     * @throws Exception
     */
    public function listCircuitsIparapheur() {
        $resp = $this->Iparapheur->getListeSousTypesWebservice($this->parapheur_type);
        if (array_key_exists('soustype', $resp))
            return $resp['soustype'];
        else {
            throw new Exception($resp['messageretour']['message']);
        }
    }

    /**
     * @link listCircuits()
     * @return array
     */
    public function listCircuitsPastell() {
        return $this->Pastell->getCircuits($this->id_e);
    }

    /**
     * @return array
     */
    public function printCircuits() {
        $circuits = array('Standard' => array('-1' => 'Signature manuscrite'));
        try {
            $circuits_parapheur = $this->listCircuits();
            if (!empty($circuits_parapheur))
                $circuits['Parapheur'] = $circuits_parapheur;
        } catch (Exception $e) {
        } //Si erreur de connexion au parapheur

        return $circuits;
    }

    /**
     * @link updateAll()
     * @return array
     */
    public function updateAllIparapheur() {
        return $this->Deliberation->majActesParapheur();
    }

    /**
     * @link updateAll()
     * @return array
     */
    public function updateAllPastell() {
        return $this->Deliberation->majSignaturesPastell();
    }

    /**
     * @param $id_d
     * @return array
     */
    public function updateInfosPastell($id_d) {
        $details=$this->getDetailsPastell($id_d);
        if (!empty($details['last_action']['action']) && $details['last_action']['action'] === 'recu-iparapheur') {
            return $details;
        } else {
            $this->Pastell->action($this->id_e, $id_d, $this->config['action']['verif-iparapheur']);
        }

        return $this->getDetailsPastell($id_d);
    }
}