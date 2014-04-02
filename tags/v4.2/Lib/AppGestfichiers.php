<?php

/**
 * Created on 12 jan. 2012
 *
 * Librairie regroupant des fonctions de gestion des fichiers
 * Gestion des transactions pour les fonctions creeRepertoire, copieFichiers, copieRepertoire, deplaceFichier, deplaceRepertoire, supprimeFichier, supprimeRepertoire
 *
 * utilisation : App::import('Lib', 'AppGestfichiers');
 *
 */
class AppGestfichiers {

// Constantes de la classe
    const TRANSACTION_SESSION_KEY = 'transactionGestFichier';
    const TRANSACTION_COMMIT_KEY = 'commit';
    const TRANSACTION_ROLLBACK_KEY = 'rollback';
    const TRANSACTION_ACTION_DEL = 'supprimer';
    const TRANSACTION_ACTION_RENAME = 'renommer';
    const TRANSACTION_TYPE_FILE = 'fichier';
    const TRANSACTION_TYPE_DIR = 'repertoire';

    /**
     * début de transaction
     * @param string $transactionSessionKey nom de la transaction en session (défaut = self::TRANSACTION_SESSION_KEY)
     */
    function begin($transactionSessionKey = self::TRANSACTION_SESSION_KEY) {
        // initialisation
        if (!isset($this->Session))
            $this->Session = new CakeSession();

        $this->Session->del($transactionSessionKey);
        $this->Session->write($transactionSessionKey, array(
            self::TRANSACTION_COMMIT_KEY => array(),
            self::TRANSACTION_ROLLBACK_KEY => array()));
    }

    /**
     * valide la transaction : suppression des fichiers de la section 'commit'
     * @param string $transactionSessionKey nom de la transaction en session (défaut = self::TRANSACTION_SESSION_KEY)
     */
    function commit($transactionSessionKey = self::TRANSACTION_SESSION_KEY) {
        // initialisation
        if (!isset($this->Session))
            $this->Session = new CakeSession();

        if (!self::_transactionEnCours($transactionSessionKey)) return;

        // lecture du commit en session
        $commits = $this->Session->read($transactionSessionKey . '.' . self::TRANSACTION_COMMIT_KEY);

        $nb = count($commits);
        for ($i = $nb - 1; $i >= 0; $i--) {
            $commit = $commits[$i];
            switch ($commit['action']) {
                case self::TRANSACTION_ACTION_DEL :
                    if ($commit['type'] == self::TRANSACTION_TYPE_FILE)
                        AppGestfichiers::_supprimeFichier($commit['element']);
                    else
                        AppGestfichiers::_supprimeRepertoire($commit['element']);
                    break;
                case self::TRANSACTION_ACTION_RENAME :
                    rename($commit['element']['from'], $commit['element']['to']);
                    break;
            }
        }

        $this->Session->del($transactionSessionKey);
    }

    /**
     * annule la transaction : suppression des fichiers et des répertoires de la section 'rollback'
     * @param string $transactionSessionKey nom de la transaction en session (défaut = self::TRANSACTION_SESSION_KEY)
     */
    function rollback($transactionSessionKey = self::TRANSACTION_SESSION_KEY) {
        // initialisation
        if (!isset($this->Session))
            $this->Session = new CakeSession();

        if (!self::_transactionEnCours($transactionSessionKey)) return;

        // lecture du rollback en session
        $rollbacks = $this->Session->read($transactionSessionKey . '.' . self::TRANSACTION_ROLLBACK_KEY);

        $nb = count($rollbacks);
        for ($i = $nb - 1; $i >= 0; $i--) {
            $rollback = $rollbacks[$i];
            switch ($rollback['action']) {
                case self::TRANSACTION_ACTION_DEL :
                    if ($rollback['type'] == self::TRANSACTION_TYPE_FILE)
                        self::_supprimeFichier($rollback['element']);
                    else
                        self::_supprimeRepertoire($rollback['element']);
                    break;
                case self::TRANSACTION_ACTION_RENAME :
                    rename($rollback['element']['from'], $rollback['element']['to']);
                    break;
            }
        }

        $this->Session->del($transactionSessionKey);
    }

    /**
     * retourne true si une transaction est en cours et false dans le cas contraire
     * @param string $transactionSessionKey nom de la transaction en session
     */
    function _transactionEnCours($transactionSessionKey) {
        // initialisation
        if (!class_exists('CakeSession'))
            return false;
        if (!isset($this->Session))
            $this->Session = new CakeSession();

        return $this->Session->check($transactionSessionKey);
    }


    /**
     * ajoute un élément au commit de la transaction en cours (rien sinon)
     * @param string $transactionSessionKey nom de la transaction en session
     * @param mixed $ele élément à ajouter au commit de la transaction
     * @param array $options tableau des parmètres optionnels :
     *        'type' : type de l'élément à ajouter au commit de la transaction (défaut = fichier)
     *        'action' : action a effectuer lors du commit (défaut = supprimer)
     */
    function _addToCommit($transactionSessionKey, $ele, $options = array()) {
        // initialisation
        if (!isset($this->Session) && class_exists('CakeSession')) $this->Session = new CakeSession();
        if (!self::_transactionEnCours($transactionSessionKey)) return;
        $defaultOptions = array(
            'type' => self::TRANSACTION_TYPE_FILE,
            'action' => self::TRANSACTION_ACTION_DEL);
        $options = array_merge($defaultOptions, $options);

        $sessionKey = $transactionSessionKey . '.' . self::TRANSACTION_COMMIT_KEY;
        $nbEle = count($this->Session->read($sessionKey));
        $newEle = array('action' => $options['action'], 'type' => $options['type'], 'element' => $ele);
        $this->Session->write($sessionKey . '.' . $nbEle, $newEle);
    }

    /**
     * ajoute un élément au rollback de la transaction en cours (rien sinon)
     * @param string $transactionSessionKey nom de la transaction en session
     * @param mixed $ele élément à ajouter au rollback de la transaction
     * @param array $options tableau des parmètres optionnels :
     *        'type' : type de l'élément à ajouter au rollback de la transaction (défaut = fichier)
     *        'action' : action a effectuer lors du rollback (défaut = supprimer)
     */
    function _addToRollback($transactionSessionKey, $ele, $options = array()) {
        // initialisation
        if (!isset($this->Session) && class_exists('CakeSession')) $this->Session = new CakeSession();
        if (!self::_transactionEnCours($transactionSessionKey)) return;
        $defaultOptions = array(
            'type' => self::TRANSACTION_TYPE_FILE,
            'action' => self::TRANSACTION_ACTION_DEL);
        $options = array_merge($defaultOptions, $options);

        $sessionKey = $transactionSessionKey . '.' . self::TRANSACTION_ROLLBACK_KEY;
        $nbEle = count($this->Session->read($sessionKey));
        $newEle = array('action' => $options['action'], 'type' => $options['type'], 'element' => $ele);
        $this->Session->write($sessionKey . '.' . $nbEle, $newEle);
    }

    /**
     * Ajoute un fichier à la transaction (génère une exception si le fichier n'existe pas sur le disque)
     * @param string $fileUri uri du fichier à ajouter à la transaction
     * @param array $options tableau des parmètres optionnels :
     *        'session' : string; précise si il faut ajouter au commit ou au rollback; défaut : self::TRANSACTION_ROLLBACK_KEY
     *        'transactionSessionKey' : string; nom de la transaction en session; défaut : self::TRANSACTION_SESSION_KEY
     */
    function ajouteFichierTransaction($fileUri, $options = array()) {
        try {
            // initialisation
            $defaultOptions = array(
                'session' => self::TRANSACTION_ROLLBACK_KEY,
                'transactionSessionKey' => self::TRANSACTION_SESSION_KEY);
            $options = array_merge($defaultOptions, $options);

            if (!is_file($fileUri))
                throw new Exception(__('fichier non trouvé', true) . ' ' . $fileUri);

            // ajout à la transaction
            if ($options['session'] == self::TRANSACTION_ROLLBACK_KEY)
                self::_addToRollback($options['transactionSessionKey'], $fileUri);
            else
                self::_addToCommit($options['transactionSessionKey'], $fileUri);
        } catch (Exception $e) {
            throw new Exception(__('Gestfichier::ajouteFichierTransaction', true) . ' : ' . $e->getMessage());
        }
    }

    /**
     * Ajoute un répertoire et/ou sont contenu à la transaction (génère une exception si le fichier n'existe pas sur le disque)
     * @param string $dirUri uri du répertoire à ajouter à la transaction
     * @param array $options tableau des parmètres optionnels :
     *        'session' : string, précise si il faut ajouter au commit ou au rollback (défaut = Rollback)
     *        'recursive' : boolean, indique si les sous répertoires sont traités (défaut = true)
     *        'includeFiles' : boolean, inclu les fichiers dans la transaction (défaut = true)
     *        'exludeHimself' : boolean, indique si le répertoire lui même doit être ajouté ou non à la transaction (défaut = false)
     *        'transactionSessionKey' : string; nom de la transaction en session; défaut : self::TRANSACTION_SESSION_KEY
     */
    function ajouteRepertoireTransaction($dirUri, $options = array()) {
        try {
            // initialisation
            $defaultOptions = array(
                'session' => self::TRANSACTION_ROLLBACK_KEY,
                'recursive' => true,
                'includeFiles' => true,
                'exludeHimself' => false,
                'transactionSessionKey' => self::TRANSACTION_SESSION_KEY);
            $options = array_merge($defaultOptions, $options);

            $dirUri = self::formatDirName($dirUri);
            if (!is_dir($dirUri))
                throw new Exception(__('répertoire non trouvé', true) . ' ' . $dirUri);

            // ajout du répertoire à la transaction
            if (!$options['exludeHimself']) {
                if ($options['session'] == self::TRANSACTION_ROLLBACK_KEY)
                    self::_addToRollback($options['transactionSessionKey'], $dirUri, array('type' => self::TRANSACTION_TYPE_DIR));
                else
                    self::_addToCommit($options['transactionSessionKey'], $dirUri, array('type' => self::TRANSACTION_TYPE_DIR));
            }

            if (!$options['recursive'] && !$options['includeFiles']) return;

            // traitement du contenu du répertoire
            $repDir = glob($dirUri . '*');
            foreach ($repDir as $fileDirUri) {
                if ($options['includeFiles'] && is_file($fileDirUri)) {
                    if ($options['session'] == self::TRANSACTION_ROLLBACK_KEY)
                        self::_addToRollback($options['transactionSessionKey'], $fileDirUri);
                    else
                        self::_addToCommit($options['transactionSessionKey'], $fileDirUri);
                }
                if ($options['recursive'] && is_dir($fileDirUri))
                    self::ajouteRepertoireTransaction($fileDirUri, array(
                        'session' => $options['session'],
                        'recursive' => $options['recursive'],
                        'includeFiles' => $options['includeFiles'],
                        'exludeHimself' => false,
                        'transactionSessionKey' => $options['transactionSessionKey']));
            }

        } catch (Exception $e) {
            throw new Exception(__('Gestfichier::ajouteRepertoireTransaction', true) . ' : ' . $e->getMessage());
        }
    }

    /**
     * Créé un fichier : ecrit son contenu sur le disque avec création du répertoire si il n'existe pas (genere une exception en cas d'erreur)
     * Le fonctionnement de cette fonction est affecté par la transaction :
     * - transaction en cours :
     *        - a l'exécution le fichier est créé
     *        - au commit : aucune action
     *        - au rollback : le fichier est supprimé
     * - hors transaction : le fichier est créé
     * @param string $fileUri uri du fichier à créer
     * @param string $transactionSessionKey nom de la transaction en session (defaut : TRANSACTION_SESSION_KEY)
     */
    function creeFichier($fileUri, $fileContent, $transactionSessionKey = self::TRANSACTION_SESSION_KEY) {
        try {
            // initialisation du repertoire de destination
            $rep = dirname($fileUri);
            if (!is_dir($rep))
                self::creeRepertoire($rep, $transactionSessionKey);

            // écriture du fichier
            if (file_put_contents($fileUri, $fileContent) === false)
                throw new Exception(__('Echec de la création du fichier', true) . ' ' . $fileUri);
            self::_addToRollback($transactionSessionKey, $fileUri);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Crée un répertoire (genere une excexption en cas d'erreur)
     * Le fonctionnement de cette fonction est affecté par la transaction :
     * - transaction en cours :
     *        - a l'exécution le répertoire est créé
     *        - au commit : aucune action
     *        - au rollback : le ou les répertoires créés sont supprimés
     * - hors transaction : le répertoire est créé
     * @param string $rep chemin complet du répertoire
     * @param string $transactionSessionKey nom de la transaction en session (defaut : TRANSACTION_SESSION_KEY)
     */
    function creeRepertoire($rep, $transactionSessionKey = self::TRANSACTION_SESSION_KEY) {
        try {
            // initialisation du repertoire de destination
            if (!is_dir($rep)) {
                while (strlen($rep) > 1 && substr($rep, -1) == DS) $rep = substr($rep, 0, -1);
                $repParts = explode(DS, $rep);
                $repACreer = '';
                foreach ($repParts as $repPart) {
                    $repACreer .= $repPart . DS;
                    if (!is_dir($repACreer)) {
                        mkdir($repACreer, 0777);
                        self::_addToRollback($transactionSessionKey, $repACreer, array('type' => self::TRANSACTION_TYPE_DIR));
                    }
                }
            }

            if (!is_dir($rep))
                throw new Exception(__('Echec de la création du repertoire', true) . ' : ' . $rep);

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Copie un fichier ver un autre fichier avec création du répertoire de destination si il n'existe pas (génère une exception en cas d'erreur)
     * Le fonctionnement de cette fonction est affecté par la transaction :
     * - transaction en cours :
     *        - a l'exécution le fichier est copié vers sa destination
     *        - au commit : aucune action
     *        - au rollback : le fichier de destination est supprimé ainsi que les répertoires éventuellement créés
     * - hors transaction : le fichier est copié vers sa destination
     * @param string $ficSourceUri chemin complet et nom du fichier à copier
     * @param string $ficDestUri chemin complet et nom du fichier de destination
     * @param string $transactionSessionKey nom de la transaction en session (defaut : TRANSACTION_SESSION_KEY)
     * @return booleen true si la copie s'est bien effectuée, false dans le cas contraire
     */
    function copieFichier($ficSourceUri, $ficDestUri, $transactionSessionKey = self::TRANSACTION_SESSION_KEY) {
        // initialisation
        $ret = false;

        try {
            // initialisation du repertoire de destination
            $repDest = dirname($ficDestUri);
            if (!is_dir($repDest))
                self::creeRepertoire($repDest, $transactionSessionKey);

            // copie du fichier
            if (!copy($ficSourceUri, $ficDestUri))
                throw new Exception(__('Echec de la copie du fichier', true) . ' ' . $ficSourceUri . ' ' . __('vers', true) . ' ' . $ficDestUri);
            if (!is_file($ficDestUri))
                throw new Exception(__('Echec de la copie du fichier', true) . ' ' . $ficSourceUri . ' ' . __('vers', true) . ' ' . $ficDestUri);
            if (self::calcHashFile($ficSourceUri, 'SHA256') != self::calcHashFile($ficDestUri, 'SHA256')) {
                self::_supprimeFichier($ficDestUri);
                throw new Exception(__('Empreinte du fichier', true) . ' : ' . $ficSourceUri . ' ' . __('différente de l\'empreinte du fichier', true) . ' : ' . $ficDestUri);
            }
            self::_addToRollback($transactionSessionKey, $ficDestUri);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        return true;
    }

    /**
     * Copie la totalité d'un répertoire vers un autre répertoire (génère une exception en cas d'erreur)
     * Le fonctionnement de cette fonction est affecté par la transaction :
     * - transaction en cours :
     *        - a l'exécution les fichiers du répertoire et sous-réperoires sont copiés vers leurs destination
     *        - au commit : aucune action
     *        - au rollback : les fichiers de destination sont supprimés ainsi que les répertoires éventuellement créés
     * - hors transaction : le fichier est copié vers sa destination
     * @param string $repSourceUri chemin complet du répertoire à copier
     * @param string $repDestUri chemin complet du répertoire de destination
     * @param string $transactionSessionKey nom de la transaction en session (defaut : TRANSACTION_SESSION_KEY)
     */
    function copieRepertoire($repSourceUri, $repDestUri, $transactionSessionKey = self::TRANSACTION_SESSION_KEY) {
        try {
            // initialisations
            $repSourceUri = self::formatDirName($repSourceUri);
            $repDestUri = self::formatDirName($repDestUri);

            // vérification du répertoire source
            if (!is_dir($repSourceUri))
                throw new Exception('GestFichier::copieRepertoire : le répertoire à copier ' . $repSourceUri . ' n\'existe pas');

            // lecture des fichiers du répertoire
            $fichiersSource = glob($repSourceUri . '*');

            foreach ($fichiersSource as $fileSourceUri) {
                $fileDestUri = str_replace($repSourceUri, $repDestUri, $fileSourceUri);
                if (is_file($fileSourceUri)) {
                    // copie
                    self::copieFichier($fileSourceUri, $fileDestUri, $transactionSessionKey);
                } else {
                    // traitement des sous repertoires
                    self::copieRepertoire($fileSourceUri, $fileDestUri, $transactionSessionKey);
                }
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    /**
     * Déplace un fichier vers un autre fichier (génère une exception en cas d'erreur)
     * Le fonctionnement de cette fonction est affecté par la transaction :
     * - transaction en cours :
     *        - a l'exécution le fichier est copié vers sa destination
     *        - au commit : le fichier source est supprimé
     *        - au rollback : le fichier de destination est supprimé ainsi que les répertoires éventuellement créés
     * - hors transaction : le fichier est copié vers sa destination puis supprimé
     * @param string $ficSourceUri chemin complet et nom du fichier à copier
     * @param string $ficDestUri chemin complet et nom du fichier de destination
     * @param string $transactionSessionKey nom de la transaction en session (defaut : TRANSACTION_SESSION_KEY)
     */
    function deplaceFichier($ficSourceUri, $ficDestUri, $transactionSessionKey = self::TRANSACTION_SESSION_KEY) {
        try {
            self::copieFichier($ficSourceUri, $ficDestUri, $transactionSessionKey);
            self::supprimeFichier($ficSourceUri, $transactionSessionKey);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Déplace un fichier temporaire uploadé par forulaire vers un autre fichier (génère une exception en cas d'erreur)
     * Le fonctionnement de cette fonction est affecté par la transaction :
     * - transaction en cours :
     *        - a l'exécution le fichier temporaire est déplacé vers sa destination
     *        - au rollback : le fichier de destination est supprimé ainsi que les répertoires éventuellement créés
     * - hors transaction : le fichier temporaire est déplacé vers sa destination
     * @param string $tmpFicTeleUri chemin complet et nom du fichier temporaire téléchargé
     * @param string $ficDestUri chemin complet et nom du fichier de destination
     * @param string $transactionSessionKey nom de la transaction en session (defaut : TRANSACTION_SESSION_KEY)
     */
    function deplaceFichierTelecharge($tmpFicTeleUri, $ficDestUri, $transactionSessionKey = self::TRANSACTION_SESSION_KEY) {
        try {
            move_uploaded_file($tmpFicTeleUri, $ficDestUri);
            if (!is_file($ficDestUri))
                throw new Exception(__('Erreur lors de l\'écriture du fichier téléchargé : fichier copié introuvable', true));
            self::ajouteFichierTransaction($ficDestUri, array('transactionSessionKey' => $transactionSessionKey));
            if (filesize($ficDestUri) == 0)
                throw new Exception(__('Erreur lors de l\'écriture du fichier téléchargé : fichier copié avec une taille nulle', true));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Déplace un répertoire vers un autre répertoire (génère une exception en cas d'erreur)
     * Le fonctionnement de cette fonction est affecté par la transaction :
     * - transaction en cours :
     *        - a l'exécution le répertoire est copié vers sa destination
     *        - au commit : le répertoire source est supprimé
     *        - au rollback : le répertoire de destination est supprimé
     * - hors transaction : le répertoire est copié vers sa destination puis supprimé
     * @param string $ficSourceUri chemin complet et nom du fichier à copier
     * @param string $ficDestUri chemin complet et nom du fichier de destination
     * @param string $transactionSessionKey nom de la transaction en session (defaut : TRANSACTION_SESSION_KEY)
     */
    function deplaceRepertoire($repSourceUri, $repDestUri, $transactionSessionKey = self::TRANSACTION_SESSION_KEY) {
        try {
            self::copieRepertoire($repSourceUri, $repDestUri, $transactionSessionKey);
            self::supprimeRepertoire($repSourceUri, $transactionSessionKey);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Renomme un fichier (génère une exception en cas d'erreur)
     * Le fonctionnement de cette fonction est affecté par la transaction :
     * - transaction en cours :
     *        - a l'exécution : le fichier est renommé
     *        - au commit : aucune action
     *        - au rollback : le fichier est renommé avec son ancien nom
     * - hors transaction : le fichier est renommé
     * @param string $fileUri chemin complet et nom du fichier à renommer
     * @param string $newFileUri chemin complet et nom du nouveau fichier
     * @param string $transactionSessionKey nom de la transaction en session (defaut : TRANSACTION_SESSION_KEY)
     */
    function renommeFichier($fileUri, $newFileUri, $transactionSessionKey = self::TRANSACTION_SESSION_KEY) {
        try {
            // contrôle de la présence du fichier
            if (!is_file($fileUri))
                throw new Exception(__('Echec du renommage du fichier', true) . ' ' . $fileUri . ' : ' . __('non trouvé', true));

            // initialisation du nouveau répertoire
            $repDest = dirname($newFileUri);
            if (!is_dir($repDest))
                self::creeRepertoire($repDest, $transactionSessionKey);

            // renommage du fichier
            if (!rename($fileUri, $newFileUri))
                throw new Exception(__('Echec du renommage du fichier', true) . ' ' . $fileUri . ' ' . __('en', true) . ' ' . $newFileUri);
            self::_addToRollback($transactionSessionKey, array('from' => $newFileUri, 'to' => $fileUri), array('action' => self::TRANSACTION_ACTION_RENAME));
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Supprime un fichier (génère une exception en cas d'erreur)
     * Le fonctionnement de cette fonction est affecté par la transaction :
     * - transaction en cours :
     *        - a l'exécution on vérifie que l'on a les droits pour supprimer le fichier (writable)
     *        - au commit : le fichier est supprimé
     *        - au rollback : aucune action
     * - hors transaction : le fichier est supprimé
     * @param string $ficSourceUri chemin complet et nom du fichier à copier
     * @param string $ficDestUri chemin complet et nom du fichier de destination
     * @param string $transactionSessionKey nom de la transaction en session (defaut : TRANSACTION_SESSION_KEY)
     */
    function supprimeFichier($fileUri, $transactionSessionKey = self::TRANSACTION_SESSION_KEY) {
        try {
            if (!is_file($fileUri))
                throw new Exception('Gestfichier::SupprimeFichier : le fichier ' . $fileUri . ' n\'existe pas');

            if (!is_writable($fileUri))
                throw new Exception('Gestfichier::SupprimeFichier : les droits du fichier ' . $fileUri . ' n\'autorise pas sa suppression');

            // gestion de la transaction
            if (self::_transactionEnCours($transactionSessionKey))
                self::_addToCommit($transactionSessionKey, $fileUri);
            else
                self::_supprimeFichier($fileUri);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Supprime un repertoire et tous les sous-repertoires (génère une exception en cas d'erreur)
     * Le fonctionnement de cette fonction est affecté par la transaction :
     * - transaction en cours :
     *        - a l'exécution on vérifie que l'on a les droits pour supprimer les rep et les fichiers (writable)
     *        - au commit : les repertoires et les fichiers est supprimés
     *        - au rollback : aucune action
     * - hors transaction : le repertoire est supprimé
     * @param string $repUri chemin complet du répertoire à supprimer
     * @param string $transactionSessionKey nom de la transaction en session (defaut : TRANSACTION_SESSION_KEY)
     */
    function supprimeRepertoire($repUri, $transactionSessionKey = self::TRANSACTION_SESSION_KEY) {
        if (empty($repUri) || $repUri == '/') return;

        try {
            // initialisation
            $repUri = self::formatDirName($repUri);

            if (!is_dir($repUri))
                throw new Exception('Gestfichier::supprimeRepertoire : le répertoire ' . $repUri . ' n\'existe pas');

            if (!is_writable($repUri))
                throw new Exception('Gestfichier::supprimeRepertoire : les droits du répertoire ' . $repUri . ' n\'autorise pas sa suppression');

            // gestion de la transaction
            if (self::_transactionEnCours($transactionSessionKey))
                self::_addToCommit($transactionSessionKey, $repUri, array('type' => self::TRANSACTION_TYPE_DIR));

            // suppression des fichiers du répertoire et des sous répertoires
            $fichiers = glob($repUri . '*');
            foreach ($fichiers as $fichier) {
                if (is_file($fichier)) {
                    // gestion de la transaction
                    if (self::_transactionEnCours($transactionSessionKey))
                        self::_addToCommit($transactionSessionKey, $fichier, array('type' => self::TRANSACTION_TYPE_FILE));
                    else
                        self::_supprimeFichier($fichier);
                } else {
                    // traitement des sous repertoires
                    self::supprimeRepertoire($fichier, $transactionSessionKey);
                }
            }

            // gestion de la transaction
            if (!self::_transactionEnCours($transactionSessionKey))
                self::_supprimeRepertoire($repUri);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * formate un nom de répertoire et le crée si le paramètre $createDir=true
     * @param string||array $dirName nom ou composition du répertoire
     * @return string chemin du répertoire
     */
    function formateRepertoire($dirName, $createDir = false) {
        // initialisation
        $ret = '';
        $doubleDS = DS . DS;

        if (empty($dirName))
            return $ret;

        if (is_array($dirName))
            $ret = implode(DS, $dirName);
        else
            $ret = $dirName;

        $ret = str_replace(array('/', '\\'), DS, $ret);

        $posDoubleDS = strpos($ret, $doubleDS);
        while ($posDoubleDS !== false) {
            $ret = str_replace($doubleDS, DS, $ret);
            $posDoubleDS = strpos($ret, $doubleDS);
        }

        if (substr($ret, -1) != DS)
            $ret .= DS;

        if ($createDir && !is_dir($ret))
            mkdir($ret, 0777, true);

        return $ret;
    }

    /**
     * formate l'uri d'un fichier et le crée son répertoire si le paramètre $createDir=true
     * @param string||array $uriFichier nom ou composition du fichier
     */
    function formateUriFichier($uriFichier, $createDir = false) {
        // initialisation
        $ret = '';
        $doubleDS = DS . DS;

        if (empty($uriFichier))
            return $ret;

        if (is_array($uriFichier))
            $ret = implode(DS, $uriFichier);
        else
            $ret = $uriFichier;

        $ret = str_replace(array('/', '\\'), DS, $ret);

        $posDoubleDS = strpos($ret, $doubleDS);
        while ($posDoubleDS !== false) {
            $ret = str_replace($doubleDS, DS, $ret);
            $posDoubleDS = strpos($ret, $doubleDS);
        }

        if ($createDir) {
            $repFichier = dirname($ret);
            if (!is_dir($repFichier)) mkdir($repFichier, 0777, true);
        }

        return $ret;
    }


    function my_mime_content_type($filename) {
        // initialisations
        $ret = '';

        if (function_exists('mime_content_type')) {
            $ret = mime_content_type($filename);
            if (strpos($ret, 'corrupt') === false)
                return $ret;
        }
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $ret = finfo_file($finfo, $filename);
            finfo_close($finfo);
            if (strpos($ret, 'corrupt') === false)
                return $ret;
        }

        $mime_types = array(
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        $ext = strtolower(array_pop(explode('.', $filename)));
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        } else {
            return 'application/octet-stream';
        }
    }

    /**
     * crée un sous répertoire temporaire dans un répertoire existant en retourne le chemin
     * @param string $dirNameRoot uri du répertoire dans lequel sera créé le sous répertoire temporaire
     * @return string nom du réperoire créé ou false si pb lors de la création durépertoire
     */
    function createTmpDir($dirNameRoot) {
        // initialisations
        if (!is_array($dirNameRoot)) $dirNameRoot = func_get_args();
        $ret = '';
        $masque = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $maxRand = strlen($masque) - 1;

        // formatage du nom du répertoire
        $dirName = self::formatDirName($dirNameRoot);

        // constitution du nom aléatoire
        $tmpDir = '';
        for ($i = 0; $i < 8; $i++) $tmpDir .= $masque{mt_rand(0, $maxRand)};

        while (is_dir($dirName . $tmpDir)) {
            $tmpDir = '';
            for ($i = 0; $i < 8; $i++) $tmpDir .= $masque{mt_rand(0, $maxRand)};
        }

        $ret = $dirName . $tmpDir . DS;
        try {
            self::creeRepertoire($ret);
        } catch (Exception $e) {
            return false;
        }
        return $ret;
    }

    /**
     * Supprime un répertoire et son contenu
     * @param string $dirName chemin du répertoire à supprimer
     * @return boolean true si effectué, false en cas de problème
     */
    function clearDir($dirName) {
        // initialisations
        $dirName = self::formateRepertoire($dirName);

        if (empty($dirName) || $dirName === 'DS')
            return false;

        // supression du contenu
        $ouverture = @opendir($dirName);
        if (!$ouverture) return;
        while ($fichier = readdir($ouverture)) {
            if ($fichier == '.' || $fichier == '..') continue;
            $curFichier = $dirName . $fichier;
            if (is_dir($curFichier)) {
                $r = self::clearDir($curFichier);
                if (!$r) return false;
            } else {
                $r = @unlink($curFichier);
                if (!$r) return false;
            }
        }
        closedir($ouverture);

        // suppression du répertoire
        $r = @rmdir($dirName);
        return $r;
    }

    /**
     * Renvoi le hash du fichier en fonction du hashtype
     * @param string $fileUri : chemin et nom du fichier
     * @param string $hashType : algorithm de hashage du fichier
     * @param string $hashValue : valeur du hash à comparer
     * @return boolean true si le hash du fichier correspond au hash passé en paramètre
     */
    function checkHashFile($fileUri, $hashType, $hashValue) {
        $hash = self::calcHashFile($fileUri, $hashType);
        return ($hashValue == $hash);
    }

    /**
     * Calcul le hash du fichier en fonction du hashtype
     * @param string $fileUri : chemin et nom du fichier
     * @param string $hashType : algorithm de hashage du fichier
     * @return string hash du fichier, vide si fichier inexistant
     */
    function calcHashFile($fileUri, $hashType) {
        // initialisations
        $ret = '';

        if (!is_file($fileUri))
            return $ret;

        return hash_file($hashType, $fileUri);
    }


    /**
     * effectue la suppression d'un repertoire. Emet une exception en cas d'erreur
     * @param string $repUri chemin complet et nom du fichier à supprimer
     */
    function _supprimeRepertoire($repUri) {
        try {
            rmdir($repUri);

            if (is_dir($repUri))
                throw new Exception(__('erreur lors de la suppression du répertoire', true) . ' : ' . $repUri);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * effectue la suppression définitive d'un fichier. Emet une exception en cas d'erreur
     * @param string $ficASupprimerUri chemin complet et nom du fichier à supprimer
     */
    function _supprimeFichier($fileUri) {
        // initialisations
        $result = '';

        try {
            // initialisation du type de l'outil de suppression définitive
            if (DS == '/') {
                $cmd = 'shred --force --remove --zero ' . escapeshellarg($fileUri);
                setlocale(LC_ALL, 'fr_FR.UTF-8');
                putenv('LC_ALL=fr_FR.UTF-8');
                $result = shell_exec($cmd);
            } else {
                unlink($fileUri);
            }

            if (file_exists($fileUri))
                throw new Exception(__('erreur lors de la suppression définitive du fichier', true) . ' \'' . $fileUri . '\' : ' . $result);

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * formate le nom d'un répertoire : commence par DS (linux uniquement) et finit par DS
     * @param string|array $dirNames éléments ou chemin du répertoire
     */
    function formatDirName($dirNames) {
        $ret = '';
        if (!is_array($dirNames)) $dirNames = func_get_args();
        $dirName = (DS == '/' ? DS : '') . implode(DS, $dirNames) . DS;
        for ($index = 0, $max_count = strlen($dirName), $lastCar = ''; $index < $max_count; $index++) {
            $car = $dirName{$index};
            if ($car == '/' || $car == '\\') $car = DS;
            if ($car != DS || $lastCar != DS) $ret .= $car;
            $lastCar = $car;
        }
        return $ret;
    }

    /**
     * formate l'uri d'un fichier : commence par DS (linux uniquement)
     * @param string|array $fileUri chemin du fichier
     */
    function formatFileUri($fileUri) {
        if (!is_array($fileUri)) $fileUri = func_get_args();
        $ret = implode(DS, $fileUri);
        $ret = str_replace(array('\\', '/', '\\\\', '\\/', '/\\', '//'), DS, $ret);
        if (DS == '/' && substr($ret, 0, 1) != '/')
            $ret = '/' . $ret;
        return $ret;
    }

    /**
     * détermine si deux fichiers sont identiques ou non
     * @param string $fileUri1 uri du premier fichier à comparer
     * @param string $fileUri2 uri du deuxième fichier à comparer
     * @return boolean true si les fichiers sont identiques, false dans la cas contraire
     */
    function sontIdentiques($fileUri1, $fileUri2) {
        if (filesize($fileUri1) != filesize($fileUri2)) return false;
        if (hash_file('sha256', $fileUri1) != hash_file('sha256', $fileUri2)) return false;
        return true;
    }


    /**
     * décompresse une archive zip ou targz dans un répertoire existant (génère une exception en cas d'erreur)
     * Le fonctionnement de cette fonction est affecté par la transaction :
     * - transaction en cours :
     *        - a l'appel : les fichiers sont décompressés
     *        - au commit : aucune action
     *        - au rollback : les fichiers sont supprimés
     * - hors transaction : les fichiers sont décompressés
     * @param string $archiveFileUri uri du fichier compressé
     * @param string $destDirName répertoire de destination (doit exister)
     * @param string $transactionSessionKey nom de la transaction en session (defaut : TRANSACTION_SESSION_KEY)
     */
    function decompresse($archiveFileUri, $destDirName, $transactionSessionKey = self::TRANSACTION_SESSION_KEY) {
        try {
            // initialisation
            $destDirName = self::formatDirName($destDirName);
            $erreurs = array();

            // contrôles
            if (!is_file($archiveFileUri))
                throw new Exception('le fichier ' . $archiveFileUri . ' n\'existe pas');
            if (!is_dir($destDirName))
                throw new Exception('le répertoire ' . $destDirName . ' n\'existe pas');
            if (!is_writable($destDirName))
                throw new Exception('les droits du répertoire ' . $destDirName . ' n\'autorise pas la décompression du fichier');

            // type du fichier archive
            $filename = basename($archiveFileUri);
            if (substr($filename, -6) == 'tar.gz')
                $extension = 'tar.gz';
            elseif (substr($filename, -3) == 'tgz')
                $extension = 'tgz';
            elseif (substr($filename, -3) == 'zip')
                $extension = 'zip';
            else
                $extension = (strpos($filename, '.') === false) ? '' : substr($filename, strpos($filename, '.') + 1);

            switch ($extension) {
                case 'zip' :
                    // liste des fichiers de l'archive
                    App::import('Vendor', 'pclzip', array('file' => 'pclzip.lib.php'));
                    $zip = new PclZip($archiveFileUri);
                    if (($extractedFiles = $zip->extract($destDirName)) == 0)
                        $erreurs[] = __('erreur lors de la décompression des fichiers contenu dans', true) . ' ' . $archiveFileUri . ', ' . $zip->errorInfo(true);
                    else
                        foreach ($extractedFiles as $extractedFile) {
                            if (!file_exists($extractedFile['filename']))
                                $erreurs[] = __('erreur du fichier', true) . ' ' . $extractedFile['stored_filename'] . ', ' . __('non dezzipé', true);
                            if ($extractedFile['status'] != 'ok')
                                $erreurs[] = __('erreur du fichier', true) . ' ' . $extractedFile['stored_filename'] . ', ' . $extractedFile['status'];
                        }
                    break;
                case 'tgz' :
                case 'tar.gz' :
                    App::import('Vendor', 'pcltar', array('file' => 'pcltar.lib.php'));
                    $extractedFiles = PclTarExtract($archiveFileUri, $destDirName);
                    foreach ($extractedFiles as $extractedFile) {
                        if (!file_exists($extractedFile['filename']))
                            $erreurs[] = __('erreur du fichier', true) . ' ' . $extractedFile['stored_filename'] . ', ' . __('non dezzipé', true);
                        if ($extractedFile['status'] != 'ok')
                            $erreurs[] = __('erreur du fichier', true) . ' ' . $extractedFile['stored_filename'] . ', ' . $extractedFile['status'];
                    }
                    break;
                default :
                    $erreurs[] = __('extension du fichier archive non gérée', true);
            }
            // ajout des fichiers extraits dans la transaction
            self::ajouteRepertoireTransaction($destDirName, array('exludeHimself' => true, 'transactionSessionKey' => $transactionSessionKey));

            // gestion des erreurs
            if (!empty($erreurs))
                throw new Exception(implode("\n", $erreurs));
        } catch (Exception $e) {
            throw new Exception(__('Gestfichier::decompresse', true) . ' : ' . $e->getMessage());
        }
    }

}
