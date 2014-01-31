<?php

/**
 * @author Florian Ajir <florian.ajir@adullact.org>
 * @editor Adullact
 *
 * @created 21 janvier 2014
 *
 * Interface controllant les actions de signature électronique
 * Utilise les connecteurs/components PastellComponent et s2lowComponent
 *
 */
App::uses('ComponentCollection', 'Controller');
App::uses('Component', 'Controller');
App::uses('s2lowComponent', 'Controller/Component');
App::uses('PastellComponent', 'Controller/Component');
App::uses('Deliberation', 'Model');
App::uses('Collectivite', 'Model');
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
     * @var string type pastell
     */
    private $pastell_type;

    /**
     * @var int|string $id_e
     */
    private $id_e;

    /**
     * Appelée lors de l'initialisation de la librairie
     * Charge le bon protocol de signature et initialise le composant correspondant
     */
    public function __construct(){
        $collection = new ComponentCollection();
        $signature = Configure::read('USE_TDT');
        $protocol = Configure::read('TDT');

        if (!$signature)
            throw new Exception("Signature désactivée");
        if (empty($protocol))
            throw new Exception("Aucun parapheur désigné");


        if (Configure::read("USE_$protocol"))
            $this->connecteur = Configure::read('TDT');
        else
            throw new Exception("Le connecteur parapheur désigné n'est pas activé : USE_$protocol");

        if ($protocol == 'PASTELL'){
            $this->Pastell = new PastellComponent($collection);
            //Enregistrement de la collectivité (pour pastell)
            $Collectivite = new Collectivite();
            $Collectivite->id = 1;
            $this->id_e = $Collectivite->field('id_entity');
            $this->pastell_type = Configure::read('PASTELL_TYPE');
        }
        if ($protocol == 'S2LOW'){
            $this->S2low = new S2lowComponent($collection);
        }
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
    public function __call($name, $arguments)
    {
        $suffix = ucfirst(strtolower($this->connecteur));
        if (empty($arguments))
            return $this->{$name.$suffix}();
        else
            return $this->{$name.$suffix}($arguments);
    }

    /**
     *
     * FIXME : gérer le cas où le dossier n'existe pas dans Pastell
     * @param $options
     * [0] => $id_d,
     * @return bool|string
     */
    public function sendPastell($options){
        $id_d = $options[0];
        $classification = !empty($options[1]) ? $options[1] : null;
        $this->Pastell->envoiTdt($this->id_e, $id_d, $classification);
        return $this->Pastell->action($this->id_e, $id_d, 'send-tdt');
    }

    /**
     * @param $options
     * @return array(
     * 'action' => 'rejet-iparapheur',
     * 'message' => 'Le document a été rejeté dans le parapheur : 23/01/2014 16:54:52 : [RejetSignataire] vu',
     * 'date' => '2014-01-23 16:55:07'
     * )
     */
    public function getLastActionPastell($options){
        $id_d = $options[0];
        $get_details = !empty($options[1]);
        $details = $this->Pastell->detailDocument($this->id_e, $id_d);
        if ($get_details)
            return $details['last_action'];
        $last_action = $details['last_action']['action'];
        return $last_action;
    }
    public function getDetailsPastell($options){
        $id_d = $options[0];
        return $this->Pastell->detailDocument($this->id_e, $id_d);
    }

    /**
     * @link updateAll()
     * @return array
     */
    public function updateAllS2low(){
        return $this->Deliberation->majActesParapheur();
    }

    /**
     * @link updateAll()
     * @return array
     */
    public function updateAllPastell(){
        return $this->Deliberation->majSignaturesPastell();
    }

    public function listClassificationPastell(){
        $liste = array();
        App::uses('Nomenclature', 'Model');
        $Nomenclature = new Nomenclature();
        $categories = $Nomenclature->find('list', array('fields'=>array('id','libelle'), 'conditions'=>array('parent_id' => 0)));
        $nomenclatures = $Nomenclature->find('all', array('fields'=>array('id','libelle', 'parent_id'), 'conditions'=>array('parent_id <>' => 0)));
        foreach ($nomenclatures as $nom){
            $niveau = substr_count($nom['Nomenclature']['id'], '.');
            $liste[$nom['Nomenclature']['parent_id']][$nom['Nomenclature']['id']] = str_repeat('&nbsp;', $niveau*2) . $nom['Nomenclature']['id'] . ' ' . $nom['Nomenclature']['libelle'];
        }

        foreach ($liste as $titre => $array){
            ksort($array);
            $liste[$titre . ' ' . $categories[$titre]] = $array;
            unset($liste[$titre]);
        }
        ksort($liste);
        return $liste;
//        return $Nomenclature->generateTreeList(array(), 'Nomenclature.code', 'Nomenclature.libelle', ' ');
    }

    public function listClassificationS2low(){

    }

    public function updateClassificationS2low(){
        return $this->S2low->getClassification();
    }

    public function updateClassificationPastell(){
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

        foreach ($classification as $class => $actif){
           if ($actif){
               $data = array();
               //Découpe la chaine par espace
               $str_tab = explode(' ', $class);
               //Premier élément : code
               $data['Nomenclature']['id'] = array_shift($str_tab);
               $data['Nomenclature']['libelle'] = implode(' ', $str_tab);
               $id_tab = explode('.', $data['Nomenclature']['id']);
//               $data['Nomenclature']['id'] = str_replace('.', '', $data['Nomenclature']['code']);
               array_pop($id_tab);
               if (!empty($id_tab))
                   $data['Nomenclature']['parent_id'] = implode('', $id_tab);
               $Nomenclature->save($data);
           }
        }
        return $classification;
    }
}
