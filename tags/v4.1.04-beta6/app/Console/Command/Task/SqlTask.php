<?php
/**
 * Tache de mise à jour
 * Permet de passer simplement des fichiers sql à la base
 * Avec prise en charge transactionnelle
 */
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
class SqlTask extends Shell
{

    protected $db;

    /**
     * Initialisation de l'objet ConnectionManager
     * (récupération de la connexion à la base de données à partir de database.php)
     */
    public function execute()
    {
        $this->db = ConnectionManager::getDataSource('default');
    }

    /**
     * @param $sqlpath chemin vers le fichier sql à executer
     * @return bool résultat de l'éxécution du sql
     */
    public function run($sqlpath)
    {
        $sqlfile = new File($sqlpath);

        if (!$sqlfile->exists()) {
            $this->out("<error>Patch sql $sqlpath introuvable, veuillez vous assurer d'avoir la dernière version des sources</error>");
            return false;
        }

        //Démarrage de la transaction
//        $this->db->begin();
        try {
            //Lecture ligne par ligne (séparateur ;)
            $sql = explode(';', $sqlfile->read());
            foreach ($sql as $sqlline) {
                //Suppression des espaces en début ou fin de ligne
                $line = trim($sqlline);
                //Saut des mots clés begin et commit (transaction déjà démarrée)
                if (!empty($line) && !in_array(strtolower($line), array('begin', 'commit')))
                    $this->db->rawQuery($line); //Execute la ligne sql
            }
            //Fin de la transaction, tout s'est bien passé, commit
//            $this->db->commit();
            return true;
        } catch (Exception $e) {
            //Fin de la transaction, une erreur a été rencontrée, rollback
            $this->out("<error>Erreur sql (ligne " . $e->getLine() . ")</error>");
            $this->out($e->getMessage());
//            $this->db->rollback();
            return false;
        }
    }

    /**
     * Démarrage de la transaction
     */
    public function begin(){
        $this->db->begin();
    }

    /**
     * Fin de la transaction, tout s'est bien passé
     */
    public function commit(){
        $this->db->commit();
    }

    /**
     * Fin de la transaction, une erreur a été rencontrée
     */
    public function rollback(){
        $this->db->rollback();
    }
}