<?php

/*
 * Created on 24 sept. 07
 */

class CollectivitesController extends AppController {

    public $uses = array('Collectivite', 'User', 'Infosupdef');
    public $components = array(
        'Auth' => array(
            'mapActions' => array(
                'read' => array('admin_index'),
                'create' => array('admin_add', 'edit9Cases'),
                'update' => array('admin_edit','setLogo','setMails'),
                ),
        )
    );

    function admin_index() {
        $collectivite = $this->Collectivite->find('first', array(
            'conditions' => array('Collectivite.id' => 1),
            'recursive' => -1));
        
        $logo_path = null;
        if (file_exists(APP . WEBROOT_DIR . DS . 'files' . DS . 'image' . DS . 'logo'))
            $logo_path = FULL_BASE_URL . $this->base . "/files/image/logo";

        $this->set('collectivite', $collectivite);
        $this->set('logo_path', $logo_path);
    }

    function admin_edit() {
        if (Configure::read('USE_PASTELL')) {
            $this->Pastell = $this->Components->load('Pastell');
        }
        if (!empty($this->data)) {
            $this->Collectivite->id = 1;
            if (Configure::read('USE_PASTELL')) {
                $entities = $this->Pastell->listEntities();
                $this->request->data['Collectivite']['nom'] = $entities[$this->data['Collectivite']['id_entity']];
            }
            if ($this->Collectivite->save($this->data)){
                $this->Session->setFlash('Informations de la collectivité modifiées', 'growl');
                
                return $this->redirect($this->previous);
            }
            $this->Session->setFlash('Erreur durant la sauvegarde', 'growl');
        }
        
        $this->Collectivite->recursive=-1;
        $this->request->data = $this->Collectivite->read(null, 1);
        
        if (Configure::read('USE_PASTELL')) {
            $this->set('entities', $this->Pastell->listEntities());
            $this->set('selected', $this->data['Collectivite']['id_entity']);
        }
    }

    function setLogo() {
        if (!empty($this->data)) {
           if(!empty($this->data['Collectivite']['logo']['tmp_name'])){
                $image = file_get_contents($this->data['Collectivite']['logo']['tmp_name']);
                $this->Collectivite->id = 1;
                if($this->Collectivite->saveField('logo', $image, true))
                {
                    App::uses('File', 'Utility');
                    $file = new File(WWW_ROOT . 'files' . DS . 'image' . DS . 'logo', true);
                    $file->write($image);
                    $file->close();
                    $this->Session->setFlash('Le Logo a été ajouté', 'growl', array('type' => 'information'));
                    return $this->redirect($this->previous);
                }
            }
            $this->Session->setFlash('Merci de soumettre une image.', 'growl', array('type' => 'error'));
        }
    }

    function setMails() {
        $path = CONFIG_PATH . 'emails/';
        $this->set('email_path', $path);

        if (!empty($this->data)) {
            $cpt = 1;
            foreach ($this->data['Mail'] as $mail) {
                if ($cpt == 1) {
                    if ($mail['name'] == '')
                        continue;
                    $name_file = 'refus';
                    $tmp_file = $mail['tmp_name'];
                    if (!move_uploaded_file($tmp_file, $path . $name_file))
                        exit("Impossible de copier le fichier");
                }
                if ($cpt == 2) {
                    if ($mail['name'] == '')
                        continue;
                    $name_file = 'traiter';
                    $tmp_file = $mail['tmp_name'];
                    if (!move_uploaded_file($tmp_file, $path . $name_file))
                        exit("Impossible de copier le fichier");
                }
                if ($cpt == 3) {
                    if ($mail['name'] == '')
                        continue;
                    $name_file = 'insertion';
                    $tmp_file = $mail['tmp_name'];
                    if (!move_uploaded_file($tmp_file, $path . $name_file))
                        exit("Impossible de copier le fichier");
                }
                if ($cpt == 4) {
                    if ($mail['name'] == '')
                        continue;
                    $name_file = 'convocation';
                    $tmp_file = $mail['tmp_name'];
                    if (!move_uploaded_file($tmp_file, $path . $name_file))
                        exit("Impossible de copier le fichier");
                }
                $cpt++;
            }
            return $this->redirect(array('action'=>'admin_index'));
        }
    }

    /**
     * Recupere le tableau des differentes options d'affichage pour les selecteurs
     */
    private function _selecteur9Cases()
    {
        $selecteur = array('Typeacte.name' => 'Type d\'acte',
                           'Seance.libelle' => 'Séance(s)',
                           'Theme.libelle' => 'Thème');
         
        $selecteur['Délibération'] = array(
            'Service.libelle' => 'Service émetteur',
            'Deliberation.objet' => 'Objet',
            'Deliberation.titre'=> 'Titre du projet',
            'Deliberation.num_pref'=> 'Classification',
            'Deliberation.date_limite'=> 'A traiter avant le');
        
        $selecteur['Circuit'] = array(
            'Circuit.nom' => 'Nom du circuit',
            'Circuit.last_viseur' => 'Dernière action de');
        
        //infos supplémentaires
        $this->Infosupdef->virtualFields['nomType'] = 'Infosupdef.nom || \' (\' || Infosupdef.type ||\')\'';
        $this->Infosupdef->virtualFields['idInfosup'] = 'Infosupdef.id';
        $infosupList = $this->Infosupdef->find('list', array(
            'fields' => array('idInfosup','nomType'),
            'conditions' => array( 'actif'=> true, 'model'=>'Deliberation' ),
            'recursive' => -1,
            'order' => 'nom'
        ));
        $children = array();
        foreach ($infosupList as $key=>$value){
            $children["Infosupdef.id.$key"] = $value;
        }
        $selecteur['Informations supplementaires'] = $children;
        
        return $selecteur;
    }

    /**
     * Affichage de l'editeur des 9 cases pour l'ensemble des projets
     * @$opt = Appel en méthode $_GET
     */
    function edit9Cases($opt=null)
    {
        $invalidePost = false;
        $selecteur = array();
        $templateProject = $this->Collectivite->getJson9Cases();

        //Sauvegarde des 9 cases
        if ($this->request->is('post')) {
            $this->Collectivite->id = 1;
            $templateProjectPost = array_values($this->request->data);
            if($this->Collectivite->saveField('templateProject', json_encode($templateProjectPost))) {
                $this->Session->write('Collective.templateProject', $templateProjectPost);
                $this->Session->setFlash('La modification à été correctement sauvegardée', 'growl', array('type' => 'information'));
                $templateProject = $templateProjectPost;
                unset($templateProjectPost);
            }
        }
        //On remet les valeurs par defaut des 9 cases
        else if($opt == 'revertModification') {
            $this->Collectivite->id = 1;
            if($this->Collectivite->saveField('templateProject', $templateProject)) {
                $templateProject = json_decode($templateProject,true);
                $this->Session->write('Collective.templateProject', $templateProject);
                $this->Session->setFlash('La restauration à été correctement effectuée', 'growl', array('type' => 'information'));
            } else {
                $this->Session->setFlash('La restauration à échoué', 'growl', array('type' => 'danger'));
            }
            
        //On affiche l'enregistrement existant en Session
        } else if($this->Session->check('Collective.templateProject')){
            $templateProject = $this->Session->read('Collective.templateProject');
        }

        //on construit les options et on affiche la valeur par defaut pour chaque select
        $this->set('selecteurProject', $this->_selecteur9Cases());
        $this->set('templateProject', $templateProject);
    }   
}
