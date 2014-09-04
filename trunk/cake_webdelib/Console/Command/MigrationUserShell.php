<?php

class MigrationUserShell extends AppShell {

    public $uses = array('User', 'Typeacte', 'Aro', 'Ado', 'ArosAdo');

    public function main() {
        
    }

    /**
     * Options d'éxecution et validation des arguments
     *
     * @return Parser $parser
     */
    public function getOptionParser() {
        $parser = parent::getOptionParser();
        $parser->description(__('Script configuration des utilisateurs.'));

        $parser->addSubcommand('AlerteMail', array(
            'help' => __('Gestion des paramètres d\'alertes par emails des utilisateurs'),
        ));

        $parser->addSubcommand('TypeActe', array(
            'help' => __('Gestion des droits sur les types d\'acte des utilisateurs'),
        ));


        return $parser;
    }

    public function AlerteMail() {
        $result = strtolower($this->in("Gestion des notifications d'alertes par email de tous les utilisateurs.\n"
                        . " [a] Ajouter une alerte\n [m] Modifier les alertes\n [q] Quitter\nQue voulez-vous faire?", array('a', 'm', 'q')));
        if ($result === 'a') {
            $this->_AjoutAlerteMail();
        } elseif ($result === 'm') {
            $this->_ModifAlerteMail();
        }
        return $this->_stop();
    }

    private function _ModifAlerteMail() {
        try {
            $this->User->begin();
            $promp = "Recevoir des alertes par email.\n "
                    . "[o] Oui (détails)\n "
                    . "[n] Non\n "
                    . "[t] Toutes les alertes\n [q] Quitter\n"
                    . "Que voulez-vous faire?";
            //Recevoir des alertes par email
            $alerte = strtolower($this->in($promp, array('o', 'n', 't', 'q'), 'o'));
            if ($alerte === 'q') {
                return $this->_stop();
            }
            if ($alerte === 'o') {
                $types = array(
                    'mail_insertion' => 'd\'insertion des projets',
                    'mail_refus' => 'des projets refusés',
                    'mail_traitement' => 'des traitements de validation en attente',
                    'mail_modif_projet_cree' => 'des projets modifiées',
                    'mail_modif_projet_valide' => 'des projets visés qui sont modifiés',
                    'mail_retard_validation' => 'des porjets en retard de validation');
                foreach ($types as $key => $type) {
                    $promp = "Recevoir des alertes par email $type ($key).\n "
                            . "[o] Oui\n "
                            . "[n] Non\n "
                            . "[q] Quitter\n"
                            . "Que voulez-vous faire?";
                    $result = strtolower($this->in($promp, array('o', 'n', 'q'), 'o'));
                    if ($result === 'q') {
                        return $this->_stop();
                    }
                    $data['User.' . $key] = $result === 'o' ? true : false;
                }
                $data['User.accept_notif'] = true;
                $this->User->updateAll($data);
                $success = true;
            } elseif ($alerte === 't') {
                $this->User->updateAll(array(
                    'User.accept_notif' => true,
                    'User.mail_refus' => true,
                    'User.mail_traitement' => true,
                    'User.mail_insertion' => true,
                    'User.mail_modif_projet_cree' => true,
                    'User.mail_modif_projet_valide' => true,
                    'User.mail_retard_validation' => true));
                $success = true;
            } else {
                $this->User->updateAll(array('User.accept_notif' => false));
                $success = true;
            }

            $this->User->commit();
        } catch (Exception $e) {
            $success = false;
            $this->User->rollback();
            $this->out("ERREUR : " . $e->getMessage());
        }

        if (empty($success))
            $this->footer('<error>Erreur : un problème est survenu durant la modification !!</error>');
        else
            $this->footer('<important>modfication accomplis avec succès !</important>');
    }

    private function _AjoutAlerteMail() {
        try {
            $this->User->begin();

            $promp = "Taper le code de l'alerte email à ajouter.\n "
                    . "[0] Recevoir les alertes par email\n "
                    . "[1] insertion dans un circuit\n "
                    . "[2] Traitement en attente\n "
                    . "[3] Projet refusé \n "
                    . "[4] Un de mes projets est modifié\n "
                    . "[5] Un projet que j'ai visé est modifié\n "
                    . "[6] Retard de validation\n [q] Quitter\nQuel type d'alerte voulez-vous traiter?";
            //Recevoir des alertes par email
            $result = strtolower($this->in($promp, array(0, 1, 2, 3, 4, 5, 6, 'q'), 'q'));
            if ($result === 'q') {
                return $this->_stop();
            }
            switch ($result) {
                case '1':
                    $data = array('User.mail_refus' => true);
                    break;
                case '2':
                    $data = array('User.mail_traitement' => true);
                    break;
                case '3':
                    $data = array('User.mail_insertion' => true);
                    break;
                case '4':
                    $data = array('User.mail_modif_projet_cree' => true);
                    break;
                case '5':
                    $data = array('User.mail_modif_projet_valide' => true);
                    break;
                case '6':
                    $data = array('User.mail_retard_validation' => true);
                    break;
                case '00':
                    $data = array('User.accept_notif' => true);
                    break;

                default:
                    throw new Exception('Code invalide');
                    break;
            }

            $this->User->updateAll($data);
            $success = true;
            $this->User->commit();
        } catch (Exception $e) {
            $success = false;
            $this->User->rollback();
            $this->out("ERREUR : " . $e->getMessage());
        }

        if (empty($success))
            $this->footer('<error>Erreur : un problème est survenu durant l\'ajout !!</error>');
        else
            $this->footer('<info>Ajout accomplis avec succès !</info>');
    }

    public function TypeActe() {
        $result = strtolower($this->in("Gestion des droits sur les types d\'acte de tous les utilisateurs.\n"
                        . " [a] Ajouter un type d'acte sur tous les utilisateurs\n [s] Supprimer un type d'acte sur tous les utilisateurs\n [q] Quitter\nQue voulez-vous faire?", array('a', 's', 'q')));
        if ($result === 'a') {
            $this->_AjoutTypeActe();
        } elseif ($result === 's') {
            $this->_SuppTypeActe();
        }
        return $this->_stop();
    }

    private function _AjoutTypeActe() {
        try {
            $this->ArosAdo->begin();
            $typeactes = $this->Typeacte->find('all', array(
                'fields' => 'id, libelle',
                'recursive' => -1)
            );
            $promp = "Liste des types d'acte à ajouter.\n ";
            foreach ($typeactes as $typeacte) {
                $promp .= '['.$typeacte['Typeacte']['id'].'] '.$typeacte['Typeacte']['libelle']."\n ";
                 $options[]=$typeacte['Typeacte']['id'];
                
            }
            $promp .= "[q] Quitter\nQuel type d'acte voulez-vous ajouter?";
            $options[]='q';
            $result = strtolower($this->in($promp, $options));
            if ($result === 'q') {
                return $this->_stop();
            }
            $aros = $this->Aro->find('all', array(
                'fields' => array('id'),
                'conditions' => array('model' => 'User'),
                'recursive' => -1));
            $ado = $this->Ado->find('first', array(
                            'fields' => array('Ado.id'),
                            'conditions' => array('Ado.model' => 'Typeacte',
                            'Ado.foreign_key' => $result),
                            'recursive' => -1));
            foreach ($aros as $aro) {
                 $this->ArosAdo->allow($aro['Aro']['id'], $ado['Ado']['id']);
            }
            $success = true;
            $this->ArosAdo->commit();
        } catch (Exception $e) {
            $success = false;
            $this->ArosAdo->rollback();
            $this->out("ERREUR : " . $e->getMessage());
        }

        if (empty($success))
            $this->footer('<error>Erreur : un problème est survenu durant l\'ajout !!</error>');
        else
            $this->footer('<info>Ajout accomplis avec succès !</info>');
    }
    
    private function _SuppTypeActe() {
        try {
            $this->ArosAdo->begin();
            $typeactes = $this->Typeacte->find('all', array(
                'fields' => 'id, libelle',
                'recursive' => -1)
            );
            $promp = "Liste des types d'acte à supprimer.\n ";
            foreach ($typeactes as $typeacte) {
                $promp .= '['.$typeacte['Typeacte']['id'].'] '.$typeacte['Typeacte']['libelle']."\n ";
                 $options[]=$typeacte['Typeacte']['id'];
                
            }
            $promp .= "[q] Quitter\nQuel type d'acte voulez-vous supprimer?";
            $options[]='q';
            $result = strtolower($this->in($promp, $options));
            if ($result === 'q') {
                return $this->_stop();
            }
            $aros = $this->Aro->find('all', array(
                'fields' => array('id'),
                'conditions' => array('model' => 'User'),
                'recursive' => -1));
            $ado = $this->Ado->find('first', array(
                            'fields' => array('Ado.id'),
                            'conditions' => array('Ado.model' => 'Typeacte',
                            'Ado.foreign_key' => $result),
                            'recursive' => -1));
            foreach ($aros as $aro) {
                 $this->ArosAdo->deny($aro['Aro']['id'], $ado['Ado']['id']);
            }
            $success = true;
            $this->ArosAdo->commit();
        } catch (Exception $e) {
            $success = false;
            $this->ArosAdo->rollback();
            $this->out("ERREUR : " . $e->getMessage());
        }

        if (empty($success))
            $this->footer('<error>Erreur : un problème est survenu durant la supression !!</error>');
        else
            $this->footer('<info>Suppression accomplis avec succès !</info>');
    }

    /**
     * Affiche un message entouré de deux barres horizontales
     * @param string $var message
     */
    public function footer($var) {
        $this->hr();
        $this->out($var);
        $this->hr();
    }

}
