<?php
/**
 * Tache de mise à jour
 * Permet de passer simplement des fichiers sql à la base
 * Avec prise en charge transactionnelle
 */
App::uses('ConnectionManager', 'Model');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

class SqlTask extends Shell {

    protected $db;

    /**
     * Initialisation de l'objet ConnectionManager
     * (récupération de la connexion à la base de données à partir de database.php)
     */
    public function execute() {
        $this->db = ConnectionManager::getDataSource('default');
        if (!$this->db->isConnected()) {
            $this->out("<error>Impossible d'établir une connexion à la base de données. Veuillez vérifier vos paramètres dans le fichier app/Config/database.php et réessayer.</error>");
            return false;
        }
    }

    /**
     * @param $sqlpath chemin vers le fichier sql à executer
     * @return bool résultat de l'éxécution du sql
     */
    public function run($sqlpath) {
        $sqlfile = new File($sqlpath);

        if (!$sqlfile->exists()) {
            $this->out("<error>Patch sql $sqlpath introuvable, veuillez vous assurer d'avoir la dernière version des sources</error>");
            return false;
        }

        //Démarrage de la transaction
        try {
            //Lecture ligne par ligne (séparateur ;)
            $content = $sqlfile->read();
            $sqlfile->close();
            //Supprime les lignes de commentaire
            $content = preg_replace('/--.*\n/', '', $content);
            $sql = explode(';', $content);
            foreach ($sql as $sqlline) {
                //Suppression des espaces en début ou fin de ligne
                $line = trim($sqlline);
                //Saut des mots clés begin et commit (transaction déjà démarrée)
                if (!empty($line) && !in_array(strtolower($line), array('begin', 'commit')))
                    $this->db->rawQuery($line); //Execute la ligne sql
            }
            //Fin de la transaction, tout s'est bien passé
            return true;
        } catch (Exception $e) {
            //Fin de la transaction, une erreur a été rencontrée
            $this->out("<important>Erreur SQL : {$e->getMessage()}</important>");
            if (!empty($line))
                $this->out("<error>Requête en erreur : $line</error>");
            return false;
        }
    }


    /**
     * @param $sqlpath chemin vers le fichier sql à executer
     * @return bool résultat de l'éxécution du sql
     */
    public function runSkipErrors($sqlpath) {
        $sqlfile = new File($sqlpath);

        if (!$sqlfile->exists()) {
            $this->out("<error>Patch sql $sqlpath introuvable, veuillez vous assurer d'avoir la dernière version des sources</error>");
            return false;
        }

        //Lecture ligne par ligne (séparateur ;)
        $content = $sqlfile->read();
        $sqlfile->close();

        //Supprime les lignes de commentaire
        $content = preg_replace('/--.*\n/', '', $content);
        $sql = explode(';', $content);
        foreach ($sql as $sqlline) {
            //Suppression des espaces en début ou fin de ligne
            $line = trim($sqlline);
            //Saut des mots clés begin et commit (transaction déjà démarrée)
            if (!empty($line) && !in_array(strtolower($line), array('begin', 'commit'))) {
                try {
                    $this->db->rawQuery($line); //Execute la ligne sql
                } catch (Exception $e) {
                    //Fin de la transaction, une erreur a été rencontrée
                    $this->out("<important>Erreur SQL : {$e->getMessage()}</important>");
                    if (!empty($line))
                        $this->out("<error>Requête en erreur : $line</error>");
                }
            }
        }
        //Fin de la transaction, tout s'est bien passé
        return true;

    }

    /**
     * Démarrage de la transaction
     */
    public function begin() {
        $this->db->begin();
    }

    /**
     * Fin de la transaction, tout s'est bien passé
     */
    public function commit() {
        $this->db->commit();
    }

    /**
     * Fin de la transaction, une erreur a été rencontrée
     */
    public function rollback() {
        $this->db->rollback();
    }
}