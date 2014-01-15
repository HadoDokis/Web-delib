<?php
/**
 * Application: webdelib / Adullact.
 * Date: 14/01/14
 * @author: Florian Ajir <florian.ajir@adullact.org>
 * @license CeCiLL V2 <http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html>
 */
App::uses('Cron', 'Model');
class CronJob extends AppModel
{

    public $useTable = false;

    /**
     * Mise à jour des dossiers en attente de signature dans le i-parapheur connecté
     * @return string
     */
    public function signatureJob()
    {
        App::uses('Deliberation', 'Model');
        $this->Deliberation = new Deliberation();
        return $this->Deliberation->majActesParapheur();
    }

    /**
     * Mise à jour des projets en délégation dans le i-parapheur connecté
     * @return string
     */
    public function delegationJob()
    {
        App::uses('Traitement', 'Cakeflow.Model');
        $this->Traitement = new Traitement();
        return $this->Traitement->majTraitementsParapheur();
    }

    /**
     * @return string
     */
    public function retardCakeflowJob()
    {
        try {
            $users = array();
            //Import des modèles
            App::uses('Traitement', 'Cakeflow.Model');
            App::uses('User', 'Model');
            App::uses('Deliberation', 'Model');
            $this->Traitement = new Traitement();
            $this->Deliberation = new Deliberation();
            $this->User = new User();

            //Paramètrage des mails
            App::uses('CakeEmail', 'Network/Email');
            $config_mail = Configure::read('SMTP_USE') ? 'smtp' : 'default';
            $this->Traitement->Behaviors->attach('Containable');
            $traitements = $this->Traitement->find('all', array(
                'contain' => array(
                    'Visa' => array(
                        'conditions' => array(
                            'not' => array('Visa.date_retard' => null),
                            'Visa.date_retard <=' => date('Y-m-d H:i:s'),
                        ),
                        'fields' => array('Visa.id', 'Visa.date_retard', 'Visa.numero_traitement', 'Visa.trigger_id', 'Visa.action'),
                    )
                ),
                'fields' => array('Traitement.id', 'Traitement.numero_traitement', 'Traitement.target_id'),
                'conditions' => array('treated' => false)
            ));

            foreach ($traitements as $traitement) {

                if (empty($traitement['Visa'])) continue;

                foreach ($traitement['Visa'] as $visa) {

                    // Si le visa ne respecte pas ces conditions, on passe au suivant
                    if (empty($visa['date_retard'])
                        || $visa['date_retard'] > date(Cron::FORMAT_DATE)
                        || $visa['action'] != 'RI'
                        || $traitement['Traitement']['numero_traitement'] != $visa['numero_traitement']
                    ) continue;

                    // Trouver l'utilisateur
                    $user = $this->User->find('first', array('conditions' => array('User.id' => $visa['trigger_id'])));
                    // Si l'utilisateur existe et accepte les alertes de retard
                    if (!empty($user) && $user['User']['accept_notif'] && $user['User']['mail_retard_validation']) {

                        $delib_id = $traitement['Traitement']['target_id'];

                        if (!in_array($user['User']['nom'] . ' ' . $user['User']['prenom'], $users))
                            $users[] = $user['User']['nom'] . ' ' . $user['User']['prenom'];

                        $delib = $this->Deliberation->find('first', array(
                            'conditions' => array('Deliberation.id' => $delib_id),
                            'fields' => array('Deliberation.id', 'Deliberation.objet', 'Deliberation.titre', 'Deliberation.circuit_id')
                        ));

                        $handle = fopen(CONFIG_PATH . '/emails/retard.txt', 'r');
                        $content = fread($handle, filesize(CONFIG_PATH . '/emails/retard.txt'));

                        $addrTraiter = FULL_BASE_URL . "/deliberations/traiter/" . $delib['Deliberation']['id'];

                        $searchReplace = array(
                            "#NOM#" => $user['User']['nom'],
                            "#PRENOM#" => $user['User']['prenom'],
                            "#IDENTIFIANT_PROJET#" => $delib['Deliberation']['id'],
                            "#OBJET_PROJET#" => $delib['Deliberation']['objet'],
                            "#TITRE_PROJET#" => $delib['Deliberation']['titre'],
                            "#LIBELLE_CIRCUIT#" => $this->Traitement->Circuit->getLibelle($delib['Deliberation']['circuit_id']),
                            "#ADRESSE_A_TRAITER#" => $addrTraiter,
                        );

                        $mail_content = str_replace(array_keys($searchReplace), array_values($searchReplace), $content);

                        $this->Email = new CakeEmail($config_mail);
                        $this->Email->to($user['User']['email']);
                        $this->Email->subject("Retard sur le projet : $delib_id");
                        $this->Email->send($mail_content);
                    }
                }
            }
            if (empty($users))
                return Cron::MESSAGE_FIN_EXEC_SUCCES . "Aucun utilisateur en retard";
            else
                return Cron::MESSAGE_FIN_EXEC_SUCCES . "Utilisateurs alertés :\n\n" . implode(",\n", $users);
        } catch (Exception $e) {
            return Cron::MESSAGE_FIN_EXEC_ERROR . $e->getMessage();
        }
    }

    /**
     * Tâche planifiée S2low
     */
    public function s2lowJob()
    {
        //Si service désactivé ==> quitter
        if (!Configure::read('USE_S2LOW')) {
            return Cron::MESSAGE_FIN_EXEC_ERROR . "\nService S2LOW désactivé";
        }
        try {
            //Initialisations
            App::uses('ComponentCollection', 'Controller');
            App::uses('S2lowComponent', 'Controller/Component');
            App::uses('Acteurseance', 'Model');
            $collection = new ComponentCollection();
            $this->S2low = new S2lowComponent($collection);
            $this->Acteurseance = new Acteurseance();

            $mails = $this->Acteurseance->find('all', array(
                'recursive' => -1,
// TODO: inclure dans le rapport : les acteurs concernés
// 'contain' => array('Acteur'),
                'conditions' => array(
                    'date_envoi !=' => null,
                    'date_reception' => null
                )));
            foreach ($mails as $mail) {
                $this->Acteurseance->id = $mail['Acteurseance']['id'];
                $mail_id = $mail['Acteurseance']['mail_id'];
                $infos = $this->S2low->checkMail($mail_id);
                $debut = strpos($infos, 'mailTo:t:');
                $tmp = substr($infos, $debut + strlen('mailTo:t:'), strlen($infos));
                $fin = strpos($tmp, '==message==');
                $info = trim(substr($tmp, 0, $fin));
                if ($debut === false)
                    continue;
                else {
                    $this->Acteurseance->saveField('date_reception', $info);
                }
            }
            return Cron::MESSAGE_FIN_EXEC_SUCCES;
        } catch (Exception $e) {
            return Cron::MESSAGE_FIN_EXEC_ERROR . $e->getMessage();
        }
    }

}