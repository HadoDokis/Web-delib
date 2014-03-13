<?php
/**
 * @author Florian Ajir <florian.ajir@adullact.org>
 * @company Adullact
 *
 * @created 21 janvier 2014
 *
 * Interface controllant les actions de signature électronique
 * Utilise les connecteurs/components PastellComponent et S2lowComponent
 *
 */
App::uses('ComponentCollection', 'Controller');
App::uses('Component', 'Controller');
App::uses('S2lowComponent', 'Controller/Component');
App::uses('PastellComponent', 'Controller/Component');
App::uses('Deliberation', 'Model');
App::uses('TdtMessage', 'Model');
App::uses('Collectivite', 'Model');

/**
 * Class Tdt
 * @package App.Lib.Tdt
 */
class Tdt {

    /**
     * @var string Protocole de signature (pastell|iparapheur)
     */
    private $connecteur;

    /**
     * @var Composant PastellComponent
     */
    private $Pastell;

    /**
     * @var Composant s2lowComponent
     */
    private $S2low;

    /**
     * @var Model Deliberation
     */
    private $Deliberation;

    /**
     * @var Model TdtMessage
     */
    private $TdtMessage;

    /**
     * @var string type pastell
     */
    private $pastell_type;

    /**
     * @var int|string $id_e
     */
    private $id_e;

    /**
     * @var array type_message => libelle_type_message
     */
    public $echanges = array(
        '2' => 'courrier_simple',
        '3' => 'demande_piece_complementaire',
        '4' => 'lettre_observation',
        '5' => 'defere_tribunal_administratif'
    );

    /**
     * Appelée lors de l'initialisation de la librairie
     * Charge le bon protocol de signature et initialise le composant correspondant
     */
    public function __construct() {
        $collection = new ComponentCollection();
        $signature = Configure::read('USE_TDT');
        $protocol = Configure::read('TDT');

        if (!$signature)
            throw new Exception("Signature désactivée");
        if (empty($protocol))
            throw new Exception("Aucun TDT désigné");

        if (Configure::read("USE_$protocol"))
            $this->connecteur = Configure::read('TDT');
        else
            throw new Exception("Le connecteur tdt désigné n'est pas activé : USE_$protocol");

        if ($protocol == 'PASTELL') {
            $this->Pastell = new PastellComponent($collection);
            //Enregistrement de la collectivité (pour pastell)
            $Collectivite = new Collectivite();
            $Collectivite->id = 1;
            $this->id_e = $Collectivite->field('id_entity');
            $this->pastell_type = Configure::read('PASTELL_TYPE');
        }
        if ($protocol == 'S2LOW') {
            $this->S2low = new S2lowComponent($collection);
        }
        $this->TdtMessage = new TdtMessage;
        $this->Deliberation = new Deliberation;
    }

    /**
     * Fonction appelée à chaque appel de fonction non connu
     * et redirige vers la bonne fonction selon le protocol
     *
     * @param string $name nom de la fonction appelée
     * @param array $arguments tableau d'arguments indexés
     * @return mixed
     */
    public function __call($name, $arguments) {
        $suffix = ucfirst(strtolower($this->connecteur));
        
        if (method_exists($this, $name.$suffix))
                return call_user_func_array(array($this, $name.$suffix), $arguments);
        else{
          throw new Exception(sprintf('The required method "%s" does not exist for %s', $name.$suffix, get_class($this)));
        } 
        
       /* $suffix = ucfirst(strtolower($this->connecteur));
        if (empty($arguments))
            return $this->{$name . $suffix}();
        else
            return $this->{$name . $suffix}($arguments);*/
    }

    /**
     * Envoi le dossier dans pastell au TDT
     * @param $options
     * [0] => $id_d,
     * @return bool|string
     */
    public function sendPastell($options) {
        $id_d = $options[0];
        $classification = !empty($options[1]) ? $options[1] : null;
        $this->Pastell->envoiTdt($this->id_e, $id_d, $classification);
        $send = $this->Pastell->action($this->id_e, $id_d, 'send-tdt');
        //Récupération du tdt_id
        $infos = $this->Pastell->detailDocument($this->id_e, $id_d);
        if (!empty($infos['data']['tedetis_transaction_id'])) {
            $delib = $this->Deliberation->find('first', array(
                'recursive' => -1,
                'fields' => array('id'),
                'conditions' => array('pastell_id' => $id_d)
            ));
            $this->Deliberation->id = $delib['Deliberation']['id'];
            $this->Deliberation->saveField('tdt_id', $infos['data']['tedetis_transaction_id']);
        }
        return $send;
    }

    /**
     * TODO
     */
    public function sendS2low() {

    }

    /**
     * @param $options
     * @return array(
     * 'action' => 'rejet-iparapheur',
     * 'message' => 'Le document a été rejeté dans le parapheur : 23/01/2014 16:54:52 : [RejetSignataire] vu',
     * 'date' => '2014-01-23 16:55:07'
     * )
     */
    public function getLastActionPastell($options) {
        $id_d = $options[0];
        $get_details = !empty($options[1]);
        $details = $this->Pastell->detailDocument($this->id_e, $id_d);
        if ($get_details)
            return $details['last_action'];
        $last_action = $details['last_action']['action'];
        return $last_action;
    }

    public function getDetailsPastell($options) {
        $id_d = $options[0];
        return $this->Pastell->detailDocument($this->id_e, $id_d);
    }

    public function getReponsesPastell($options, $all = false) {
        $tdt_messages = array();
        $id_d = $options[0];
        $this->Pastell->action($this->id_e, $id_d, 'verif-reponse-tdt');
        $infos = $this->Pastell->detailDocument($this->id_e, $id_d);

        if (empty($infos['data']))
            return false;

        foreach ($this->echanges as $type => $echange){
            if (!empty($infos['data']['has_'.$echange])){
                if ($all || !$this->TdtMessage->existe($infos['data'][$echange.'_id'])){
                    $tdt_messages[] = array(
                        'TdtMessage' => array(
                            'type_message' => $type,
                            'data' => $this->Pastell->getFile($this->id_e, $id_d, $echange),
                            'message_id' => $infos['data'][$echange.'_id'],
                            'date_message' => $infos['data'][$echange.'_date']
                        )
                    );
                }
            }
        }

        return $tdt_messages;
    }

    /**
     * @param array $options
     * @return mixed
     */
    public function getReponsesS2low($options) {
        $tdt_messages = array();
        $tdt_id= $options[0];
        $result = $this->S2low->getNewFlux($tdt_id);
        if (!empty($result)){
            $result_array = explode("\n", trim($result));
            if ($result_array[0] != 'KO'){
                foreach ($result_array as $result) {
                    if (!empty($result)) {
                        $tdt_message = array();
                        $infos = explode('-', $result);
                        $tdt_message['TdtMessage']['type_message'] = $infos[0];
                        $tdt_message['TdtMessage']['type_reponse'] = $infos[1];
                        $tdt_message['TdtMessage']['message_id'] = $infos[2];
                        $tdt_messages[] = $tdt_message;
                    }
                }
            }
        }
        return $tdt_messages;
    }

    public function getDateArPastell($options) {
        $id_d = $options[0];
        $this->Pastell->action($this->id_e, $id_d, 'verif-tdt');
        $infos = $this->Pastell->detailDocument($this->id_e, $id_d);
        if (!empty($infos['data']['date_ar']))
            return $infos['data']['date_ar'];
        else
            return false;
    }

    /**
     * @param $options
     * @return bool
     */
    public function getDateArS2low($options) {
        $tdt_id = $options[0];
        $flux = $this->S2low->getFluxRetour($tdt_id);
        $codeRetour = substr($flux, 3, 1);

        if ($codeRetour == 4) {
            $dateAR = $this->S2low->getDateAR($res = mb_substr($flux, strpos($flux, '<actes:ARActe'), strlen($flux)));
            return $dateAR;
        }
        return false;
    }

    /**
     * @return array
     */
    public function listClassification() {
        $liste = array();
        App::uses('Nomenclature', 'Model');
        $Nomenclature = new Nomenclature();
        $categories = $Nomenclature->find('list', array(
            'fields' => array('id', 'libelle'),
            'conditions' => array('parent_id' => 0)
        ));
        $nomenclatures = $Nomenclature->find('all', array(
            'fields' => array('id', 'libelle', 'parent_id'),
            'conditions' => array('parent_id <>' => 0)
        ));
        foreach ($nomenclatures as $nom) {
            $niveau = substr_count($nom['Nomenclature']['id'], '.');
            $liste[$nom['Nomenclature']['parent_id']][$nom['Nomenclature']['id']] = str_repeat('&nbsp;', $niveau * 2) . $nom['Nomenclature']['id'] . ' ' . $nom['Nomenclature']['libelle'];
        }

        foreach ($liste as $titre => $array) {
            ksort($array);
            $liste[$titre . ' ' . $categories[$titre]] = $array;
            unset($liste[$titre]);
        }
        ksort($liste);
        return $liste;
    }

    public function updateClassificationS2low() {
        return $this->S2low->getClassification();
    }

    public function updateClassificationPastell() {
        //Création d'un dossier temporaire
        $id_d = $this->Pastell->createDocument($this->id_e);
        //Récupération de la classification
        $classification = $this->Pastell->getClassification($this->id_e, $id_d, 'classification');
        //Suppression du dossier temporaire
        $this->Pastell->delete($this->id_e, $id_d);

        App::uses('Nomenclature', 'Model');
        $Nomenclature = new Nomenclature();
        if (!empty($classification))
            $Nomenclature->deleteAll(array());

        foreach ($classification as $class => $actif) {
            if ($actif) {
                $data = array();
                //Découpe la chaine par espace
                $str_tab = explode(' ', $class);
                //Premier élément : code
                $data['Nomenclature']['id'] = array_shift($str_tab);
                $data['Nomenclature']['libelle'] = implode(' ', $str_tab);
                $id_tab = explode('.', $data['Nomenclature']['id']);
                array_pop($id_tab);
                if (!empty($id_tab))
                    $data['Nomenclature']['parent_id'] = implode('', $id_tab);
                $Nomenclature->save($data);
            }
        }
        return $classification;
    }

    /**
     * Récupération de l'acte tamponé
     */
    public function getTamponS2low($options) {
        $tdt_id = $options[0];
        $flux = $this->S2low->getActeTampon($tdt_id);
        return $flux;
    }

    /**
     * Récupération de l'acte tamponé
     */
    public function getTamponPastell($options) {
        $id_d = $options[0];
        $flux = $this->Pastell->getFile($this->id_e, $id_d, 'acte_tamponne');
        return $flux;
    }

    /**
     * Récupération du fichier bordereau
     */
    public function getBordereauPastell($options) {
        $id_d = $options[0];
        $flux = $this->Pastell->getFile($this->id_e, $id_d, 'bordereau');
        return $flux;
    }

    /**
     * Récupération du fichier bordereau
     */
    public function getBordereauS2low($options) {
        $tdt_id = $options[0];
        $flux = $this->S2low->getAR($tdt_id);
        return $flux;
    }
    
    /**
     * Récupération du fichier de message
     * 
     * @param int $message_id
     * @return String
     */
    public function getDocumentS2low($message_id) {
        return gzread($this->S2low->getDocument($message_id));
    }
    
    /**
     * Récupération du de l'ARacte
     * 
     * @param int $iTdt
     * @return boolean|String
     */
    public function getArActeS2low($iTdt) {

        $flux = $this->S2low->getFluxRetour($iTdt);
        $codeRetour = substr($flux, 3, 1);

        if ($codeRetour == 4) {
            return mb_substr($flux, strpos($flux, '<?xml version'), strlen($flux));
        }

        return false;
    }
    
    /**
     * Récupération du de l'ARacte
     * 
     * @param int $iTdt
     * @return boolean|String
     */
    public function getArActePastell($iTdt) {
        $id_d = $iTdt;
        $this->Pastell->action($this->id_e, $id_d, 'verif-tdt');
        $infos = $this->Pastell->detailDocument($this->id_e, $id_d);
        if (!empty($infos['data']['aractes']))
            return $infos['data']['aractes'];
        else
            return false;
    }
}