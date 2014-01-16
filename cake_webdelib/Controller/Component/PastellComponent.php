<?php

class PastellComponent extends Component
{
    /**
     * @param string $page script php
     * @param array $data
     * @return resource
     */
    function _initCurl($page, $data = array())
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
    function _exec($curl){
        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result);
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
     * Liste l'ensemble des flux (types de documents) disponible sur Pastell
     * comme par exemple, les actes, les mails sécurisées, les flux citoyen, etc...
     *
     * @return array (type => nom)
     * type : Groupe de type de document (exemple : Flux généraux)
     * nom : Nom à afficher pour l'utilisateur (exemple : Actes, Message du centre de gestion)
     */
    function getDocumentsType()
    {
        $documents = array();
        $curl = $this->_initCurl('document-type.php');
        $result = curl_exec($curl);
        curl_close($curl);
        $natures = json_decode($result, true);
        foreach ($natures as $nature) {
            $type = $nature['type'];
            $nom = $nature['nom'];
            $documents[$type][] = $nom;
        }
        return $documents;
    }

    /**
     * Liste l'ensemble des champs d'un type document ainsi que les informations sur chaque champs
     * (type de champs, valeur par défaut, script de choix, ...)
     *
     * @param $type Type de document (retourné par document-type.php). Exemple : actes, mailsec, ...
     * @return mixed
     */
    function getInfosType($type)
    {
        $curl = $this->_initCurl("document-type-info.php?type=$type");
        $result = curl_exec($curl);
        curl_close($curl);

        $infos = json_decode($result);
        return ($infos);
    }

    /**
     * Liste l'ensemble des entités sur lesquelles l'utilisateur a des droits. Liste également les entités filles.
     *
     * @return array
     * id_e => Identifiant numérique de l'entité
     * denomination => Libellé de l'entité (ex : Saint-Amand-les-Eaux)
     * siren => Numéro SIREN de l'entité (si c'est une collectivité ou un centre de gestion)
     * centre_de_gestion => Identifiant numérique du CDG de la collectivité
     * entite_mere => Identifiant numérique de l'entité mère de l'entité (par exemple pour un service)
     */
    function listEntities()
    {
        $collectivites = array();
        $curl = $this->_initCurl('list-entite.php');
        $result = curl_exec($curl);
        curl_close($curl);
        $entities = json_decode($result);

        foreach ($entities as $entity) {
            $entity = (array)$entity;
            $collectivites[$entity['id_e']] = $entity['denomination'];
        }
        return ($collectivites);
    }

    /**
     * Liste l'ensemble des documents d'une entité Liste également les entités filles.
     * @param integer $id_e Identifiant numérique de l'entité
     * @param string $type Type de document
     * @param integer $offset (facultatif) numéro de la première ligne à retourner
     * @param integer $limit (facultatif, 100 par défaut) nombre de document à retourner
     * @return array (
     *                  id_e => Identifiant numérique de l'entité (identique à l'entrée),
     *                  id_d => Identifiant unique du document,
     *                  role => Rôle de l'entité sur le document (exemple : éditeur)
     * )
     */
    function listDocuments($id_e, $type, $offset=null, $limit=100)
    {
        $documents = array();
        $curl = $this->_initCurl('list-document.php');
        $result = curl_exec($curl);
        curl_close($curl);
        $entities = json_decode($result);

        foreach ($entities as $entity) {
            $entity = (array)$entity;
            $documents[$entity['id_e']] = $entity['denomination'];
            $documents[$entity['id_d ']] = $entity['denomination'];
        }
        return ($documents);
    }

    function getInfosField($id_e, $id_d, $type)
    {
        $curl = $this->_initCurl("external-data.php?id_e=$id_e&id_d=$id_d&field=$type");
        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result);
    }

    function createDocument($id_e, $type = 'actes')
    {
        $curl = $this->_initCurl("create-document.php?id_e=$id_e&type=$type");
        $result = curl_exec($curl);
        curl_close($curl);

        $infos = json_decode($result);
        $infos = (array)$infos;
        if (array_key_exists('id_d', $infos))
            return ($infos['id_d']);
        elseif (array_key_exists('error-message', $infos))
            throw new Exception($infos['error-message']);
        else
            throw new Exception("Une erreur inconnue est survenue");
    }

    function modifyDocument($id_e, $id_d, $delib = array(), $annexes = array())
    {
        $file = WEBROOT_PATH . "/files/generee/fd/null/" . $delib['Deliberation']['id'] . "/delib.pdf";
        $delib['Deliberation']['objet_delib'] = str_replace(Configure::read('CARACS_INTERDITS'), "_", $delib['Deliberation']['objet_delib']);

        $acte = array('id_e' => $id_e,
            'id_d' => $id_d,
            'objet' => $delib['Deliberation']['objet_delib'],
            'date_de_lacte' => $delib['Seance']['date'],
            'numero_de_lacte' => $delib['Deliberation']['num_delib'],
            'type' => $delib['Nomenclature']['code'],
            'arrete' => "@$file",
            'acte_nature' => $delib['Deliberation']['nature_id'],
        );
        $curl = $this->_initCurl('modif-document.php', $acte);
        curl_exec($curl);
        curl_close($curl);
        foreach ($annexes as $annex)
            $this->sendAnnex($id_e, $id_d, $annex);

        $resultat = $this->getInfosDocument($id_e, $id_d);
        $resultat = (array)$resultat;
        $resultat['data'] = (array)$resultat['data'];
        $pos = strpos($resultat['data']['classification'], "existe");
        if (($pos !== false) && $resultat['data']['envoi_tdt']) {
            $result = utf8_decode($resultat['data']['classification']);
        } else
            $result = 1;

        return $result;
    }

    function insertInParapheur($id_e, $id_d, $sous_type = null)
    {
        $curl = $this->_initCurl("modif-document.php?id_e=$id_e&id_d=$id_d&envoi_iparapheur=true");
        $result = curl_exec($curl);
        curl_close($curl);
    }

    function insertInCircuit($id_e, $id_d, $sous_type)
    {
        $infos = array('id_e' => $id_e,
            'id_d' => $id_d,
            'iparapheur_sous_type' => $sous_type);
        $curl = $this->_initCurl("modif-document.php", $infos);
        $result = curl_exec($curl);
        curl_close($curl);
    }

    function getInfosDocument($id_e, $id_d)
    {
        $acte = array('id_e' => $id_e,
            'id_d' => $id_d
        );
        $curl = $this->_initCurl('detail-document.php', $acte);
        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result);
    }

    function action($id_e, $id_d, $action)
    {
        $acte = array('id_e' => $id_e,
            'id_d' => $id_d,
            'action' => $action
        );
        $curl = $this->_initCurl('action.php', $acte);
        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result);
    }

    function sendAnnex($id_e, $id_d, $annex)
    {
        $acte = array('id_e' => $id_e,
            'id_d' => $id_d,
            'autre_document_attache' => "@$annex"
        );
        $curl = $this->_initCurl('modif-document.php', $acte);
        $result = curl_exec($curl);
        curl_close($curl);
    }

    function getFile($id_e, $id_d, $field)
    {
        $url = Configure::read("PASTELL_HOST") . "/web/document/recuperation-fichier.php?id_e=$id_e&id_d=$id_d&field=$field";
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

    function refresh()
    {
        $refresh_exec = Configure::read('REFRESH_PASTELL'); //FIXME n'existe pas
        if (!file_exists($refresh_exec)) {
            $result['code'] = 'KO';
            $result['message'] = "L'éxecutable n'a pas été trouvé";
            return $result;
        }
        $result = shell_exec($refresh_exec);
        return $result;
    }

    function journal($id_e, $id_d)
    {
        $acte = array('id_e' => $id_e,
            'id_d' => $id_d
        );
        $curl = $this->_initCurl('journal.php', $acte);
        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result);
    }
}
