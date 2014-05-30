<?php
/**
 * @author Sébatien Plaza <sebastien.plaza@adullact-projet.coop>
 * @company Adullact
 *
 * @created 7 mai 2014
 *
 * Interface controllant les actions de versment SAE
 * Utilise les connecteurs/components PastellComponent et AsalaeComponent
 *
 */
App::uses('AppTools', 'Lib');
App::uses('Connecteur', 'Lib');
/**
 * Class Sae
 * @package App.Lib.Sae
 */
class Sae extends Connecteur {

    /**
     * @var Component AsalaeComponent
     */
    private $Asalae;


    /**
     * Appelée lors de l'initialisation de la librairie
     * Charge le bon protocol de signature et initialise le composant correspondant
     */
    public function __construct() {
        parent::__construct(Configure::read('SAE'));

        if (!Configure::read('USE_SAE'))
            throw new Exception("SAE désactivée");

        if (!$this->getType())
            throw new Exception("Aucun SAE désigné");

        if (Configure::read('USE_'.$this->getType()))
            $this->connecteur = Configure::read('SAE');
        else
            throw new Exception("Le connecteur SAE désigné n'est pas activé : USE_".$this->getType());

        if ($this->getType() == 'ASALAE') {
            throw new Exception("Le connecteur ASALAE n\'est pas encore opérationnel");
            //$this->S2low = new S2lowComponent($collection);
        }
    }

     /**
     * Envoi le dossier dans pastell au SAE
     * @param $id_d
     * @param null $classification
     * @return array
     */
    public function sendPastell($acte, $document = null, $annexes = array()) {
        
        if(empty($acte['Deliberation']['pastell_id']))
            $acte['Deliberation']['pastell_id']=  parent::createPastell($acte, $document = null, $annexes = array());
        //$this->Pastell->envoiTdt($this->id_e, $id_d, $classification);
        //$acte['Deliberation']['num_pref']
        
        $result = $this->Pastell->action($this->id_e, $acte['Deliberation']['pastell_id'], $this->config['action']['send-archive']);     
//Récupération du tdt_id
//        $infos = $this->Pastell->detailDocument($this->id_e, $acte['Deliberation']['pastell_id']);
//        if (!empty($infos['data']['tedetis_transaction_id'])) {
//            $this->Deliberation->id = $acte['Deliberation']['id'];
//            $this->Deliberation->saveField('tdt_id', $infos['data']['tedetis_transaction_id']);
//        }sae_etat
        return $result;
    }
}