<?php
class HistoriquesController extends AppController {

    public $components = array('Paginator', 'Filtre');

    /**
     * Affichage de l'historique des commentaires 
     */
    public function index() {
        //on initialise le filtre avec le retour de donné pour paramétrer le filtre
        $this->Filtre->initialisation('Historique', $this->data);
        //initialisation des conditions pour la recherche
        $conditions = $this->Filtre->conditions();
        //condition permanente

        //on suprime le modèle donc la jointure délibération
        $this->Historique->unbindModel(array('belongsTo' => array('Deliberation')));
        //on la recret avec une jointure a droite de cette facons les commentaires avec une id de délibération supprimé n'apparaisse pas
        $this->Historique->bindModel(
                array('belongsTo' => array(
                        'Deliberation' => array(
                            'className' => 'Deliberation',
                            'conditions' => '',
                            'order' => '',
                            'type' => 'right',
                            'dependent' => false,
                            'foreignKey' => 'delib_id')))
        );
        //requette avec paginate pour avoir des pages de 20 resultats
        $conditions['Historique.created IS NOT '] = '';
        $paginate = array(
            'fields' => array('Historique.id','Historique.user_id', 'Historique.commentaire', 'Historique.created', 'User.nom', 'User.prenom', 'Deliberation.id', 'Deliberation.objet'),
            'conditions' => $conditions,
            'limit' => 20,
            'order' => array(
                'Historique.created' => 'ASC'
            )
        );
        //initialisation des pages affichés
        $this->Paginator->settings = $paginate;
        $historique = $this->Paginator->paginate($this->Historique);
        //initialisation des champs du filtres
        $this->_ajouterFiltre();
        $this->set('historique', $historique);
    }

    /**
     * Ajoute les filtres voulues
     * 
     * @param type $projets paramètre pouvant contenir les informations vouluent ds une combobox 
     */
    function _ajouterFiltre($projets = null) {

        //champ texte
        $this->Filtre->addCritere('Deliberationobjet', array(
            'field' => 'Deliberation.objet',
            'inputOptions' => array(
                'label' => __('Objet', true),
                'type' => 'text',
                'title' => __('Filtre sur les objets des délibérations')),
            'column' => 3));

        $this->Filtre->addCritere('DeliberationId', array(
            'field' => 'Deliberation.id',
            'retourLigne' => true,
            'inputOptions' => array(
                'label' => __('Id', true),
                'type' => 'text',
                'title' => __('Filtre sur les ids des délibérations')),
            'column' => 3));

        $this->Filtre->addCritere('UserLogin', array(
            'field' => 'User.login',
            'inputOptions' => array(
                'label' => __('Login', true),
                'type' => 'text',
                'title' => __('Filtre sur les identifiants de connexion des utilisateurs')),
            'column' => 3));

        $this->Filtre->addCritere('UserNom', array(
            'field' => 'User.nom',
            'inputOptions' => array(
                'label' => __('Nom', true),
                'type' => 'text',
                'title' => __('Filtre sur les noms des utilisateurs')),
            'column' => 3));

        $this->Filtre->addCritere('UserPrenom', array(
            'field' => 'User.prenom',
            'retourLigne' => true,
            'inputOptions' => array(
                'label' => __('Prenom', true),
                'type' => 'text',
                'title' => __('Filtre sur les prenoms des utilisateurs')),
            'column' => 3));

        //champ datetimepicker
        $this->Filtre->addCritere('dateDebut', array(
            'field' => 'Historique.created >= DATE',
            'inputOptions' => array(
                'label' => __('date début', true),
                'type' => 'date',
                'style' => 'cursor:pointer',
                'help' => __('Cliquez sur le champs ci-dessus pour choisir la date'),
                //'disabled' => false,//readonly
                'title' => __('Filtre sur les dates')),
            'Datepicker' => array(
                'language' => 'fr',
                'autoclose' => 'true',
                'format' => 'yyyy-mm-dd hh:00:00',
                'startView' => 'decade', //decade
                'minView' => 'day',
            ),
            'column' => 3));

        $this->Filtre->addCritere('dateFin', array(
            'field' => 'Historique.created <= DATE',
            'inputOptions' => array(
                'label' => __('date de fin', true),
                'type' => 'date',
                'style' => 'cursor:pointer',
                'help' => __('Cliquez sur le champs ci-dessus pour choisir la date'),
                'readonly' => '',
                'title' => __('Filtre sur les dates')),
            'Datepicker' => array(
                'language' => 'fr',
                'autoclose' => 'true',
                'format' => 'yyyy-mm-dd hh:00:00',
                'startView' => 'decade', //decade
                'minView' => 'day',
            ),
            'column' => 3));

        //champ select
        $this->Filtre->addCritere('difDate', array(
            'retourLigne' => true,
            'attribute' => array(__('1 heure'), __('1 jour'), __('1 mois'), __('1 ans'),__('hier'),_('cette semaine'),__('aujourd\'hui')),
            'inputOptions' => array(
                'empty' => 'Aucune plage de date',
                'type' => 'select',
                'title' => __('Sélectioner une plage entre la date de début et de fin'),
                'label' => __('Plage de dates', true),
            //'options' => array(__('1 heure'), __('1 jour'),__('1 mois'),__('1 ans'))
            ),
            'column' => 3));
    }

}