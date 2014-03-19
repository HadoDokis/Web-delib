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
     * Appelée lors de l'initialisation de la librairie
     * Charge le bon protocol de signature et initialise le composant correspondant
     */
    public function __construct(){
        $collection = new ComponentCollection();
        $signature = Configure::read('USE_PARAPHEUR');
        $protocol = Configure::read('PARAPHEUR');

        if (!$signature)
            throw new Exception("La fonctionnalité de signature est désactivée");
        if (empty($protocol))
            throw new Exception("Aucun parapheur désigné");


        if (Configure::read("USE_$protocol"))
            $this->connecteur = Configure::read('PARAPHEUR');
        else
            throw new Exception("Le connecteur parapheur désigné n'est pas activé : USE_$protocol");

        if ($protocol == 'PASTELL'){
            $this->Pastell = new PastellComponent($collection);
            //Enregistrement de la collectivité (pour pastell)
            $Collectivite = new Collectivite();
            $Collectivite->id = 1;
            $this->collectivite = $Collectivite->field('id_entity');
            $this->pastell_type = Configure::read('PASTELL_TYPE');
        }
        if ($protocol == 'IPARAPHEUR'){
            $this->Iparapheur = new IparapheurComponent($collection);
            $this->visibility = Configure::read('IPARAPHEUR_VISIBILITY');
        }
        $this->Deliberation = new Deliberation;
        $this->parapheur_type = Configure::read('IPARAPHEUR_TYPE');

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
    public function __call($name, $arguments)
    {
        $suffix = ucfirst(strtolower($this->connecteur));
        
        if (method_exists($this, $name.$suffix))
                return call_user_func_array(array($this, $name.$suffix), $arguments);
        else{
          throw new Exception(sprintf('The required method "%s" does not exist for %s', $name.$suffix, get_class($this)));
        } 
    }

    /**
     * @param array $delib
     * @param int|string $circuit_id
     * @param string $document contenu du fichier du document principal
     * @param array $annexes (content, filename, mimetype)
     * @return bool|int false si echec sinon identifiant iparapheur
     */
    public function sendIparapheur($delib, $circuit_id, $document, $annexes=array()){
        
        if (is_numeric($circuit_id)){
            $circuits = $this->listCircuitsIparapheur();
            $libelleSousType = $circuits[$circuit_id];
        }else{
            $libelleSousType = $circuit_id;
        }
        $targetName = $delib['Deliberation']['objet'];
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
            $this->log($ret['messageretour']['message'], 'parapheur');
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
    public function sendPastell($delib, $circuit_id, $document, $annexes=array()){
        $id_d = $this->Pastell->createDocument($this->collectivite, $this->pastell_type);
        $res = $this->Pastell->modifDocument($this->collectivite, $id_d, $delib, $document, $annexes);
        if ($res == 1) {
            if (is_numeric($circuit_id)){
                $circuits = $this->Pastell->getInfosField($this->collectivite, $id_d, 'iparapheur_sous_type');
                $sousType = $circuits[$circuit_id];
            }else{
                $sousType = $circuit_id;
            }
            $this->Pastell->selectCircuit($this->collectivite, $id_d, $sousType);
            $this->Pastell->envoiSignature($this->collectivite, $id_d);
            $this->Pastell->action($this->collectivite, $id_d, 'send-iparapheur');
            return $id_d;
        } else {
            $this->Pastell->action($this->collectivite, $id_d, "supression");
            return false;
        }
    }

    public function getEtatIparapheur($options){}

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
        $details = $this->Pastell->detailDocument($this->collectivite, $id_d);
        if ($get_details)
            return $details['last_action'];
        $last_action = $details['last_action']['action'];
        return $last_action;
    }

    public function getBordereauPastell($options){
        $id_d = $options[0];
        $bordereau = $this->Pastell->getFile($this->collectivite, $id_d, 'document_signe');
        return $bordereau;
    }

    public function getSignaturePastell($options){
        $id_d = $options[0];
        return $this->Pastell->getFile($this->collectivite, $id_d, 'signature');
    }

    public function getDetailsPastell($options){
        $id_d = $options[0];
        return $this->Pastell->detailDocument($this->collectivite, $id_d);
    }

    public function isSignePastell($options){
        $id_d = $options[0];
        $details = $this->Pastell->detailDocument($this->collectivite, $id_d);
        return !empty($details['data']['has_signature']);
    }

    public function deleteIparapheur($options){
        $id = $options[0];
        $this->Iparapheur->archiverDossierWebservice($id, 'EFFACER');
        $this->Iparapheur->effacerDossierRejeteWebservice($id);
    }
    public function archiveIparapheur($options){

    }

    public function deletePastell($options){
        $id_d = $options[0];
        return $this->Pastell->action($this->collectivite, $id_d, "supression");
    }

    /**
     * @link listCircuits()
     * @return array
     * @throws Exception
     */
    public function listCircuitsIparapheur(){
        $resp = $this->Iparapheur->getListeSousTypesWebservice($this->parapheur_type);
        if (array_key_exists('soustype', $resp))
            return $resp['soustype'];
        else{
            throw new Exception($resp['messageretour']['message']);
        }
    }

    /**
     * @link listCircuits()
     * @return array
     */
    public function listCircuitsPastell(){
        return $this->Pastell->getCircuits($this->collectivite);
    }

    public function printCircuits(){
        $circuits = array('Standard' => array('-1' => 'Signature manuscrite'));
        try{
            $circuits_parapheur = $this->listCircuits();
            if (!empty($circuits_parapheur))
                $circuits['Parapheur'] = $circuits_parapheur;
        }catch(Exception $e){} //Si erreur de connexion au parapheur

        return $circuits;
    }

    /**
     * @link updateAll()
     * @return array
     */
    public function updateAllIparapheur(){
        return $this->Deliberation->majActesParapheur();
    }

    /**
     * @link updateAll()
     * @return array
     */
    public function updateAllPastell(){
        return $this->Deliberation->majSignaturesPastell();
    }
}