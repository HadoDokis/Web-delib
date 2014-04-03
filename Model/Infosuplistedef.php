<?php
/**
 * définitions des informations supplémentaires paramétrables des projets de délibération
 *
 * PHP versions 4 and 5
 * @link         http://www.adullact.org
 * @package      web-delib
 * @version        1.0
 * @lastmodified    $Date: 2007-10-14
 */

class Infosuplistedef extends AppModel
{
    public $displayField = 'nom';
    public $belongsTo = 'Infosupdef';
    public $hasMany = array( 'Infosup' );

    public $validate = array(
        'nom' => array(
            array(
                'rule' => 'notEmpty',
                'message' => 'Entrer un nom pour l\'élément'
            )
        )
    );


    /**
     * Intervertit l'ordre de l'élément $id avec le suivant ou le précédent suivant $following
     * @param integer   $id
     * @param bool      $following
     */
    function invert($id = null, $following = true)
    {
        // Initialisations
        $gap = $following ? 1 : -1;

        // lecture de l'élément à déplacer
        $recFrom = $this->find('first', array(
            'recursive' => -1,
            'fields' => array('id', 'ordre', 'infosupdef_id'),
            'conditions' => array('id' => $id)));

        // lecture de l'élément a intervertir
        $recTo = $this->find('first', array(
            'recursive' => -1,
            'fields' => array('id', 'ordre'),
            'conditions' => array(
                'infosupdef_id' => $recFrom['Infosuplistedef']['infosupdef_id'],
                'ordre' => $recFrom['Infosuplistedef']['ordre'] + $gap)));

        // Si pas d'élément à intervertir alors on sort sans rien faire
        if (empty($recTo)) return;

        // Mise à jour du champ ordre pour les deux enregistrements
        $recFrom['Infosuplistedef']['ordre'] += $gap;
        $this->save($recFrom, false);
        $recTo['Infosuplistedef']['ordre'] -= $gap;
        $this->save($recTo, false);

        return;
    }

    function beforeSave()
    {
        // calcul du n° d'ordre en cas d'ajout
        if (!array_key_exists('id', $this->data['Infosuplistedef']) || empty($this->data['Infosuplistedef']['id']))
            $this->data['Infosuplistedef']['ordre'] = $this->find('count', array(
                    'recurtsive' => -1,
                    'conditions' => array('infosupdef_id' => $this->data['Infosuplistedef']['infosupdef_id']))) + 1;
        return true;
    }

    /**
     * Réordonne les numéros d'ordre après une suppression pour l'infosupdef $infosupdefId
     */
    function reOrdonne($infosupdefId)
    {
        $recs = $this->find('all', array(
            'recursive' => -1,
            'fields' => array('id', 'ordre'),
            'conditions' => array('infosupdef_id' => $infosupdefId),
            'order' => array('ordre')));

        foreach ($recs as $n => $rec) {
            if (($n + 1) != $rec['Infosuplistedef']['ordre']) {
                $rec['Infosuplistedef']['ordre'] = ($n + 1);
                $this->save($rec, false);
            }
        }
    }

    /**
     * Suppression de tous les éléments de l'infosupdef $infosupdefId
     */
    function delList($infosupdefId)
    {
        $recs = $this->find('all', array('conditions' => array('infosupdef_id' => $infosupdefId), 'fields' => array('id'), 'recursive' => -1));
        foreach ($recs as $rec) {
            $this->delete($rec['Infosuplistedef']['id']);
        }
    }

    /**
     * détermine si une occurence peut être supprimée
     * @param integer $id id de l'occurence à supprimer
     * @return boolean true si l'instance peut être supprimée et false dans le cas contraire
     */
    function isDeletable($id)
    {
        // lecture de l'occurence en base de données
        $data = $this->find('first', array(
            'recursive' => -1,
            'fields' => array('id', 'infosupdef_id'),
            'conditions' => array('id' => $id)));
        // si utilisée alors on ne peut pas la supprimer
        return !$this->Infosup->hasAny(array('infosupdef_id' => $data['Infosuplistedef']['infosupdef_id'], 'text' => $data['Infosuplistedef']['id']));
    }

    /**
     * retourne le libellé correspondant au booléen $actif
     */
    function libelleActif($actif)
    {
        return $actif ? 'Oui' : 'Non';
    }

}
