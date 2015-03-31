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
    
    private function _array_depth($array) {
        $max_depth = 1;
        if (is_array($array)){
            foreach ($array as $value) {
                if (is_array($value)) {
                    $depth = $this->_array_depth($value) + 1;

                    if ($depth > $max_depth) {
                        $max_depth = $depth;
                    }
                }
            }
        }
        return $max_depth;
    }


    function edit9Cases($opt=null)
    {
        $rollback = false;
        //sauvegarde
        if (!empty($this->request->data)) {
            $this->Collectivite->id = 1;
            foreach($this->request->data['Collectivites'] as $key=>$val)
            {
                if (strpos($val,'&&') !== false) {
                    $valTemp = array();
                    foreach (explode('&&',$val) as $valChild){
                        $valTemp[] = json_decode($valChild, true);
                    }
                    $val = $valTemp;
                }
                else {
                    $val = json_decode($val, true);
                }
                if(empty($val)) $rollback=true;
                $caseTotal[$key] = $val;
            }
            ksort($caseTotal);
            if (!$rollback){
                if($this->Collectivite->saveField('templateProject', json_encode($caseTotal), true)) {
                    $this->Session->write('Collective.templateProject', $caseTotal);
                    $this->Session->setFlash('La modification à été correctement sauvegardée', 'growl', array('type' => 'information'));
                }
            } else {
                $this->Session->setFlash('La sauvegarde à échoué', 'growl', array('type' => 'danger'));
            }
        }
        
        //restaure les valeurs par defaut
        if($opt == 'revertModification')
        {
            $this->Collectivite->id = 1;
            if($this->Collectivite->saveField('templateProject', $this->Collectivite->getJson9Cases(), true)) {
                $this->Session->write('Collective.templateProject', json_decode($this->Collectivite->getJson9Cases(),true));
                $this->Session->setFlash('La restauration à été correctement effectuée', 'growl', array('type' => 'information'));
            }
        }
       
        $collectivite = $this->Collectivite->find('first', array(
            'conditions' => array('Collectivite.id' => 1),
            'fields' => array('templateProject'),
            'recursive' => -1));
        
        //on recupere les options d'affichage pour les 9 cases   
       //groupe global
        $montableau[] = array('id'=> json_encode(array(
            'model'=>'Typeacte',
            'fields'=>'name',
                )), 'text'=> 'Type d\'acte');
        $montableau[] = array('id'=> json_encode(array(
            'model'=>'Seance',
            'fields'=>'libelle',
            'text' => 'Séance(s)'
                )), 'text'=> 'Séance(s)');
        $montableau[] = array('id'=> json_encode(array(
            'model'=>'Theme',
            'fields'=>'libelle',
            'text' => 'Thème'
                )), 'text'=> 'Thème');
        
        //groupe Deliberation
        $mongroup['id'] = '';
        $mongroup['text'] = 'Délibération';
        $children = array(
            array('id' => json_encode(array(
            'model'=>'Service',
            'fields'=>'libelle',
            'text'=>'Service émetteur',
                )), 'text'=> 'Service émetteur'), 
            array('id' => json_encode(array(
            'model'=>'Deliberation',
            'fields'=>'objet'
                )),'text'=> 'Objet'), 
            array('id' => json_encode(array(
            'model'=>'Deliberation',
            'fields'=>'titre',
             'text'=>'Titre du projet'
                )),'text'=> 'Titre du projet'),
            array('id' => json_encode(array(
            'model'=>'Deliberation',
            'fields'=>'num_pref',
             'text'=>'Classification'
                )),'text'=> 'Classification'),
            array('id' => json_encode(array(
            'model'=>'Deliberation',
            'fields'=>'date_limite',
             'text'=>'A traiter avant le'
                )), 'text'=> 'A traiter avant le'));
        $mongroup['children'] = $children;
        $montableau[] = $mongroup;
        
        //groupe Circuit
        $mongroup['id'] = '';
        $mongroup['text'] = 'Circuit';
        $children = array(
            array('id'=> json_encode(array(
            'model'=>'Circuit',
            'fields'=>'nom',
             'text'=>'Circuit'
                )), 'text'=> 'Nom du circuit'), 
            array('id'=> json_encode(array(
            'model'=>'Circuit',
            'fields'=>'last_viseur',
             'text'=>'Dernière action de'
                )), 'text'=> 'Dernière action de'));
        $mongroup['children'] = $children;
        $montableau[] = $mongroup;
        
        //groupe Infosup
        $this->Infosupdef->virtualFields['nomType'] = 'Infosupdef.nom || \' (\' || Infosupdef.type ||\')\'';
        $this->Infosupdef->virtualFields['idInfosup'] = 'Infosupdef.id';
        $infosupList = $this->Infosupdef->find('list', array(
            'fields' => array('idInfosup','nomType'),
            'conditions' => array( 'actif'=> true, 'model'=>'Deliberation' ),
            'recursive' => -1,
            'order' => 'nom'
        ));
        
        $mongroup['id'] = '';
        $mongroup['text'] = 'Informations supplementaires';
        $children = array();
        foreach ($infosupList as $key=>$value){
            
            $children[] = array('id' => json_encode(array(
                'model'=>'Infosupdef',
                'id' => $key,
                'text' => $value
                )), 'text' => $value);
        }
        $mongroup['children'] = $children;
        $montableau[] = $mongroup;

        $this->set('caseGroup',json_encode($montableau));
        
        //On affiche l'enregistrement existant ou celui par defaut
        if(!empty($collectivite['Collectivite']['templateProject'])){
            $templateProject = json_decode($collectivite['Collectivite']['templateProject'], true); 
        }else{
            $templateProject = json_decode($this->Collectivite->getJson9Cases(), true);
        }
        
        //On affiche temporairement les modifications non enregistrées
        if($rollback){
            $templateProject = $caseTotal;
        }
                
        $separator = '';
        foreach($templateProject as $key=>$value)
        {
            if($this->_array_depth($value)>1)
            {
                $i=0;
                $tempValueArray = '';
                $separator = '';
                foreach($value as $valueTemp)
                {
                    if($i>0) $separator = '&&';
                    $tempValueArray .= $separator.json_encode($valueTemp);
                    $i++;
                }
                $templateProjectFinal[$key] = $tempValueArray;
            }
            else if(!empty($value)){
                $templateProjectFinal[$key] = json_encode($value);
            }
            else {
                $templateProjectFinal[$key] = '';
            }
        }
        $this->set('templateProject',$templateProjectFinal);
    }   
}
