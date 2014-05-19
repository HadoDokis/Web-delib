<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
App::uses('ComponentCollection', 'Controller');
App::uses('Component', 'Controller');
App::uses('PastellComponent', 'Controller/Component');
App::uses('Deliberation', 'Model');
App::uses('Collectivite', 'Model');
/**
 * Description of Connecteur
 *
 * @author splaza
 */
class Connecteur {
    
    /**
     * @var string Protocole de signature (pastell|iparapheur)
     */
    private $connecteur;
    
    /**
     * @var bool Indique si le connecteur est activé
     */
    protected $active=true;

    /**
     * @var Composant PastellComponent
     */
    protected $Pastell;
    
    /**
     * @var string type pastell
     */
    protected $pastell_type;
    
    /** 
     * @var int|string $id_e
     */
    protected $id_e;
    
    /**
     * @var Model Deliberation
     */
    protected $Deliberation;

    /**
     * @var int|string collectivite
     */
    protected $Collectivite;
    
    /**
     * @var Model Deliberation
     */
    protected $collection;
    
    /**
     * @var array configuration
     */
    protected $config;
    
    function __construct($connecteur) {
        $this->collection = new ComponentCollection();

        if(!empty($connecteur))
            $this->connecteur = $connecteur;
        else $this->connecteur = NULL;
        
        if ($this->connecteur == 'PASTELL') {
                $this->Pastell = new PastellComponent($collection);
                //Enregistrement de la collectivité (pour pastell)
                $Collectivite = new Collectivite();
                $Collectivite->id = 1;
                $this->id_e = $Collectivite->field('id_entity');
                
                $this->pastell_type = Configure::read('PASTELL_TYPE');
                $pastell_config = Configure::read('Pastell');
                $this->config = $pastell_config[$this->pastell_type];
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
     * @throws Exception
     */
    public function __call($name, $arguments) {
        if(!empty($this->connecteur) && $this->active)
            $suffix = ucfirst(strtolower($this->connecteur));
        else return NULL; //Retoun NULL pour les appels de fonction sans connecteur

        if (method_exists($this, $name . $suffix))
            return call_user_func_array(array($this, $name . $suffix), $arguments);
        else {
            throw new Exception(sprintf('The required method "%s" does not exist for %s', $name . $suffix, get_class()));
        }
    }
    
    public function getType()
    {
      return !empty($this->connecteur)?$this->connecteur:false;   
    }
    /**
     * @return array
     */
    public function listClassification() {
        if($this->connecteur!='PASTELL') return NULL;
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
    
     /**
     * @param array $acte
     * @param array $annexes (content, filename)
     * @return bool|int false si echec sinon identifiant pastell
     */
     protected function createPastell($acte, $document = null, $annexes = array()) {
        
        $this->Deliberation->id = $acte['Deliberation']['id'];
        $id_d = $this->Pastell->createDocument($this->id_e, $this->pastell_type);
        
        try {
            $this->Pastell->editTransmission($this->id_e, $id_d);
            if ($this->Pastell->modifDocument($this->id_e, $id_d, $acte, $acte['Deliberation']['delib_pdf'], $annexes)) {
                $this->Deliberation->saveField('pastell_id', $id_d);
                return $id_d;
            } 
            else
            {
                throw new Exception();
            }
        } catch (Exception $e) {
            $this->Pastell->action($this->id_e, $id_d, $this->config['action']['supression']);
            $this->log($e->getMessage(), 'error');
            return false;
        }
    }
}
