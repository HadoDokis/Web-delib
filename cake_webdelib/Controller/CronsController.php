<?php

class CronsController extends AppController {

    var $name = 'Crons';
    var $helpers = array('DurationPicker');
    var $components = array('VueDetaillee', 'Applist');
    // Gestion des droits
    var $commeDroit = array(
        'view' => 'Crons:index',
        'edit' => 'Crons:index',
        'executer' => 'Crons:index');

    const FORMAT_DATE = 'Y-m-d H:i:s';

    function beforeFilter() {
        parent::beforeFilter();
    }

    var $paramBdx = array();

    /**
     * Vue détaillée des crons (tâches planifiées)
     */
    function view($id = null) {
        // initialisations
        require_once(APP . 'Lib' . DS . 'tools.php');

        $this->request->data = $this->{$this->modelClass}->find('first', array(
            'recursive' => 1,
            'conditions' => array('Cron.id' => $id)));
        if (empty($this->request->data)) {
            $this->Session->setFlash(__('Invalide id pour la', true) . ' ' . __('tâche planifiée', true) . ' : ' . __('affichage de la vue impossible.', true), 'growl', array('type' => 'important'));
            $this->redirect(array('action' => 'index'));
        } else {
            $this->pageTitle = Configure::read('appName') . ' : ' . __('Tâche planifiée', true) . ' : ' . __('vue détaillée', true);

            /* préparation des informations à afficher dans la vue détaillée */

            $maVue = new $this->VueDetaillee(
                    $this->request->data[$this->modelClass]['nom'], __('Retour à la liste des tâches planifiées', true));
            $maVue->ajouteSection(__('Informations principales', true));
            $maVue->ajouteLigne(__('Identifiant interne (id)', true), $this->request->data[$this->modelClass]['id']);
            $maVue->ajouteLigne(__('Nom', true), $this->request->data[$this->modelClass]['nom']);
            $maVue->ajouteLigne(__('Description', true), $this->request->data[$this->modelClass]['description']);
            $maVue->ajouteLigne(__('Fonction appelée', true), $this->request->data[$this->modelClass]['plugin'] . '/' . $this->request->data[$this->modelClass]['controller'] . '/' . $this->request->data[$this->modelClass]['action']);
            if ($this->request->data[$this->modelClass]['params'] != "" && $this->request->data[$this->modelClass]['params'] != "NULL")
                $maVue->ajouteLigne(__('Paramètre de la fonction', true), $this->request->data[$this->modelClass]['params']);
            $maVue->ajouteLigne(__('Active', true), $this->{$this->modelClass}->libelleActive($this->request->data[$this->modelClass]['active']));
            $maVue->ajouteLigne(__('Date de création', true), AppTools::timeFormat($this->request->data[$this->modelClass]['created'], 'd-m-Y à H:i:s'));
            $maVue->ajouteElement(__('Par', true), $this->request->data["CreatedUser"]["prenom"] . ' ' . $this->request->data["CreatedUser"]["nom"]);
            $maVue->ajouteLigne(__('Date de dernière modification', true), AppTools::timeFormat($this->request->data[$this->modelClass]['modified'], 'd-m-Y à H:i:s'));
            $maVue->ajouteElement(__('Par', true), $this->request->data["ModifiedUser"]["prenom"] . " " . $this->request->data["ModifiedUser"]["nom"]);

            $maVue->ajouteSection(__('Prochaine exécution', true));
            $maVue->ajouteLigne(__('Date prévue', true), AppTools::timeFormat($this->request->data[$this->modelClass]['next_execution_time'], 'd-m-Y à H:i:s'));
            $maVue->ajouteLigne(__('Délai entre 2 exécutions', true), AppTools::durationToString($this->request->data[$this->modelClass]['execution_duration']));

            $maVue->ajouteSection(__('Dernière exécution', true));
            $maVue->ajouteLigne(__('Statut', true), $this->{$this->modelClass}->libelleStatus($this->request->data[$this->modelClass]['last_execution_status']));
            $maVue->ajouteLigne(__('Début exécution', true), AppTools::timeFormat($this->request->data[$this->modelClass]['last_execution_start_time'], 'd-m-Y à H:i:s'));
            $maVue->ajouteElement(__('Fin exécution', true), AppTools::timeFormat($this->request->data[$this->modelClass]['last_execution_end_time'], 'd-m-Y à H:i:s'));
            $maVue->ajouteLigne(__('Rapport', true), nl2br($this->request->data[$this->modelClass]['last_execution_report']));

            $this->set('contenuVue', $maVue->getContenuVue());
        }
    }

    /**
     * Planification d'une tâche
     */
    function planifier($id = null) {
        $sortie = false;
        if (empty($this->request->data)) {
            // Initialisations
            $this->request->data = $this->{$this->modelClass}->find('first', array(
                'recursive' => -1,
                'conditions' => array('id' => $id)));
            if (empty($this->request->data)) {
                $this->Session->setFlash(__('Id invalide', true) . ' : ' . __('planification impossible.', true), 'growl', array('type' => 'important'));
                $sortie = true;
            } else {
                if (!empty($this->request->data[$this->modelClass]['next_execution_time'])) {
                    $nextExecutionTime = explode(' ', $this->request->data[$this->modelClass]['next_execution_time']);
                    $this->request->data[$this->modelClass]['next_execution_date'] = $nextExecutionTime[0];
                    $this->request->data[$this->modelClass]['next_execution_heure'] = $nextExecutionTime[1];
                }
            }
        } else {
            // Initialisations avant sauvegarde
            $this->request->data[$this->modelClass]['next_execution_time'] = array_merge($this->request->data[$this->modelClass]['next_execution_date'], $this->request->data[$this->modelClass]['next_execution_heure']);
            unset($this->request->data[$this->modelClass]['next_execution_date']);
            unset($this->request->data[$this->modelClass]['next_execution_heure']);
            $this->request->data[$this->modelClass]['modified_user_id'] = $this->Session->read('user.User.id');
            if ($this->{$this->modelClass}->save($this->request->data)) {
                $nomCron = $this->{$this->modelClass}->field('nom');
                $this->Session->setFlash(__('La tâche ', true) . ' \'' . $nomCron . '\' ' . __('a été correctement planifiée.', true), 'growl');
                $sortie = true;
            }
            else
                $this->Session->setFlash(__('Veuillez corriger les erreurs du formulaire.', true), 'growl', array('type' => 'erreur'));
        }
        if ($sortie)
            $this->redirect(array('action' => 'index'));
        else {
            $this->pageTitle = Configure::read('appName') . ' : ' . __('Tâche planifiée', true) . ' : ' . __('Planification', true);
        }
    }
    
    /**
     * Ajout d'une tâche planifiée
     */
    function add() {
        if (!empty($this->request->data)) {
            // Initialisations avant sauvegarde
            $this->request->data[$this->modelClass]['next_execution_time'] = array_merge($this->request->data[$this->modelClass]['next_execution_date'], $this->request->data[$this->modelClass]['next_execution_heure']);
            unset($this->request->data[$this->modelClass]['next_execution_date']);
            unset($this->request->data[$this->modelClass]['next_execution_heure']);
            $this->request->data[$this->modelClass]['created_user_id'] = $this->Session->read('user.User.id');
            $this->request->data[$this->modelClass]['modified_user_id'] = $this->Session->read('user.User.id');
            //mise en forme avant d'enregistrer plugin et controller
            $this->request->data[$this->modelClass]['plugin'] = strtolower($this->request->data[$this->modelClass]['plugin']);
            $this->request->data[$this->modelClass]['controller'] = strtolower(str_replace('Controller', '', $this->request->data[$this->modelClass]['controller']));
            
            $this->{$this->modelClass}->create($this->request->data);
            if ($this->{$this->modelClass}->save()) {
                $this->Session->setFlash(__('La tâche planifiée', true) . ' \'' . $this->{$this->modelClass}->field('nom') . '\' ' . __('a été modifiée.', true), 'growl');
                $this->redirect(array('action' => 'index'));
            }
            else
                $this->Session->setFlash(__('Veuillez corriger les erreurs du formulaire.', true), 'growl', array('type' => 'erreur'));
        }else{
            $plugin_ctrl_method = $this->Applist->construireArbre();
            $this->set('plugin_ctrl_method', $plugin_ctrl_method);
        }
        $this->pageTitle = Configure::read('appName') . ' : ' . __('Création de tâche planifiée', true);
    }
    
    /**
     * Edition d'une tâche planifiée
     */
    function edit($id){
        $sortie = false;
        if (empty($this->request->data)) {
            // Initialisations
            $this->request->data = $this->{$this->modelClass}->find('first', array(
                'recursive' => -1,
                'conditions' => array('id' => $id)));
            if (empty($this->request->data)) {
                $this->Session->setFlash(__('Id invalide', true) . ' : ' . __('edition impossible.', true), 'growl', array('type' => 'important'));
                $sortie = true;
            } else {
                if (!empty($this->request->data[$this->modelClass]['next_execution_time'])) {
                    $nextExecutionTime = explode(' ', $this->request->data[$this->modelClass]['next_execution_time']);
                    $this->request->data[$this->modelClass]['next_execution_date'] = $nextExecutionTime[0];
                    $this->request->data[$this->modelClass]['next_execution_heure'] = $nextExecutionTime[1];
                }
            }
            $plugin_ctrl_method = $this->Applist->construireArbre();
            $this->set('plugin_ctrl_method', $plugin_ctrl_method);
            $this->set('actual_plugin', $this->request->data[$this->modelClass]['plugin']);
            $this->set('actual_controller', $this->request->data[$this->modelClass]['controller']);
            $this->set('actual_action', $this->request->data[$this->modelClass]['action']);
        } else {
            // Initialisations avant sauvegarde
            $this->request->data[$this->modelClass]['next_execution_time'] = array_merge($this->request->data[$this->modelClass]['next_execution_date'], $this->request->data[$this->modelClass]['next_execution_heure']);
            unset($this->request->data[$this->modelClass]['next_execution_date']);
            unset($this->request->data[$this->modelClass]['next_execution_heure']);
            $this->request->data[$this->modelClass]['plugin'] = strtolower($this->request->data[$this->modelClass]['plugin']);
            $this->request->data[$this->modelClass]['controller'] = strtolower(str_replace('Controller', '', $this->request->data[$this->modelClass]['controller']));
            $this->request->data[$this->modelClass]['modified_user_id'] = $this->Session->read('user.User.id');
            if ($this->{$this->modelClass}->save($this->request->data)) {
                $nomCron = $this->{$this->modelClass}->field('nom');
                $this->Session->setFlash(__('La tâche planifiée ', true) . ' \'' . $nomCron . '\' ' . __('a été sauvegardée.', true), 'growl');
                $sortie = true;
            }
            else
                $this->Session->setFlash(__('Veuillez corriger les erreurs du formulaire.', true), 'growl', array('type' => 'erreur'));
        }
        if ($sortie)
            $this->redirect(array('action' => 'index'));
        else {
            $this->pageTitle = Configure::read('appName') . ' : ' . __('Tâche planifiée', true) . ' : ' . __('Modification', true);
        }
    }
    
    /**
     * Liste des crons
     */
    function index() {
        $this->pageTitle = Configure::read('appName') . ' : ' . __('Tâches planifiées', true);

        $this->paginate = array(
            'order' => array('Cron.next_execution_time ASC'),
            'page' => 1);
        $this->request->data = $this->paginate('Cron');

        // mise en forme pour la vue
        foreach ($this->request->data as $i => $data) {
            $this->request->data[$i]['Cron']['statusLibelle'] = $this->{$this->modelClass}->libelleStatus($data['Cron']['last_execution_status']);
            $this->request->data[$i]['Cron']['activeLibelle'] = $this->{$this->modelClass}->libelleActive($data['Cron']['active']);
            $this->request->data[$i]['Cron']['durationLibelle'] = $this->{$this->modelClass}->libelleDuration($data['Cron']['execution_duration']);
        }
    }
    
    function delete($id){
        if ($id!=null){
            $this->Cron->delete($id);
            $this->Session->setFlash(__('Tâche planifiée numéro ', true).$id.__(' supprimée !', true), 'growl', array('type' => 'important'));
        }else{
            $this->Session->setFlash(__('Tâche planifiée numéro ', true).$id.__(' introuvable !', true), 'growl', array('type' => 'erreur'));
        }
        $this->redirect(array('action' => 'index'));
    }


    /**
     * fonction d'exécution du cron $id
     * @param integer $id id de la tâche a exécuter
     */
    function executer($id) {
        // lecture du crons à exécuter
        $cron = $this->Cron->find('first', array(
            'recursive' => -1,
            'fields' => array('id', 'nom'),
            'conditions' => array('id' => $id)));

        // excécutions
        if (empty($cron))
            $this->Session->setFlash(__('Invalide id pour la', true) . ' ' . __('tâche planifiée', true) . ' : ' . __('exécution impossible.', true), 'growl', array('type' => 'important'));
        else {
            $this->Session->setFlash(__('Tâche planifiée', true) . ' \'' . $cron['Cron']['nom'] . '\' ' . __('exécutée.', true), 'growl');
            $this->_runCronId($cron['Cron']['id']);
        }
        $this->redirect(array('action' => 'index'));
    }

    /**
     * fonction d'exécution de tous les crons actifs (appelée par le shell 'cron')
     */
    function runCrons() {
        $this->log('Exécution des tâches crons..');
        $cron = $this->Cron;
        // initialisation de l'heure de prochaine exécution des tâches en erreur
        $now = date(self::FORMAT_DATE);
        require_once(APP . 'Lib' . DS . 'tools.php');
        $nextExecutionErrorTime = AppTools::addSubDurationToDate($now, $cron::NOUVEL_ESSAI_DELAIS_DURATION, self::FORMAT_DATE, 'sub');

        // lecture des crons à exécuter
        $crons = $this->Cron->find('all', array(
            'recursive' => -1,
            'fields' => array('id'),
            'conditions' => array(
                'active' => true
//                ,
//                array('OR' => array(
//                        'next_execution_time' => NULL,
//                        'next_execution_time <=' => $now)),
//                array('OR' => array(
//                        'last_execution_status' => array($cron::EXECUTION_STATUS_SUCCES, $cron::EXECUTION_STATUS_WARNING),
//                        array(
//                            'last_execution_status' => $cron::EXECUTION_STATUS_FAILED,
//                            'last_execution_start_time <=' => $nextExecutionErrorTime)))
                ),
            'order' => array('next_execution_time')));

        // excécutions
        foreach ($crons as $cron) {
            $this->_runCronId($cron['Cron']['id']);
        }

//        die(__(date(self::FORMAT_DATE).' : Execution des crons terminée.', true));
        $this->Session->setFlash(__(date(self::FORMAT_DATE) . ' : Execution des crons terminée.', true), 'growl');
        $this->redirect(array('action' => 'index'));
    }

    /**
     * fonction d'exécution d'un cron par son id (actif ou non)
     * @param integer $id id du cron a exécuter
     */
    function _runCronId($id) {
        // initialisations
        require_once(APP . 'Lib' . DS . 'tools.php');
        $lastExecutionStartTime = null;
        $appliUrl = FULL_BASE_URL . $this->webroot;
        $this->log("Serveur cible : ".$appliUrl);
        // lecture du cron à exécuter
        $cron = $this->Cron->find('first', array(
            'recursive' => -1,
            'conditions' => array('id' => $id)));

        // Sortie si tâche non trouvée
        if (empty($cron))
            return;
        
        // initialisation de l'url
        $url = $appliUrl . $cron['Cron']['plugin'] . '/' . $cron['Cron']['controller'] . '/' . $cron['Cron']['action'];
        if (!empty($cron['Cron']['params'])) {
            $params = explode(',', $cron['Cron']['params']);
            foreach ($params as $param)
                $url .= '/' . $param;
        }
        for ($iTentative = 1; $iTentative <= Cron::TENTATIVES_NB; $iTentative++) {
            // initialisation de la ressource curl
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            // execution de la requete
            if (empty($lastExecutionStartTime))
                $lastExecutionStartTime = date(self::FORMAT_DATE);
            $output = curl_exec($ch);
            
            $this->log("cron : ".$id." returned :".$output);
            
            $lastExecutionEndTime = date(self::FORMAT_DATE);
            curl_close($ch);
            // initialisation du rapport d'exécution
            $rappExecution = str_replace(array(Cron::MESSAGE_FIN_EXEC_SUCCES, Cron::MESSAGE_FIN_EXEC_WARNING, Cron::MESSAGE_FIN_EXEC_ERROR), '', $output);

            // si pas d'erreur, sortie du traitement
            if (empty($output) || strpos($output, Cron::MESSAGE_FIN_EXEC_ERROR) === false)
                break;

            // attente avant nouvelle tentative
            sleep(Cron::TENTATIVES_DELAIS_SECONDES);
        }

        // mise à jour du cron
        if (empty($output) || substr($output, 0, 21) == Cron::MESSAGE_FIN_EXEC_SUCCES) {
            $cron['Cron']['last_execution_status'] = Cron::EXECUTION_STATUS_SUCCES;
            $cron['Cron']['next_execution_time'] = $this->Cron->calcNextExecutionTime($cron);
        } elseif (strpos($output, Cron::MESSAGE_FIN_EXEC_WARNING) !== false) {
            $cron['Cron']['last_execution_status'] = Cron::EXECUTION_STATUS_WARNING;
            $cron['Cron']['next_execution_time'] = $this->Cron->calcNextExecutionTime($cron);
        } else {
            $cron['Cron']['last_execution_status'] = Cron::EXECUTION_STATUS_FAILED;
        }
        $cron['Cron']['last_execution_start_time'] = $lastExecutionStartTime;
        $cron['Cron']['last_execution_end_time'] = $lastExecutionEndTime;
        $cron['Cron']['last_execution_report'] = $rappExecution;
        $this->Cron->save($cron);
    }

    function test($id=null) {
        // initialisation de la ressource curl
//        $c = curl_init();
//        // indique à curl quelle url on souhaite télécharger
//        curl_setopt($c, CURLOPT_URL, "http://".$_SERVER['HTTP_HOST'].$this->base."/models/generer/$id/null/1/0/1/documents/0/false");
//        // indique à curl de ne pas retourner les headers http de la réponse dans la chaine de retour
//        curl_setopt($c, CURLOPT_HEADER, false);
//        // Display communication with server
//        curl_setopt($c, CURLOPT_VERBOSE, true); 
//        // indique à curl de nous retourner le contenu de la requête plutôt que de l'afficher
//        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
//        // execution de la requete
//        $output = curl_exec($c);
////        if ($output === false) {
////            trigger_error('Erreur curl : ' . curl_error($c), E_USER_WARNING);
////        } else {
//            debug($output);
////        }
//        curl_close($c);
//        $plugins = $this->Applist->get('plugins');
//        debug($plugins);
//        
//        $pluginControllers = $this->Applist->getPluginControllers($plugins[0]);
//        debug($pluginControllers);
//        
//        $pluginControllerMethods = $this->Applist->getControllerMethods($pluginControllers[2], $plugins[0]);
//        debug($pluginControllerMethods);
//
//        $controllers = $this->Applist->get('controller');
//        debug($controllers);
//        
//        $methods = $this->Applist->getControllerMethods($controllers[0]);
//        debug($methods);
    }

}

?>