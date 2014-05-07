<?php
/**
 * Connecteur Pastell
 * v1.3 : ok
 *
 * Author : Florian Ajir <florian.ajir@adullact.org>
 * Date : 16/01/2014
 *
 * PHP 5.3
 *
 * @package app.Controller.Component
 * @license CeCiLL V2 (http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html)
 */

/**
 * Class PastellComponent
 */
App::uses('Component', 'Controller');
App::uses('ComponentCollection', 'Controller');
App::uses('SessionComponent', 'Controller/Component');
App::uses('AppTools', 'Lib');
App::uses('File', 'Utility');
App::uses('Folder', 'Utility');

class PastellComponent extends Component {
//    public $components = array('Session');

    private $host;
    private $login;
    private $pwd;
    private $parapheur_type;
    private $pastell_type;
    private $Session;
    private $config;
    public $reponse;

    function __construct() {
        $collection = new ComponentCollection();
        $this->Session = new SessionComponent($collection);
        $this->host = Configure::read('PASTELL_HOST');
        $this->login = Configure::read('PASTELL_LOGIN');
        $this->pwd = Configure::read('PASTELL_PWD');
        $this->pastell_type = Configure::read('PASTELL_TYPE');
        $this->parapheur_type = Configure::read('IPARAPHEUR_TYPE');
        //clés de champs
        $pastell_config = Configure::read('Pastell');
        $this->config = $pastell_config[$this->pastell_type];
        $this->reponse = null;
    }

    /**
     * Retourne la liste des circuits du PASTELL
     * La liste est enregistrée en Session
     * pour économiser du traffic réseau entre WD et Pastell lors des appels suivants
     *
     * @param int $id_e identifiant de la collectivité
     * @return array
     */
    public function getCircuits($id_e) {
        if (!$this->Session->check('user.Pastell.circuits')) {
            $tmp_id_d = $this->createDocument($id_e);
            $circuits = $this->getInfosField($id_e, $tmp_id_d, 'iparapheur_sous_type');
            if (!empty($circuits))
                $this->Session->write('user.Pastell.circuits', $circuits);
            $this->action($id_e, $tmp_id_d, 'supression');
        } else {
            $circuits = $this->Session->read('user.Pastell.circuits');
        }
        return $circuits;
    }

    /**
     * @param string $page script php
     * @param array $data
     * @param bool $file_transfert attente d'un fichier en retour ?
     * @return bool|mixed|string retour du webservice
     */
    public function execute($page, $data = array(), $file_transfert = false) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_USERPWD, $this->login . ":" . $this->pwd);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $api = $this->host . "/api/$page";
        curl_setopt($curl, CURLOPT_URL, $api);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, TRUE);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        if ($file_transfert) {
            $folder = new Folder(AppTools::newTmpDir(TMP . 'files' . DS), true, 0777);
            $file = new File($folder->path . DS . 'WD_PASTELL_DOC', true, 0777);
            $fp = fopen($file->path, 'w');
            curl_setopt($curl, CURLOPT_FILE, $fp);
            $response = curl_exec($curl);
            curl_close($curl);
            fclose($fp);
            $content = $file->read();
            $folder->delete();
        }else{
            $response = curl_exec($curl);
            curl_close($curl);
        }
        $result = json_decode($response, true);
        $ok = $this->_log($page, $data, $result);
        if ($ok){
            if ($file_transfert){
                // Test si le retour est un fichier ou un message json (erreur)
                return json_decode($content) == null ? $content : false;
            }
            else
                return $result;
        }
        else
            return false;
    }

    /**
     * Fonction de log des réponses pastell
     * @param string $page script ws
     * @param array $data paramètres
     * @param array $retourWS reponse ws
     * @return bool false si en erreur
     */
    protected function _log($page, $data, $retourWS) {
        $this->log('Request : ' . $page, 'pastell');
        if (!empty($data)) {
            $this->log('Data : ' . print_r($data, true), 'pastell');
        }
        if (empty($retourWS)) {
            $this->log('Error : Aucune réponse du serveur distant', 'pastell');
            return false;
        }
        if (!empty($retourWS['message'])) {
            $this->reponse = 'Message : ' . $retourWS['message'];
            $this->log($this->reponse, 'pastell');
        }
        if (!empty($retourWS['error-message'])) {
            $this->reponse = 'Error : ' . $retourWS['error-message'];
            $this->log($this->reponse, 'pastell');
            return false;
        }
        return true;
    }

    /**
     * Converti un tableau d'arguments en une chaine pour envoi en méthode $_GET (args par url)
     *
     * @param array $params tableau de paramètres
     * @return string chaine de paramètres
     */
    protected function _paramsArray2String($params = array()) {
        $target = '';
        foreach ($params as $opt => $val) {
            if (!empty($val))
                $target .= $opt . '=' . urlencode($val) . '&';
        }
        return substr($target, 0, strlen($target) - 1);
    }

    /**
     * Permet d'obtenir la version de la plateforme. Pastell assure une compatibilité ascendante entre les différents numéro de révision.
     * @return array(
     *      version => Numéro de version commerciale,
     *      revision => Numéro de révision du dépôt de source officiel de Pastell  (https://adullact.net/scm/viewvc.php/?root=pastell),
     *      version_complete => Version affiché sur la l'interface web de la plateforme
     * )
     */
    public function getVersion() {
        return $this->execute('version.php');
    }

    /**
     * Types de document supportés par la plateforme
     *
     * Liste l'ensemble des flux (types de documents) disponible sur Pastell
     * comme par exemple, les actes, les mails sécurisées, les flux citoyen, etc...
     *
     * @return array(type* => array(id => nom))
     * type : Groupe de type de document (exemple : Flux généraux)
     * id : Identifiant du type
     * nom : Nom à afficher pour l'utilisateur (exemple : Actes, Message du centre de gestion)
     */
    public function getDocumentTypes() {
        $documents = array();
        $natures = $this->execute('document-type.php');
        foreach ($natures as $id => $nature) {
            $documents[$nature['type']][$id] = $nature['nom'];
        }
        return $documents;
    }

    /**
     * Information sur un type de document
     *
     * Liste l'ensemble des champs d'un type document ainsi que les informations sur chaque champs
     * (type de champs, valeur par défaut, script de choix, ...)
     *
     * @param string $type Type de document (retourné par document-type.php). Exemple : actes, mailsec, ...
     * @return array propriete * : Couple clé/valeur de la propriété
     */
    public function getDocumentTypeInfos($type) {
        return $this->execute("document-type-info.php?type=$type");
    }

    /**
     * Actions possible sur un type de document
     *
     * Ramène la liste des statuts/actions possibles sur ce type de document ainsi que des infos relatives à ce type de document
     *
     * @param string $type Type de document (retourné par document-type.php)
     * @return array propriete * : Couple clé/valeur de la propriété
     */
    public function getDocumentTypeActions($type) {

        return $this->execute("document-type-action.php?type=$type");
    }

    /**
     * Listes des entités
     *
     * Liste l'ensemble des entités sur lesquelles l'utilisateur a des droits. Liste également les entités filles.
     *
     * @param bool $details si on veut afficher toutes les infos des entités
     * si $details :
     * @return array( array*(
     *  id_e => Identifiant numérique de l'entité
     *  denomination => Libellé de l'entité (ex : Saint-Amand-les-Eaux)
     *  siren => Numéro SIREN de l'entité (si c'est une collectivité ou un centre de gestion)
     *  centre_de_gestion => Identifiant numérique (id_e) du CDG de la collectivité
     *  entite_mere => Identifiant numérique (id_e) de l'entité mère de l'entité (par exemple pour un service)
     * ))
     * sinon
     * @return array( id_e => denomination )
     */
    public function listEntities($details = false) {
        $entities = $this->execute('list-entite.php');
        if ($details)
            return $entities;
        else {
            $collectivites = array();
            foreach ($entities as $entity) {
                $collectivites[$entity['id_e']] = $entity['denomination'];
            }
            return $collectivites;
        }
    }

    /**
     * Listes de documents d'une collectivité
     *
     * Liste l'ensemble des documents d'une entité Liste également les entités filles.
     *
     * @param int $id_e Identifiant de l'entité (retourné par list-entite)
     * @param string $type Type de document (retourné par document-type)
     * @param integer $offset (facultatif, 0 par défaut) Numéro de la première ligne à retourner
     * @param integer $limit (facultatif, 100 par défaut) Nombre maximum de lignes à retourner
     * @return array (
     *                  id_e => Identifiant numérique de l'entité (identique à l'entrée),
     *                  id_d => Identifiant unique du document,
     *                  role => Rôle de l'entité sur le document (exemple : éditeur),
     *                  last-action => Dernière action effectuée sur le document,
     *                  last_action_date => Date de la dernière action,
     *                  type => Type de document (identique à l'entrée),
     *                  creation => Date de création du document,
     *                  modification => Date de dernière modification du document,
     *                  entite * => Liste des identifiant (id_e) des autres entités qui ont des droits sur ce document
     * )
     */
    public function listDocuments($id_e, $type, $offset = 0, $limit = 100) {
        return $this->execute("list-document.php?id_e=$id_e&type=$type&offset=$offset&limit=$limit");
    }

    /**
     * Recherche multi-critère dans la liste des documents
     *
     * @param array $options
     * @return array
     * Format de sortie :
     * [] => {
     *  id_e =>             Identifiant numérique de l'entité
     *  id_d =>             Identifiant unique du document
     *  role =>             Rôle de l'entité sur le document (exemple : éditeur)
     *  last-action =>      Dernière action effectuée sur le document
     *  last_action_date => Date de la dernière action
     *  type =>             Type de document (identique à l'entrée)
     *  creation =>         Date de création du document
     *  modification =>     Date de dernière modification du document
     *  entite * =>         Liste des identifiant (id_e) des autres entités qui ont des droits sur ce document.
     * }
     */
    public function rechercheDocument($options = array()) {
        $default_options = array(
            'id_e' => null, //Identifiant de l'entité (retourné par list-entite)
            'type' => null, //Type de document (retourné par document-type.php)
            'offset' => null, //numéro de la première ligne à retourner
            'lastetat' => null, //Dernier état du document
            'last_state_begin' => null, //date du passage au dernier état du document le plus ancien(date iso)
            'last_state_end' => null, //date du passage au dernier état du document le plus récent(date iso)
            'etatTransit' => null, //le document doit être passé dans cet état
            'state_begin' => null, //date d'entrée la plus ancienne de l'état etatTransit
            'state_end' => null, //date d'entrée la plus récente de l'état etatTransit
            'tri' => null, //critère de tri parmi last_action_date, title et entite
            'search' => null, //l'objet du document doit contenir la chaine indiquée
        );
        $options = array_merge($default_options, $options);
        return $this->execute('list-document.php?' . $this->_paramsArray2String($options));
    }

    /**
     * Détail sur un document
     *
     * Récupère l'ensemble des informations sur un document Liste également les entités filles.
     *
     * @param int $id_e Identifiant de l'entité (retourné par list-entite)
     * @param int $id_d Identifiant unique du document (retourné par list-document)
     * @return array
     * Format de sortie :
     * [] => {
     *  info => Reprend les informations disponible sur list-document.php
     *  data => Données issue du formulaire (voir document-type-info.php pour savoir ce qu'il est possible de récupérer)
     *  action_possible * => Liste des actions possible (exemple : modification, envoie-tdt, ...)
     * }
     */
    public function detailDocument($id_e, $id_d) {
        $acte = array(
            'id_e' => $id_e,
            'id_d' => $id_d
        );
        return $this->execute('detail-document.php', $acte);
    }

    /**
     * Détail sur plusieurs documents
     *
     * Récupère l'ensemble des informations sur un document Liste également les entités filles.
     *
     * @param int $id_e Identifiant de l'entité (retourné par list-entite)
     * @param array $id_d Tableau d'identifiants uniques de documents (retourné par list-document)
     * @return array
     * Format de sortie :
     * [] => {
     *  info => Reprend les informations disponible sur list-document.php
     *  data => Données issue du formulaire (voir document-type-info.php pour savoir ce qu'il est possible de récupérer)
     *  action_possible * => Liste des actions possible (exemple : modification, envoie-tdt, ...)
     * }
     */
    public function detailDocuments($id_e, $id_d) {
        $args = "id_e=$id_e";
        foreach ($id_d as $d) $args .= "&id_d=$d";
        return $this->execute('detail-several-document.php?' . $args);
    }

    /**
     * @param int $id_e identifiant de la collectivité
     * @param string $type type de flux (pastell)
     * @return integer id_d Identifiant unique du document crée.
     * @throws Exception Si erreur lors de la création
     */
    public function createDocument($id_e, $type = null) {
        if ($type == null)
            $type = $this->pastell_type;
        $infos = $this->execute("create-document.php?id_e=$id_e&type=$type");
        if (empty($infos))
            throw new Exception("Echec de connexion avec le serveur Pastell");
        if (array_key_exists('id_d', $infos))
            return ($infos['id_d']);
        elseif (array_key_exists('error-message', $infos))
            throw new Exception($infos['error-message']);
        else
            throw new Exception("Une erreur inconnue est survenue");
    }

    /**
     * FIXME : 'error-message' => 'Type action-possible introuvable'
     *
     * Récupération des choix possibles pour un champs spécial du document : external-data
     *
     * Récupère les valeurs possible d'un champs.
     * En effet, certaine valeur sont « externe » a Pastell : classification Actes, classification CDG, etc..
     * Ce script permet de récupérer l'ensemble de ces valeurs.
     * Ce script est utilisable sur tous les champs qui dispose d'une propriétés « controler »
     *
     * @param int $id_e identifiant de la collectivité
     * @param int $id_d identifiant du dossier pastell
     * @param string $field le nom d'un champ du document
     * @return array
     * valeur_possible * => Information supplémentaire sur la valeur possible (éventuellement sous forme de tableau associatif)
     */
    public function getInfosField($id_e, $id_d, $field) {
        return $this->execute("external-data.php?id_e=$id_e&id_d=$id_d&field=$field");
    }

    /**
     * Modification d'un document
     * @param int $id_e identifiant de la collectivité
     * @param int $id_d identifiant du dossier pastell
     * @param array $delib
     * @param string $document (Pdf)
     * @param array $annexes
     * @return bool|string
     * result : ok - si l'enregistrement s'est bien déroulé
     * formulaire_ok : 1 si le formulaire est valide, 0 sinon
     * message : Message complémentaire
     *
     * A noter que pour connaître la liste et les intitulés exacts des champs modifiables,
     * il convient d'utiliser la fonction document-type-info.php, en lui précisant le type concerné.
     */
    public function modifDocument($id_e, $id_d, $delib, $document, $annexes = array()) {
        if (empty($delib) || empty($delib['Typeacte']['nature_id']) || empty($delib['Deliberation']['objet_delib']))
            return false;

        $folder = new Folder(AppTools::newTmpDir(TMP . 'files' . DS), true, 0777);
        $file = new File($folder->path . DS . 'arrete.pdf', true, 0777);
        $file->write($document);
        $fields = $this->config['field'];
        $acte = array(
            'id_e' => $id_e,
            'id_d' => $id_d,
            'classification' => !empty($delib['Deliberation']['num_pref']) ? $delib['Deliberation']['num_pref'] : '',
            $fields['objet'] => utf8_decode($delib['Deliberation']['objet_delib']),
            $fields['arrete'] => '@' . $file->path,
            $fields['acte_nature'] => $delib['Typeacte']['nature_id'],
            $fields['numero_de_lacte'] => !empty($delib['Deliberation']['num_delib']) ? $delib['Deliberation']['num_delib'] : $delib['Deliberation']['id'],
            $fields['date_de_lacte'] => !empty($delib['Seance']['date']) ? $delib['Seance']['date'] : date("Y-m-d H:i:s"),
            $fields['classification'] => !empty($delib['Deliberation']['num_pref']) ? $delib['Deliberation']['num_pref'] : ''

        );

        $success = $this->execute('modif-document.php', $acte);

        if (!empty($success) && !empty($annexes))
            foreach ($annexes as $annex) {
                $annexeFile = new File($folder->path . DS . $annex['filename'], true, 0777);
                $annexeFile->write($annex['content']);
                $success &= $this->sendAnnex($id_e, $id_d, $annexeFile->path);
            }

        $folder->delete();

        return $success;
    }

    /**
     * Envoie d'un fichier pour modifier un document
     * @param int $id_e identifiant de la collectivité
     * @param int $id_d identifiant du dossier pastell
     * @param array $options
     * [] =>
     *  string field_name le nom du champs
     *  string file_name le nom du fichier
     *  integer file_number le numéro du fichier (pour les fichier multiple)
     *  string file_content le contenu du fichier
     *
     * @return array
     * [] =>
     *  result : ok - si l'enregistrement s'est bien déroulé
     *  formulaire_ok : 1 si le formulaire est valide, 0 sinon
     *  message : Message complémentaire
     */
    public function sendFile($id_e, $id_d, $options = array()) {
        $default_options = array(
            'id_e' => $id_e,
            'id_d' => $id_d,
            'field_name' => null, //le nom du champs
            'file_name' => null, //le nom du fichier
            'file_number' => null, //le numéro du fichier (pour les fichier multiple)
            'file_content' => null, //le contenu du fichier
        );
        $options = array_merge($default_options, $options);
        return $this->execute('list-document.php?' . $this->_paramsArray2String($options));
    }

    /**
     * Réception d'un fichier
     * @param int $id_e identifiant de la collectivité
     * @param int $id_d identifiant du dossier pastell
     * @param string $field_name le nom du champs
     * @param integer $file_number le numéro du fichier (pour les fichiers multiple)
     * @return array
     * [] =>
     *  file_name : le nom du fichier
     *  file_content : le contenu du fichier
     */
    public function receiveFile($id_e, $id_d, $field_name = null, $file_number = null) {
        $options = array(
            'id_e' => $id_e,
            'id_d' => $id_d,
            'field_name' => $field_name, //le nom du champs
            'file_number' => $file_number, //le numéro du fichier (pour les fichier multiple)
        );
        return $this->execute('receive-file.php?' . $this->_paramsArray2String($options));
    }

    /**
     * Execute une action sur un document
     *
     * @param int $id_e Identifiant de l'entité (retourné par list-entite)
     * @param int $id_d Identifiant unique du document (retourné par list-document)
     * @param string $action Nom de l'action (retourné par detail-document, champs action-possible)
     * @param array $options
     * @return array
     * [] =>
     *  result : 1 si l'action a été correctement exécute. Sinon, une erreur est envoyé
     *  message : Message complémentaire en cas de réussite
     *
     */
    public function action($id_e, $id_d, $action, $options = array()) {
        $acte = array(
            'id_e' => $id_e,
            'id_d' => $id_d,
            'action' => $action
        );
        $acte = array_merge($acte, $options);

        return $this->execute('action.php', $acte);
    }

    /**
     * Supprime un dossier dans Pastell
     * Attention: l'exécution de cette commande peut necessiter beaucoup de temps
     * @param int $id_e identifiant d'entité (collectivité)
     * @param int $id_d identifiant de dossier
     * @return bool|array
     */
    public function delete($id_e, $id_d) {
        return $this->action($id_e, $id_d, $this->config['action']['supression']);
    }

    /**
     * Récupère le contenu d'un fichier
     * @param int $id_e Identifiant de l'entité (retourné par list-entite)
     * @param int $id_d Identifiant unique du document (retourné par list-document)
     * @param string $field le nom d'un champ du document
     * @param int $num le numéro du fichier, s'il s'agit d'un champ fichier multiple
     * @return string fichier c'est le fichier qui est renvoyé directement
     */
    public function getFile($id_e, $id_d, $field, $num = 0) {
        $data = array(
            'id_e' => $id_e,
            'id_d' => $id_d,
            'field' => $field,
        );
        $page = 'recuperation-fichier.php';
        if ($num)
            $data['num'] = $num;

        return $this->execute($page, $data, true);
    }

    /**
     * TODO ajouter les autres paramètres d'entrée
     * Récupérer le journal
     * @param int $id_e identifiant de la collectivité
     * @param int $id_d identifiant du dossier pastell
     * @return bool|array résultat
     */
    public function journal($id_e, $id_d) {
        $acte = array(
            'id_e' => $id_e,
            'id_d' => $id_d
        );
        return $this->execute('journal.php', $acte);
    }

    /**
     * Déclare à Pastell que l'acte doit être envoyé en signature
     * Attention: le document doit être inséré dans un circuit avant !
     * @param int $id_e identifiant de la collectivité
     * @param int $id_d identifiant du dossier pastell
     * @param string $classification
     * @param bool $value
     * @return bool|array résultat
     */
    public function envoiSignature($id_e, $id_d, $classification = null, $value = true) {
        $data = array(
            'id_e' => $id_e,
            'id_d' => $id_d,
            $this->config['field']['envoi_signature'] => $value,
        );
        if (!empty($classification))
            $data[$this->config['field']['classification']] = $classification;
        return $this->execute('modif-document.php', $data);
    }

    /**
     * Déclare à Pastell que l'acte doit être envoyé au tdt
     * Attention: le document doit être inséré dans un circuit avant !
     * @param int $id_e identifiant de la collectivité
     * @param int $id_d identifiant du dossier pastell
     * @param string $classification
     * @param bool $value
     * @return bool|array résultat
     */
    public function envoiTdt($id_e, $id_d, $classification = null, $value = true) {
        $data = array(
            'id_e' => $id_e,
            'id_d' => $id_d,
            $this->config['field']['envoi_tdt'] => $value
        );
        if (!empty($classification))
            $data[$this->config['field']['classification']] = $classification;
        return $this->execute('modif-document.php', $data);
    }

    /**
     * Déclare à Pastell que l'acte doit être envoyé au sae
     * Attention: le document doit être inséré dans un circuit avant !
     * @param int $id_e identifiant de la collectivité
     * @param int $id_d identifiant du dossier pastell
     * @param bool $value
     * @return bool|array résultat
     */
    public function envoiSae($id_e, $id_d, $value = true) {
        $data = array(
            'id_e' => $id_e,
            'id_d' => $id_d,
            $this->config['field']['envoi_sae'] => $value
        );
        return $this->execute('modif-document.php', $data);
    }

    /**
     * Déclare à Pastell que l'acte doit être envoyé à la GED
     * Attention: le document doit être inséré dans un circuit avant !
     * @param int $id_e identifiant de la collectivité
     * @param int $id_d identifiant du dossier pastell
     * @param bool $value
     * @return bool|array résultat
     */
    public function envoiGed($id_e, $id_d, $value = true) {
        $data = array(
            'id_e' => $id_e,
            'id_d' => $id_d,
            $this->config['field']['envoi_ged'] => $value
        );
        return $this->execute('modif-document.php', $data);
    }

    /**
     * déclare à PASTELL par quel cheminement devra passer le dossier
     * Attention: le document doit être inséré dans un circuit avant !
     * @param int $id_e identifiant de la collectivité
     * @param int $id_d identifiant du dossier pastell
     * @param bool $auto déclaration automatique du cheminement en fonction de la configuration webdelib
     * @param array $params si !auto, les paramètres doivent être passés dans ce tableau
     * @return bool|array résultat
     */
    public function editTransmission($id_e, $id_d, $auto = true, $params = array()) {
        $data = array(
            'id_e' => $id_e,
            'id_d' => $id_d
        );
        if ($auto) {
            if (Configure::read('USE_PARAPHEUR') && Configure::read('PARAPHEUR') == 'PASTELL')
                $data[$this->config['field']['envoi_signature']] = true;
            if (Configure::read('USE_TDT') && Configure::read('TDT') == 'PASTELL')
                $data[$this->config['field']['envoi_tdt']] = true;
            if (Configure::read('USE_SAE') && Configure::read('SAE') == 'PASTELL')
                $data[$this->config['field']['envoi_sae']] = true;
            if (Configure::read('USE_GED') && Configure::read('GED_PASTELL'))
                $data[$this->config['field']['envoi_ged']] = true;
        } elseif (!empty($params)) {
            $data = array_merge($data, $params);
        } else {
            return false;
        }
        return $this->execute("modif-document.php", $data);
    }

    /**
     * Envoi à Pastell l'information sur le circuit du parapheur à emprunter
     * @param int $id_e identifiant de la collectivité
     * @param int $id_d identifiant du dossier pastell
     * @param string $sous_type
     * @return bool|array résultat
     */
    public function selectCircuit($id_e, $id_d, $sous_type) {
        $infos = array(
            'id_e' => $id_e,
            'id_d' => $id_d,
            $this->config['field']['iparapheur_type'] => $this->parapheur_type,
            $this->config['field']['iparapheur_sous_type'] => utf8_decode($sous_type),
            $this->config['field']['envoi_signature'] => true
        );
        return $this->execute('modif-document.php', $infos);
    }

    /**
     * Joint ses annexes à un document dans Pastell
     * @param int $id_e identifiant de la collectivité
     * @param int $id_d identifiant du dossier pastell
     * @param string $annex chemin de l'annexe à attacher
     * @return bool|array résultat
     */
    public function sendAnnex($id_e, $id_d, $annex) {
        $acte = array(
            'id_e' => $id_e,
            'id_d' => $id_d,
            $this->config['field']['autre_document_attache'] => "@$annex"
        );
        return $this->execute('modif-document.php', $acte);
    }

    /**
     * Récupère le tableau de classifications
     * @param int $id_e identifiant de la collectivité
     * @param int $id_d identifiant du dossier pastell
     * @return array
     */
    public function getClassification($id_e, $id_d) {
        return $this->getInfosField($id_e, $id_d, 'classification');
    }
}
