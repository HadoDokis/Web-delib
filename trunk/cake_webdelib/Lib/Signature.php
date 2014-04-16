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
App::uses('ComponentCollection', 'Controller');
App::uses('Component', 'Controller');
App::uses('IparapheurComponent', 'Controller/Component');
App::uses('PastellComponent', 'Controller/Component');
App::uses('Deliberation', 'Model');
App::uses('Collectivite', 'Model');

class Signature {

    /**
     * @var string Protocole de signature (pastell|iparapheur)
     */
    private $connecteur;

    /**
     * @var Composant PastellComponent
     */
    private $Pastell;

    /**
     * @var Composant IparapheurComponent
     */
    private $Iparapheur;

    /**
     * @var Model Deliberation
     */
    private $Deliberation;

    /**
     * @var string type technique dans le parapheur
     */
    private $parapheur_type;

    /**
     * @var string type pastell
     */
    private $pastell_type;

    /**
     * @var int|string collectivite
     */
    private $collectivite;

    /**
     * @var string visibilité du document parapheur
     */
    private $visibility;

    /**
     * @var array configuration
     */
    private $config;

    /**
     * Appelée lors de l'initialisation de la librairie
     * Charge le bon protocol de signature et initialise le composant correspondant
     */
    public function __construct() {
        $collection = new ComponentCollection();
        $use_parapheur = Configure::read('USE_PARAPHEUR');
        $protocol = Configure::read('PARAPHEUR');

        if ($use_parapheur) {
            if (empty($protocol))
                throw new Exception("Aucun parapheur désigné");

            if (Configure::read("USE_$protocol"))
                $this->connecteur = Configure::read('PARAPHEUR');
            else
                throw new Exception("Le connecteur parapheur désigné n'est pas activé : USE_$protocol");

            if ($protocol == 'PASTELL') {
                $this->Pastell = new PastellComponent($collection);
                //Enregistrement de la collectivité (pour pastell)
                $Collectivite = new Collectivite();
                $Collectivite->id = 1;
                $this->collectivite = $Collectivite->field('id_entity');
                $this->pastell_type = Configure::read('PASTELL_TYPE');
                $pastell_config = Configure::read('Pastell');
                $this->config = $pastell_config[$this->pastell_type];
            }
            if ($protocol == 'IPARAPHEUR') {
                $this->Iparapheur = new IparapheurComponent($collection);
                $this->visibility = Configure::read('IPARAPHEUR_VISIBILITY');
            }
            $this->Deliberation = new Deliberation;
            $this->parapheur_type = Configure::read('IPARAPHEUR_TYPE');
        }
    }

    /**
     * Fonction appelée à chaque appel de fonction non connu
     * et redirige vers la bonne fonction selon le protocol
     *
     * @param string $name nom de la fonction appelée
     * @param array $arguments tableau d'arguments indexés
     * @return mixed
     * @throws Exception
     */
    public function __call($name, $arguments) {
        $suffix = ucfirst(strtolower($this->connecteur));

        if (method_exists($this, $name . $suffix))
            return call_user_func_array(array($this, $name . $suffix), $arguments);
        else {
            throw new Exception(sprintf('The required method "%s" does not exist for %s', $name . $suffix, get_class()));
        }
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
        $date_limite = !empty($delib['Deliberation']['date_limite']) ? $delib['Deliberation']['date_limite'] : null;

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
    public function sendPastell($delib, $circuit_id, $document = null, $annexes = array()) {
        //Vérifications
        if (empty($delib['Deliberation']['num_pref']))
            return false;
        //Présence document ? delib_pdf ?
        if (empty($document))
            if (!empty($delib['Deliberation']['delib_pdf']))
                $document = $delib['Deliberation']['delib_pdf'];
            else
                return false;


        $id_d = $this->Pastell->createDocument($this->collectivite, $this->pastell_type);
        try {
            if (!$this->Pastell->modifDocument($this->collectivite, $id_d, $delib, $document, $annexes))
                throw new Exception();

            if (is_numeric($circuit_id)) {
                $circuits = $this->Pastell->getInfosField($this->collectivite, $id_d, $this->config['field']['iparapheur_sous_type']);
                $sousType = $circuits[$circuit_id];
            } else {
                $sousType = $circuit_id;
            }

            $this->Pastell->selectCircuit($this->collectivite, $id_d, $sousType);
            if ($this->Pastell->action($this->collectivite, $id_d, $this->config['action']['send-iparapheur']))
                return $id_d;
            else
                throw new Exception();
        } catch (Exception $e) {
            $this->Pastell->action($this->collectivite, $id_d, $this->config['action']['supression']);
            return false;
        }
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
        $details = $this->Pastell->detailDocument($this->collectivite, $id_d);
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
        $bordereau = $this->Pastell->getFile($this->collectivite, $id_d, $this->config['field']['document_signe']);
        return $bordereau;
    }

    /**
     * @param int $id_d
     * @return string flux du fichier signature (zip)
     */
    public function getSignaturePastell($id_d) {
        $signature = $this->Pastell->getFile($this->collectivite, $id_d, $this->config['field']['signature']);
        return $this->Pastell->getFile($this->collectivite, $id_d, $this->config['field']['signature']);
    }

    /**
     * @param int $id_d
     * @return array
     */
    public function getDetailsPastell($id_d) {
        return $this->Pastell->detailDocument($this->collectivite, $id_d);
    }

    /**
     * @param int $id_d
     * @return bool
     */
    public function isSignePastell($id_d) {
        $details = $this->Pastell->detailDocument($this->collectivite, $id_d);
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
        return $this->Pastell->action($this->collectivite, $id_d, $this->config['action']['supression']);
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
        return $this->Pastell->getCircuits($this->collectivite);
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
        $this->Pastell->action($this->collectivite, $id_d, $this->config['action']['verif-iparapheur']);
        return $this->getDetailsPastell($id_d);
    }
}