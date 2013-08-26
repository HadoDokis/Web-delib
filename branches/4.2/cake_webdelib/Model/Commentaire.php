<?php

class Commentaire extends AppModel {

    var $name = 'Commentaire';
    var $validate = array('texte' => array(
            array('rule' => 'notEmpty',
                'message' => 'Entrer un commentaire.')));

    // -------------------------------------------------------------------------

    /**
     * Lecture des enregistrements
     *
     * @return array
     */
    public function gedoooReadAll($deliberation_id) {
        return $this->find('all', array(
                    'fields' => array('texte', 'commentaire_auto'),
                    'conditions' => array('Commentaire.delib_id' => $deliberation_id),
                    'recursive' => -1
        ));
    }

    /**
     * Normalisation des enregistrements se trouvant sous la clé 'Commentaire':
     * ajout des valeurs calculées, ...
     *
     * @param array $record
     * @return array
     */
    public function gedoooNormalizeAll(array $data) {
        $commentaires = $data['Commentaires'];
        unset($data['Commentaires']);

        $listeCommentaires = array();
        $listeAvisCommission = array();

        foreach ($commentaires as $commentaire) {
            if ($commentaire['Commentaire']['commentaire_auto']) {
                $listeAvisCommission[] = array('avis' => $commentaire['Commentaire']['texte']);
            } else {
                $listeCommentaires[] = array('avis' => $commentaire['Commentaire']['texte']);
            }
        }

        if (!empty($listeAvisCommission)) {
            $data['AvisCommission'] = $listeAvisCommission;
        }

        if (!empty($listeCommentaires)) {
            $data['Commentaires'] = $listeCommentaires;
        }

        return $data;
    }

    /**
     * Retourne une correspondance entre les champs CakePHP (même calculés)
     * et les champs Gedooo.
     *
     * @param array $records
     * @return array
     */
    public function gedoooPaths() {
        return array(
            'avis' => 'texte',
            'texte_commentaire' => 'texte',
        );
    }

    /**
     * Retourne une correspondance entre les champs CakePHP (même calculés)
     * et les types Gedooo.
     *
     * @param array $records
     * @return array
     */
    public function gedoooTypes() {
        return array(
            'texte' => 'text',
        );
    }

}

?>
