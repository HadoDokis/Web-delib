<?php
/**
 * Gestion des filtres dans les vues index
 * Stockage des listes des options des filtres dans la session
 * Passage des critères du filtre au controlleur par $this->data
 * Utilise la variable de session Filtre qui a la structure suivante :
 * - nom : non du filtre courant
 * - Criteres('nomCritere') : liste des ciriteres qui ont la structure suivante :
 *        -'field' : nom du champ a filtrer (ex : User.name)
 *    -'inputOptions' : liste des options du select du formulaire du filtre
 *        -'classeDiv' : nom de la classe de la div qui contient l'input
 *        -'retourLigne' : boobleen qui indique si il faut ajouter un div spacer
 */

class FiltreComponent extends Component
{

    public $components = array('Session');

    // called before Controller::beforeFilter()
    function initialize(&$controller, $settings = array())
    {
        // saving the controller reference for later use
        $this->controller = $controller;
    }

    /**
     * Initialisation du filtre : création et sauvegarde des valeurs des critères saisis dans la vue
     * @param string $name : nom du filtre
     * @param string $dataFiltre : data du formulaire de saisi des critère du filtre
     * @param array $options tableau des parmètres optionnels :
     *        'url' : array, optionnel, url du formulaire du filtre
     */
    function initialisation($name, $dataFiltre, $options = array())
    {
        // Initilisations
        $filtreActif = false;
        $defaultOptions = array('url' => array(
            'controller' => $this->controller->request->params['controller'],
            'action' => $this->controller->request->params['action']
        ));
        $options = array_merge($defaultOptions, $options);

        // Si on a déjà un filtre en session et qu'il est différent alors on supprime l'ancien filtre
        if ($this->Session->check('Filtre') && $this->Session->read('Filtre.nom') != $name) {
            $this->Session->delete('Filtre');
        }
        // Initialisation
        if ($this->Session->check('Filtre')) {
            if (!empty($dataFiltre)) {
                // Sauvegarde des valeurs des critères sélectionnés dans la vue
                foreach ($dataFiltre['Critere'] as $nomCritere => $valCritere) {
                    if ($this->Session->read('Filtre.Criteres.' . $nomCritere . '.inputOptions.type') == 'date') {
                        // critère de type date
                        $dateVide = array('day' => '', 'month' => '', 'year' => '');
                        // initialisation de la valeur en session
                        $valSessionSelected = $this->Session->read('Filtre.Criteres.' . $nomCritere . '.inputOptions.selected');
                        if (empty($valSessionSelected))
                            $this->Session->write('Filtre.Criteres.' . $nomCritere . '.inputOptions.selected', $dateVide);
                        // Si la date est incomplete : on garde la valeur précédente en session
                        if ((!empty($valCritere['day']) || !empty($valCritere['month']) || !empty($valCritere['year']))
                            && (empty($valCritere['day']) || empty($valCritere['month']) || empty($valCritere['year']))
                        ) {
                            $this->Session->write('Filtre.Criteres.' . $nomCritere . '.changed', false);
                            $valCritere = $this->Session->read('Filtre.Criteres.' . $nomCritere . '.inputOptions.selected');
                        } else {
                            if ($this->Session->read('Filtre.Criteres.' . $nomCritere . '.inputOptions.selected') === $valCritere) {
                                $this->Session->write('Filtre.Criteres.' . $nomCritere . '.changed', false);
                            } else {
                                $this->Session->write('Filtre.Criteres.' . $nomCritere . '.inputOptions.selected', $valCritere);
                                $this->Session->write('Filtre.Criteres.' . $nomCritere . '.changed', true);
                            }
                        }
                        $filtreActif = $filtreActif || ($valCritere != $dateVide);
                    } else {
                        if ($this->Session->read('Filtre.Criteres.' . $nomCritere . '.inputOptions.selected') === $valCritere) {
                            $this->Session->write('Filtre.Criteres.' . $nomCritere . '.changed', false);
                        } else {
                            $this->Session->write('Filtre.Criteres.' . $nomCritere . '.inputOptions.selected', $valCritere);
                            $this->Session->write('Filtre.Criteres.' . $nomCritere . '.changed', true);
                        }
                        $filtreActif = $filtreActif || ($valCritere !== '');
                    }
                }
                // Sauvegarde des valeurs du fonctionnement du filtre
                $this->Session->write('Filtre.Fonctionnement.affiche', $dataFiltre['filtreFonc']['affiche']);
                $this->Session->write('Filtre.Fonctionnement.actif', $filtreActif);
            }
        } else {
            $this->Session->write('Filtre.nom', $name);
            $this->Session->write('Filtre.Fonctionnement.affiche', false);
            $this->Session->write('Filtre.Fonctionnement.actif', false);
            $this->Session->write('Filtre.url', $options['url']);
        }
    }

    /**
     * Test l'existence du filtre $name
     * @param string $name : nom du filtre à tester
     * @return bool true si il existe, false dans le cas contraire
     */
    function exists($name)
    {
        return ($this->Session->check('Filtre') && $this->Session->read('Filtre.nom') == $name);
    }

    /**
     * Test l'existence d'un critère du filtre
     * @param string $name : nom du critere du filtre à tester, si vide test l'existence de la présence de critères
     * @return bool true si il existe, false dans le cas contraire
     */
    function critereExists($name = null)
    {
        if (empty($name))
            return ($this->Session->check('Filtre.Criteres'));
        else
            return $this->Session->check('Filtre.Criteres.' . $name);
    }

    /**
     * Ajoute un critere au filtre courant (en session)
     * @param string $nomCritere : nom du critère
     * @param array $params paramètres de la fonction sous forme de tableau avec les entrées suivantes :
     *    - string $field : nom du model.champ a filtrer (ex : User.name)
     *    - array $inputOptions : options du select affiché dans le formulaire
     *    'label' : texte affiché devant le select
     *    'options' : liste des options du select, ....
     *    - string $classeDiv nom de la classe du div contenant l'input
     *    - booleen $retourLigne indique si il faut ajouter un div spacer
     */
    function addCritere($nomCritere, $params)
    {
        // Initialisation des valeurs par défaut
        $defaut = array(
            'classeDiv' => 'demi',
            'retourLigne' => false);
        $params = array_merge($defaut, $params);
        // Initialisation des valeurs par défaut des options du select
        $inputOptionsDefaut = array();
        if (!array_key_exists('multiple', $params['inputOptions']) || !$params['inputOptions']['multiple'])
            $inputOptionsDefaut = array(
                'empty' => __('tous', true));
        $params['inputOptions'] = array_merge($inputOptionsDefaut, $params['inputOptions']);

        $this->Session->write('Filtre.Criteres.' . $nomCritere, $params);
        $this->Session->write('Filtre.Criteres.' . $nomCritere . '.inputOptions.selected', '');
        $this->Session->write('Filtre.Criteres.' . $nomCritere . '.changed', false);

    }

    /**
     * Supprime un critere au filtre courant (en session)
     * @param string $nomCritere : nom du critère à supprimer
     */
    function delCritere($nomCritere)
    {
        $this->Session->delete('Filtre.Criteres.' . $nomCritere);
    }

    /**
     * Initialise la valeur sélectionnée du critère
     * @param string $nomCritere : nom du critère
     * @param string $valCritere valeur du critère
     */
    function setCritere($nomCritere, $valCritere)
    {
        // initialisation
        if (!$this->critereExists($nomCritere)) return;
        // selon le type de critère
        if ($this->Session->check('Filtre.Criteres.' . $nomCritere . '.inputOptions.options')) {
            // select : recherche de l'option correspondante à $valCritere
            $options = $this->Session->read('Filtre.Criteres.' . $nomCritere . '.inputOptions.options');
            if (array_key_exists($valCritere, $options)) {
                $this->Session->write('Filtre.Criteres.' . $nomCritere . '.inputOptions.selected', $valCritere);
                $this->Session->write('Filtre.Fonctionnement.actif', true);
            }
        } else {
            // input
            $this->Session->write('Filtre.Criteres.' . $nomCritere . '.inputOptions.selected', $valCritere);
            $this->Session->write('Filtre.Fonctionnement.actif', true);
        }
    }

    /**
     * Supprime tous les criteres du filtre (en session)
     */
    function supprimer()
    {
        $this->Session->delete('Filtre');
    }

    /**
     * Retourne un tableau de conditions en fonction de la valeur des filtres de la vue
     */
    function conditions()
    {
        $conditions = array();
        if (!$this->Session->check('Filtre.Criteres')) return $conditions;
        $criteres = $this->Session->read('Filtre.Criteres');
        foreach ($criteres as $critere) {
            if (!array_key_exists('selected', $critere['inputOptions'])) continue;
            if (is_array($critere['inputOptions']['selected'])) {
                if (empty($critere['inputOptions']['selected'])) continue;
            } else
                if (strlen($critere['inputOptions']['selected']) == 0) continue;
            if (array_key_exists('type', $critere['inputOptions']) && $critere['inputOptions']['type'] == 'date') {
                // date
                if (strlen($critere['inputOptions']['selected']['day']) > 0
                    && strlen($critere['inputOptions']['selected']['month']) > 0
                    && strlen($critere['inputOptions']['selected']['year']) > 0
                ) {
                    // la date est renseignée
                    if (strpos($critere['field'], '>') !== false)
                        $conditions[$critere['field']] = sprintf("%s-%s-%s 00:00:00", $critere['inputOptions']['selected']['year'], $critere['inputOptions']['selected']['month'], $critere['inputOptions']['selected']['day']);
                    elseif (strpos($critere['field'], '<') !== false)
                        $conditions[$critere['field']] = sprintf("%s-%s-%s 23:59:59", $critere['inputOptions']['selected']['year'], $critere['inputOptions']['selected']['month'], $critere['inputOptions']['selected']['day']);
                    else
                        $conditions[$critere['field']] = sprintf("%s-%s-%s", $critere['inputOptions']['selected']['year'], $critere['inputOptions']['selected']['month'], $critere['inputOptions']['selected']['day']);
                }
            } elseif (array_key_exists('type', $critere['inputOptions']) && $critere['inputOptions']['type'] == 'text') {
                // text : gestion du méta caractère %
                if (strpos($critere['inputOptions']['selected'], '%') !== false)
                    $conditions[$critere['field'] . ' ILIKE'] = $critere['inputOptions']['selected'];
                else
                    $conditions[$critere['field']] = $critere['inputOptions']['selected'];
            } else {
                // select : cas ou la valeur sélectionnée commence par '>|', '>=|', '<|', '<=|'
                if (!is_array($critere['inputOptions']['selected']) && strpos($critere['inputOptions']['selected'], '|') !== false) {
                    $tabCritere = explode('|', $critere['inputOptions']['selected']);
                    $conditions[$critere['field'] . ' ' . $tabCritere[0]] = $tabCritere[1];
                } else
                    $conditions[$critere['field']] = $critere['inputOptions']['selected'];
            }
        }
        return $conditions;
    }

    /**
     * Test la valeur du critère $name a changé
     * @param string $nomCritere : nom du filtre à tester si vide test l'existence de la présence de critères
     * @return bool true si la valeur a changé, false dans le cas contraire ou si le critère n'existe pas
     */
    function critereChanged($nomCritere)
    {
        if (!$this->critereExists($nomCritere)) return false;

        return $this->Session->read('Filtre.Criteres.' . $nomCritere . '.changed');
    }

    /**
     * Retourne la valeur sélectionnée du critère $name
     * @param string $nomCritere : nom du filtre à tester si vide test l'existence de la présence de critères
     * @return bool valeur sélectionnée
     */
    function critereSelected($nomCritere)
    {
        if (!$this->critereExists($nomCritere)) return false;

        return $this->Session->read('Filtre.Criteres.' . $nomCritere . '.inputOptions.selected');
    }

}
