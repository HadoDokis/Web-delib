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
class PastellComponent extends Component
{

    /**
     * @param string $page script php
     * @param array $data
     * @return resource
     */
    protected function _initCurl($page, $data = array())
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, Configure::read("PASTELL_LOGIN") . ":" . Configure::read("PASTELL_PWD"));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $api = Configure::read("PASTELL_HOST") . "/api/$page";
        curl_setopt($curl, CURLOPT_URL, $api);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, TRUE);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        return $curl;
    }

    /**
     * @param $curl
     * @return mixed
     */
    protected function _exec($curl){
        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result);
    }

    /**
     * Converti un tableau d'arguments en une chaine pour envoi en méthode $_GET (args par url)
     *
     * @param array $params tableau de paramètres
     * @return string chaine de paramètres
     */
    protected function _paramsArray2String($params = array()){
        $target = '';
        foreach ($params as $opt => $val){
            if (!empty($val))
                $target.= $opt.'='.urlencode($val).'&';
        }
        return substr($target,0,strlen($target)-1);
    }

    /**
     * Permet d'obtenir la version de la plateforme. Pastell assure une compatibilité ascendante entre les différents numéro de révision.
     * @return array(
     *      version => Numéro de version commerciale,
     *      revision => Numéro de révision du dépôt de source officiel de Pastell  (https://adullact.net/scm/viewvc.php/?root=pastell),
     *      version_complete => Version affiché sur la l'interface web de la plateforme
     * )
     */
    public function getVersion()
    {
        $curl = $this->_initCurl('version.php');
        return $this->_exec($curl);
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
    public function getDocumentTypes()
    {
        $documents = array();
        $curl = $this->_initCurl('document-type.php');
        $result = curl_exec($curl);
        curl_close($curl);
        $natures = json_decode($result, true);
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
    public function getDocumentTypeInfos($type)
    {
        $curl = $this->_initCurl("document-type-info.php?type=$type");
        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result, true);
    }

    /**
     * Actions possible sur un type de document
     *
     * Ramène la liste des statuts/actions possibles sur ce type de document ainsi que des infos relatives à ce type de document
     *
     * @param string $type Type de document (retourné par document-type.php)
     * @return array propriete * : Couple clé/valeur de la propriété
     */
    public function getDocumentTypeActions($type){

        $curl = $this->_initCurl("document-type-action.php?type=$type");
        $result = curl_exec($curl);
        curl_close($curl);
        return (json_decode($result, true));
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
    public function listEntities($details=false)
    {
        $curl = $this->_initCurl('list-entite.php');
        $result = curl_exec($curl);
        curl_close($curl);
        $entities = json_decode($result, true);
        if ($details)
            return $entities;
        else{
            $collectivites = array();
            foreach ($entities as $entity){
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
     * @param integer $id_e Identifiant de l'entité (retourné par list-entite)
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
    public function listDocuments($id_e, $type, $offset=0, $limit=100)
    {
        $curl = $this->_initCurl("list-document.php?id_e=$id_e&type=$type&offset=$offset&limit=$limit");
        $result = curl_exec($curl);
        curl_close($curl);
        $documents = json_decode($result, true);
        return $documents;
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
    public function rechercheDocument($options = array()){
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
        $options = array_merge($default_options,$options);
        $curl = $this->_initCurl('list-document.php?'.$this->_paramsArray2String($options));
        $result = curl_exec($curl);
        curl_close($curl);
        $documents = json_decode($result, true);
        return $documents;
    }

    /**
     * Détail sur un document
     *
     * Récupère l'ensemble des informations sur un document Liste également les entités filles.
     *
     * @param integer $id_e Identifiant de l'entité (retourné par list-entite)
     * @param integer $id_d Identifiant unique du document (retourné par list-document)
     * @return array
     * Format de sortie :
     * [] => {
     *  info => Reprend les informations disponible sur list-document.php
     *  data => Données issue du formulaire (voir document-type-info.php pour savoir ce qu'il est possible de récupérer)
     *  action_possible * => Liste des actions possible (exemple : modification, envoie-tdt, ...)
     * }
     */
    public function detailDocument($id_e, $id_d)
    {
        $acte = array(
            'id_e' => $id_e,
            'id_d' => $id_d
        );
        $curl = $this->_initCurl('detail-document.php', $acte);
        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result, true);
    }

    /**
     * FIXME
     * Détail sur plusieurs documents
     *
     * Récupère l'ensemble des informations sur un document Liste également les entités filles.
     *
     * @param integer $id_e Identifiant de l'entité (retourné par list-entite)
     * @param array $id_d Tableau d'identifiants uniques de documents (retourné par list-document)
     * @return array
     * Format de sortie :
     * [] => {
     *  info => Reprend les informations disponible sur list-document.php
     *  data => Données issue du formulaire (voir document-type-info.php pour savoir ce qu'il est possible de récupérer)
     *  action_possible * => Liste des actions possible (exemple : modification, envoie-tdt, ...)
     * }
     */
    public function detailDocuments($id_e, $id_d)
    {
        $acte = array(
            'id_e' => $id_e,
            'id_d' => json_encode($id_d) //FIXME format id_d attendu
        );

        $curl = $this->_initCurl('detail-several-document.php', $acte);
        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result, true);
    }

    /**
     * @param integer $id_e Identifiant de l'entité (retourné par list-entite)
     * @param string $type Type de document (retourné par document-type)
     * @return integer id_d	Identifiant unique du document crée.
     * @throws Exception Si erreur lors de la création
     */
    public function createDocument($id_e, $type)
    {
        $curl = $this->_initCurl("create-document.php?id_e=$id_e&type=$type");
        $result = curl_exec($curl);
        curl_close($curl);

        $infos = json_decode($result, true);
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
     * @param integer $id_e Identifiant de l'entité (retourné par list-entite)
     * @param integer $id_d Identifiant unique du document (retourné par list-document)
     * @param string $field le nom d'un champ du document
     * @return array
     * valeur_possible * => Information supplémentaire sur la valeur possible (éventuellement sous forme de tableau associatif)
     */
    public function getInfosField($id_e, $id_d, $field)
    {
        $curl = $this->_initCurl("external-data.php?id_e=$id_e&id_d=$id_d&field=$field");
        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result, true);
    }

    /**
     * FIXME
     * Modification d'un document
     *
     * @param $id_e
     * @param $id_d
     * @param array $delib
     * @param array $annexes
     * @return int|string
     * result : ok - si l'enregistrement s'est bien déroulé
     * formulaire_ok : 1 si le formulaire est valide, 0 sinon
     * message : Message complémentaire
     *
     * A noter que pour connaître la liste et les intitulés exacts des champs modifiables,
     * il convient d'utiliser la fonction document-type-info.php, en lui précisant le type concerné.
     */
    public function modifDocument($id_e, $id_d, $delib = array(), $annexes = array())
    {
        if (empty($delib)) return -1;
        App::uses('Deliberation', 'Model');
        $this->Deliberation = new Deliberation();
        $model_id = $this->Deliberation->getModel($delib['Deliberation']['id']);
        //Génération du fichier et ajout au zip
//        $this->requestAction('/models/generer/'.$delib['Deliberation']['id'].'/null/'.$model_id.'/0/0/Document/0/1');
        $file_name = WEBROOT_PATH . "/files/generee/fd/null/" . $delib['Deliberation']['id'] . "/Document.pdf";
        //FIXME caractères spéciaux
//        $delib['Deliberation']['objet_delib'] = str_replace(array("'",'"','&','#'), "_", $delib['Deliberation']['objet_delib']);

        $acte = array(
            'id_e' => $id_e,
            'id_d' => $id_d,
            'objet' => $delib['Deliberation']['objet_delib'],
            'date_de_lacte' => $delib['Seance']['date'],
            'numero_de_lacte' => $delib['Deliberation']['num_delib'],
            'type' => $delib['Nomenclature']['code'],
            'arrete' => "@$file_name",
            'acte_nature' => $delib['Deliberation']['nature_id'],
        );

        $curl = $this->_initCurl('modif-document.php', $acte);
        $result_modif = curl_exec($curl);
        curl_close($curl);
        foreach ($annexes as $annex)
            $this->sendAnnex($id_e, $id_d, $annex);
//        debug($result_modif);
        $resultat = $this->getInfosDocument($id_e, $id_d);
        $pos = strpos($resultat['data']['classification'], "existe");
        if ($pos !== false && $resultat['data']['envoi_tdt']) {
            $result = utf8_decode($resultat['data']['classification']);
        } else
            $result = 1;

        return $result;
    }

    /**
     * Envoie d'un fichier pour modifier un document
     *
     * @param integer $id_e Identifiant de l'entité (retourné par list-entite)
     * @param integer $id_d Identifiant unique du document (retourné par list-document)
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
    public function sendFile($id_e, $id_d, $options = array()){
        $default_options = array(
            'id_e' => $id_e,
            'id_d' => $id_d,
            'field_name' => null, //le nom du champs
            'file_name' => null, //le nom du fichier
            'file_number' => null, //le numéro du fichier (pour les fichier multiple)
            'file_content' => null, //le contenu du fichier
        );
        $options = array_merge($default_options,$options);
        $curl = $this->_initCurl('list-document.php?'.$this->_paramsArray2String($options));
        $result = curl_exec($curl);
        curl_close($curl);
        $documents = json_decode($result, true);
        return $documents;
    }

    /**
     * Réception d'un fichier
     *
     * @param integer $id_e
     * @param integer $id_d
     * @param string $field_name le nom du champs
     * @param integer $file_number le numéro du fichier (pour les fichiers multiple)
     * @return array
     * [] =>
     *  file_name : le nom du fichier
     *  file_content : le contenu du fichier
     */
    public function receiveFile($id_e, $id_d, $field_name = null, $file_number = null){
        $options = array(
            'id_e' => $id_e,
            'id_d' => $id_d,
            'field_name' => $field_name, //le nom du champs
            'file_number' => $file_number, //le numéro du fichier (pour les fichier multiple)
        );
        $curl = $this->_initCurl('receive-file.php?'.$this->_paramsArray2String($options));
        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result, true);
    }

    /**
     * Execute une action sur un document
     *
     * @param integer $id_e Identifiant de l'entité (retourné par list-entite)
     * @param integer $id_d Identifiant unique du document (retourné par list-document)
     * @param string $action Nom de l'action (retourné par detail-document, champs action-possible)
     * @param array $destinataire tableau contenant l'identifiant des destinataires pour les actions qui le requièrent
     * @return array
     * [] =>
     *  result : 1 si l'action a été correctement exécute. Sinon, une erreur est envoyé
     *  message : Message complémentaire en cas de réussite
     *
     */
    public function action($id_e, $id_d, $action, $destinataire = array())
    {
        $acte = array(
            'id_e' => $id_e,
            'id_d' => $id_d,
            'action' => $action
        );
        //FIXME : à tester
        if (!empty($destinataire))
            $acte['destinataire'] = $destinataire;

        $curl = $this->_initCurl('action.php', $acte);
        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result, true);
    }

    /**
     * Récupère le contenu d'un fichier
     *
     * @param integer $id_e Identifiant de l'entité (retourné par list-entite)
     * @param integer $id_d Identifiant unique du document (retourné par list-document)
     * @param string $field le nom d'un champ du document
     * @param int $num le numéro du fichier, s'il s'agit d'un champ fichier multiple
     * @return string fichier c'est le fichier qui est renvoyé directement
     */
    public function getFile($id_e, $id_d, $field, $num = 0)
    {
        //FIXME url api?  https://pastell.test.adullact.org/api/recuperation-fichier.php
        $url = Configure::read("PASTELL_HOST") . "/web/document/recuperation-fichier.php?id_e=$id_e&id_d=$id_d&field=$field";
        if ($num)
            $url .= '&num='.$num;
        $hostfile = fopen($url, 'r');
        $filename = tempnam("/tmp/", $field . "_");
        $fh = fopen($filename, 'w');

        while (!feof($hostfile)) {
            $output = fread($hostfile, 8192);
            fwrite($fh, $output);
        }

        fclose($hostfile);
        fclose($fh);
        $content = file_get_contents($filename);
        unlink($filename);
        return ($content);
    }

    /**
     * TODO ajouter les autres paramètres d'entrée
     * Récupérer le journal
     *
     * @param $id_e
     * @param $id_d
     * @return mixed
     */
    public function journal($id_e, $id_d)
    {
        $acte = array(
            'id_e' => $id_e,
            'id_d' => $id_d
        );
        $curl = $this->_initCurl('journal.php', $acte);
        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result);
    }

    public function insertInParapheur($id_e, $id_d, $sous_type = null)
    {
        //FIXME propriété envoi_signature au lieu de envoi_iparapheur?
        $curl = $this->_initCurl("modif-document.php?id_e=$id_e&id_d=$id_d&envoi_iparapheur=true");
        $result = curl_exec($curl);
        curl_close($curl);
    }

    public function insertInCircuit($id_e, $id_d, $sous_type)
    {
        $infos = array(
            'id_e' => $id_e,
            'id_d' => $id_d,
            'iparapheur_sous_type' => $sous_type);
        $curl = $this->_initCurl("modif-document.php", $infos);
        $result = curl_exec($curl);
        curl_close($curl);
    }

    public function sendAnnex($id_e, $id_d, $annex)
    {
        $acte = array(
            'id_e' => $id_e,
            'id_d' => $id_d,
            'autre_document_attache' => "@$annex"
        );
        $curl = $this->_initCurl('modif-document.php', $acte);
        $result = curl_exec($curl);
        curl_close($curl);
    }

}
