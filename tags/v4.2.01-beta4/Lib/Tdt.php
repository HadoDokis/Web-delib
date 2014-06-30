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
App::uses('S2lowComponent', 'Controller/Component');
App::uses('TdtMessage', 'Model');
App::uses('AppTools', 'Lib');
App::uses('ConnecteurLib', 'Lib');

/**
 * Class Tdt
 * @package App.Lib.Tdt
 */
class Tdt extends ConnecteurLib {

    /**
     * @var Component s2lowComponent
     */
    private $S2low;

    /**
     * @var Model TdtMessage
     */
    private $TdtMessage;

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
        parent::__construct(Configure::read('TDT'));

        if (!Configure::read('USE_TDT'))
            throw new Exception("TDT désactivée");
        if (!$this->getType())
            throw new Exception("Aucun TDT désigné");

        if (Configure::read("USE_".$this->getType()))
            $this->connecteur = Configure::read('TDT');
        else
            throw new Exception("Le connecteur tdt désigné n'est pas activé : USE_".$this->getType());

        if ($this->getType() == 'S2LOW') {
            $this->S2low = new S2lowComponent($this->collection);
        }
        $this->TdtMessage = new TdtMessage;
        $this->Deliberation = new Deliberation;
    }

    /**
     * Envoi le dossier dans pastell au TDT
     * @param $id_d
     * @param null $classification
     * @return array
     */
    public function sendPastell($acte, $document = null, $annexes = array()) {
        
        if(empty($acte['Deliberation']['pastell_id']))
            $acte['Deliberation']['pastell_id']=  parent::createPastell($acte, $document = null, $annexes = array());
        //$this->Pastell->envoiTdt($this->id_e, $id_d, $classification);
        //$acte['Deliberation']['num_pref']
        
        $result = $this->Pastell->action($this->id_e, $acte['Deliberation']['pastell_id'], $this->config['action']['send-tdt']);
        //Récupération du tdt_id
        $infos = $this->Pastell->detailDocument($this->id_e, $acte['Deliberation']['pastell_id']);
        if (!empty($infos['data']['tedetis_transaction_id'])) {
            $this->Deliberation->id = $acte['Deliberation']['id'];
            $this->Deliberation->saveField('tdt_id', $infos['data']['tedetis_transaction_id']);
        }
        return $result;
    }

    /**
     * TODO
     */
    public function sendS2low() {

    }

    /**
     * @param int $id_d
     * @param bool $get_details récupérer le message et la date
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
     * @return array
     */
    public function getDetailsPastell($id_d) {
        return $this->Pastell->detailDocument($this->id_e, $id_d);
    }

    /**
     * @param int $id_d
     * @param bool $all
     * @return array|bool
     */
    public function getReponsesPastell($acte) {
        $id_d=$acte['Deliberation']['pastell_id'];
        if(empty($id_d))return false;
        $tdt_messages = array();
        $this->Pastell->action($this->id_e, $id_d, $this->config['action']['verif-reponse-tdt']);
        $infos = $this->Pastell->detailDocument($this->id_e, $id_d);
        if (empty($infos['data']))
            return false;

        foreach ($this->echanges as $type => $echange){
            if (!empty($infos['data']['has_'.$echange])){
                $demande=$this->TdtMessage->findByTdtId($infos['data'][$echange.'_id']);
                
                if (empty($demande))
                    $tdt_messages[] = array(
                            'type' => $type,
                            'id' => $infos['data'][$echange.'_id'],
                            'data' => $this->Pastell->getFile($this->id_e, $id_d, $echange),
                            'date_message' => $infos['data'][$echange.'_date']
                    );
                if (!empty($infos['data'][$echange.'_response_transaction_id'])){
                $reponse=$this->TdtMessage->findByTdtId($infos['data'][$echange.'_response_transaction_id']);
                if (empty($reponse))
                    $tdt_messages[] = array(
                            'type' => $type,
                            'id' => $infos['data'][$echange.'_response_transaction_id'],
                            'data' => $this->Pastell->getFile($this->id_e, $id_d, 'reponse_'.$echange),
                            'date_message' => NULL // Pas de date retour de patell
                    );
                }
            }
        }

        return $tdt_messages;
    }

    /**
     * @param int $tdt_id
     * @return mixed
     */
    public function getReponsesS2low($acte) {
        $tdt_id=$acte['Deliberation']['tdt_id'];
        $tdt_messages = array();
        $result = $this->S2low->getNewFlux($tdt_id);
		
        if (!empty($result)){
            $result_array = explode("\n", trim($result));
            if ($result_array[0] != 'KO'){
                foreach ($result_array as $line) {
                    if (!empty($result)) {
                        list($type, $status, $id) = explode("-",$line);
                        $tdt_messages[] = array('type'=>$type,
                                                'status'=>$status,
                                                'id'=>$id,
                                                'data'=>$this->S2low->getDocument($id));
                    }
                }
            }
        }
        
        return $tdt_messages;
    }

    public function getDateArPastell($acte) {
        $id_d=$acte['Deliberation']['pastell_id'];
        $this->Pastell->action($this->id_e, $id_d, $this->config['action']['verif-tdt']);
        $infos = $this->Pastell->detailDocument($this->id_e, $id_d);
        if (!empty($infos['data']['date_ar']))
            return $infos['data']['date_ar'];////$this->Date->frenchDate(strtotime($date))
        else
            return false;
    }

    /**
     * @param int $tdt_id
     * @return bool
     */
    public function getDateArS2low($acte) {
        $tdt_id=$acte['Deliberation']['tdt_id'];
        $flux = $this->S2low->getFluxRetour($tdt_id);
        $codeRetour = substr($flux, 3, 1);

        if ($codeRetour == 4) {
            $dateAR = $this->S2low->getDateAR($res = mb_substr($flux, strpos($flux, '<actes:ARActe'), strlen($flux)));
            return $dateAR;
        }
        return false;
    }

    public function updateClassificationS2low() {
        return $this->S2low->getClassification();
    }

    public function updateClassificationPastell() {
        //Création d'un dossier temporaire
        $id_d = $this->Pastell->createDocument($this->id_e);
        //Récupération de la classification
        $classification = $this->Pastell->getClassification($this->id_e, $id_d);
        //Suppression du dossier temporaire
        //$this->Pastell->delete($this->id_e, $id_d);

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
     * Récupération de l'acte tamponé au format pdf
     */
    public function getTamponS2low($acte) {
        $tdt_id=$acte['Deliberation']['tdt_id'];
        $flux = $this->S2low->getActeTampon($tdt_id);
        $infoContent=AppTools::FileMime($flux);
        if(!empty($infoContent['mimetype']) && $infoContent['mimetype']=='application/pdf')
            return $flux;
        
        return NULL;
    }

    /**
     * Récupération de l'acte tamponé au format pdf
     */
    public function getTamponPastell($acte) {
        $id_d=$acte['Deliberation']['pastell_id'];
        if(empty($id_d))return false;
        $infos = $this->Pastell->detailDocument($this->id_e, $id_d);
        if (!empty($infos['data']['acte_tamponne']))
            return $this->Pastell->getFile($this->id_e, $id_d, $this->config['field']['acte_tamponne']);
        else
            return false;
    }

    /**
     * Récupération du fichier bordereau
     */
    public function getBordereauPastell($acte) {
        $id_d=$acte['Deliberation']['pastell_id'];
        if(empty($id_d))return false;
        $infos = $this->Pastell->detailDocument($this->id_e, $id_d);
        if (!empty($infos['data']['bordereau']))
            return $this->Pastell->getFile($this->id_e, $id_d, $this->config['field']['bordereau']);
        else
            return false;
    }

    /**
     * Récupération du fichier bordereau
     * @param int $tdt_id
     * @return mixed
     */
    public function getBordereauS2low($acte) {
        $tdt_id=$acte['Deliberation']['tdt_id'];
        return $this->S2low->getAR($tdt_id);
    }
    
    /** TODO FIXME
     * Récupération du fichier de message
     * 
     * @param int $message_id
     * @return String
     */
    public function getDocumentS2low($message_id) {
        return $this->S2low->getDocument($message_id);
    }
    
    /**
     * Récupération du de l'ARacte
     * 
     * @param int $iTdt
     * @return boolean|String
     */
    public function getArActeS2low($acte) {
        $iTdt=$acte['Deliberation']['tdt_id'];
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
    public function getArActePastell($acte) {
        $id_d=$acte['Deliberation']['pastell_id'];
        if(empty($id_d))return false;
        $infos = $this->Pastell->detailDocument($this->id_e, $id_d);
        if (!empty($infos['data']['aractes']))
            return $this->Pastell->getFile($this->id_e, $id_d, $this->config['field']['aractes']);
        else
            return false;
    }

    public function getDateClassificationS2low(){
        return $this->S2low->getDateClassification();
    }

    /**
     * Date de la derniere mise à jour de la classification
     * @return string
     */
    public function getDateClassificationPastell(){
        App::uses('Nomenclature', 'Model');
        $Nomenclature = new Nomenclature();
        $nomenc = $Nomenclature->find('first', array(
            'recursive' => -1,
            'fields' => array('modified'),
            'order' => 'modified DESC'
        ));
        if (!empty($nomenc)){
            setlocale(LC_ALL, 'fr_FR.utf8');
//            return date("d/m/Y", strtotime($nomenc['Nomenclature']['modified']));
            return strftime("%A %d %B %Y", strtotime($nomenc['Nomenclature']['modified']));
        }

    }
}