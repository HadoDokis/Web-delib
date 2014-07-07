<?php
/**
 * Préparation du contenu des vues détaillées (view) dans les controleurs.
 *
 */

class VueDetailleeComponent extends Component
{
    private $contenuVue = array();
    private $iOnglet;
    private $iSection;
    private $iLigne;

    function __construct($titreVue = 'Vue détaillée', $lienRetourTitle = 'Retour', $lienRetourUrl = array('action' => 'index'), $actions = array())
    {
        $this->contenuVue['titreVue'] = $titreVue;
        $this->contenuVue['lienRetour'] = array(
            'title' => $lienRetourTitle,
            'url' => $lienRetourUrl);
        $this->contenuVue['actions'] = $actions;
        $this->contenuVue['onglets'] = array();
        $this->iOnglet = 0;
    }

    /**
     * Ajoute un onglet
     */
    function ajouteOnglet($nom = '')
    {
        $this->iOnglet++;
        $this->iSection = 0;
        $this->contenuVue['onglets'][$this->iOnglet] = array(
            'titre' => $nom,
            'sections' => array()
        );
    }

    /**
     * Ajoute une section
     */
    function ajouteSection($nom = '', $options = array())
    {
        // Initialisation des valeurs par défaut
        $defaut = array(
            'tag' => 'h4',
            'htmlAttributes' => array());
        $options = array_merge($defaut, $options);

        if (!$this->iOnglet) $this->ajouteOnglet();
        $this->iSection++;
        $this->iLigne = 0;
        $this->contenuVue['onglets'][$this->iOnglet]['sections'][$this->iSection] = array(
            'titre' => $nom,
            'tag' => $options['tag'],
            'htmlAttributes' => $options['htmlAttributes'],
            'lignes' => array()
        );
    }

    /**
     * ajoute une nouvelle ligne à la dernière section
     */
    function ajouteLigne($libelle, $valeur = '', $ddClasse = '')
    {
        if (!$this->iSection) $this->ajouteSection();
        $this->iLigne++;
        $this->contenuVue['onglets'][$this->iOnglet]['sections'][$this->iSection]['lignes'][$this->iLigne][] = array(
            'libelle' => $libelle,
            'valeur' => $valeur,
            'ddClasse' => $ddClasse
        );
    }

    /**
     * ajoute un nouvel élément à la dernière ligne de la dernière section
     */
    function ajouteElement($libelle, $valeur = '', $ddClasse = '')
    {
        if (!$this->iLigne)
            $this->ajouteLigne($libelle, $valeur);
        else
            $this->contenuVue['onglets'][$this->iOnglet]['sections'][$this->iSection]['lignes'][$this->iLigne][] = array(
                'libelle' => $libelle,
                'valeur' => $valeur,
                'ddClasse' => $ddClasse
            );
    }

    /**
     * retourne le contenue de la vue
     */
    function getContenuVue()
    {
        return $this->contenuVue;
    }

}
