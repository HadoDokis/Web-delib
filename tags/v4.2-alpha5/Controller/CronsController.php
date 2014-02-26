<?php

/**
 * Class CronsController
 * Gestion des tâches planifiées dans Webdelib
 */
class CronsController extends AppController {

    public $helpers = array('DurationPicker');
    public $components = array(
        'VueDetaillee',
        'Applist',
        'Crons',
        'Paginator'
    );

    public $demandeDroit = array('index');

    const FORMAT_DATE = 'Y-m-d H:i:s';

    /**
     * Vue détaillée des crons (tâches planifiées)
     */
    function view($id = null) {
        // initialisations
        $this->request->data = $this->{$this->modelClass}->find('first', array(
            'recursive' => 1,
            'conditions' => array('Cron.id' => $id)));
        if (empty($this->request->data)) {
            $this->Session->setFlash(__('Invalide id pour la', true) . ' ' . __('tâche planifiée', true) . ' : ' . __('affichage de la vue impossible.', true), 'growl', array('type' => 'important'));
            return $this->redirect(array('action' => 'index'));
        } else {
            $this->pageTitle = Configure::read('appName') . ' : ' . __('Tâche planifiée', true) . ' : ' . __('vue détaillée', true);

            /* préparation des informations à afficher dans la vue détaillée */
            $maVue = new $this->VueDetaillee($this->request->data[$this->modelClass]['nom'], __('Retour à la liste des tâches planifiées', true));
            $maVue->ajouteSection(__('Informations principales', true));
            $maVue->ajouteLigne(__('Identifiant interne (id)', true), $this->request->data[$this->modelClass]['id']);
            $maVue->ajouteLigne(__('Nom', true), $this->request->data[$this->modelClass]['nom']);
            $maVue->ajouteLigne(__('Description', true), $this->request->data[$this->modelClass]['description']);
            $maVue->ajouteLigne(__('Fonction appelée', true), $this->request->data[$this->modelClass]['plugin'] . '/' . $this->request->data[$this->modelClass]['model'] . '/' . $this->request->data[$this->modelClass]['action']);
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
            } else
                $this->Session->setFlash(__('Veuillez corriger les erreurs du formulaire.', true), 'growl', array('type' => 'erreur'));
        }
        if ($sortie)
            return $this->redirect(array('action' => 'index'));
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
                return $this->redirect(array('action' => 'index'));
            } else
                $this->Session->setFlash(__('Veuillez corriger les erreurs du formulaire.', true), 'growl', array('type' => 'erreur'));
        } else {
            $plugin_ctrl_method = $this->Applist->construireArbre();
            $this->set('plugin_ctrl_method', $plugin_ctrl_method);
        }
        $this->pageTitle = Configure::read('appName') . ' : ' . __('Création de tâche planifiée', true);
    }

    /**
     * Edition d'une tâche planifiée
     */
    function edit($id) {
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
            } else
                $this->Session->setFlash(__('Veuillez corriger les erreurs du formulaire.', true), 'growl', array('type' => 'erreur'));
        }
        if ($sortie)
            return $this->redirect($this->previous);
        else {
            $this->pageTitle = Configure::read('appName') . ' : ' . __('Tâche planifiée', true) . ' : ' . __('Modification', true);
        }
    }

    /**
     * Liste des crons
     */
    function index() {
        $this->pageTitle = Configure::read('appName') . ' : ' . __('Tâches planifiées', true);
        $this->request->data = $this->Cron->find('all', array(
            'order' => array('Cron.next_execution_time ASC'),
        ));
        // mise en forme pour la vue
        foreach ($this->request->data as &$cron) {
            $cron['Cron']['statusLibelle'] = $this->{$this->modelClass}->libelleStatus($cron['Cron']['last_execution_status']);
            $cron['Cron']['activeLibelle'] = $this->{$this->modelClass}->libelleActive($cron['Cron']['active']);
            $cron['Cron']['durationLibelle'] = $this->{$this->modelClass}->libelleDuration($cron['Cron']['execution_duration']);
        }
    }

    function delete($id) {
        if ($id != null) {
            $this->Cron->delete($id);
            $this->Session->setFlash(__('Tâche planifiée numéro ', true) . $id . __(' supprimée !', true), 'growl', array('type' => 'important'));
        } else {
            $this->Session->setFlash(__('Tâche planifiée numéro ', true) . $id . __(' introuvable !', true), 'growl', array('type' => 'erreur'));
        }
        $this->redirect($this->referer());
    }

    function unlock($id) {
        if ($id != null) {
            $this->Cron->id = $id;
            $this->Cron->saveField('lock', false);
            $this->Session->setFlash(__('Tâche planifiée numéro ', true) . $id . __(' dévérrouillée !', true), 'growl', array('type' => 'important'));
        } else {
            $this->Session->setFlash(__('Tâche planifiée numéro ', true) . $id . __(' introuvable !', true), 'growl', array('type' => 'erreur'));
        }
        $this->redirect($this->referer());
    }

    /**
     * fonction d'exécution du cron $id
     * @param integer $id id de la tâche a exécuter
     * @return redirect
     */
    function executer($id) {
        // lecture du crons à exécuter
        $cron = $this->Cron->find('first', array(
            'recursive' => -1,
            'fields' => array('id', 'nom', 'lock'),
            'conditions' => array('id' => $id)));

        // excécutions
        if (empty($cron))
            $this->Session->setFlash(__('Invalide id pour la', true) . ' ' . __('tâche planifiée', true) . ' : ' . __('exécution impossible.', true), 'growl', array('type' => 'important'));
        elseif ($cron['Cron']['lock']) {
            $this->Session->setFlash(__('Tâche', true) . ' \'' . $cron['Cron']['nom'] . '\' ' . __('vérrouillée !', true), 'growl');
        } else {
            $this->Crons->runCronId($id);
            $this->Session->setFlash(__('Tâche', true) . ' \'' . $cron['Cron']['nom'] . '\' ' . __('exécutée.', true), 'growl');
        }
        return $this->redirect($this->referer());
    }

    /**
     * fonction d'exécution de tous les crons actifs (appelée par le shell 'cron')
     */
    function runCrons() {
        $this->Crons->runAll();

        $errors = $this->Cron->find('count', array(
            'recursive' => -1,
            'conditions' => array('last_execution_status' => Cron::EXECUTION_STATUS_FAILED)
        ));

        if (!$errors)
            $message = 'Toutes les tâches actives ont été exécutées avec succès.';
        elseif ($errors = 1)
            $message = 'Attention: une tâche s&apos;est achevée avec un code d&apos;erreur.';
        else
            $message = 'Attention: plusieurs tâches se sont achevées avec un code d&apos;erreur.';

        $this->Session->setFlash("<p>Exécution terminée !</p><p>$message</p>", 'growl');
        return $this->redirect(array('action' => 'index'));
    }

}
