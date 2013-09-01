# Refonte de la méthode ModelsController::generer()

## Problèmes

* une méthode, 7 paramètres
* appelée depuis plusieurs endroits de l'application
* code complexe
* nombre de requêtes élevé: environ 9000 pour une séance comportant 70 délibérations

## Plusieurs utilités

* fusionner des modèles de documents odt avec des données de l'application
* convertir le document odt résultant en pdf
* fusionner des fichiers pdf
* créer une archive de fichiers
* stocker des fichiers (?)
* envoyer des mails

## Hiérarchie des données envoyées à Gedooo

### Données communes

#### Partie principale
* Données non liées aux enregistrements
    * "date_jour_courant","string"
    * "date_du_jour","date"

* Collectivité
    * "nom_collectivite","string"
    * "adresse_collectivite","string"
    * "cp_collectivite","string"
    * "ville_collectivite","string"
    * "telephone_collectivite","string"

### Pour une délibération (un projet de délibération)

#### Partie principale

* Délibération
* Infosup (Deliberation/Projet, champs et types de données dynamiques)

#### Itérations
* ActeursAbsents
* ActeursAbstention
* ActeursContre
* ActeursMandates
* ActeursPour
* ActeursPresents
* ActeursSansParticipation
* Annexes
* AvisSeance
* Convoques
* Deliberations
* Historique
* Seances
    * AvisSeance
    * Convoques
    * Infosup (Seance, champs et types de données dynamiques)

### Pour une séance

#### Partie principale
* Seance
    * "date_convocation_seance","date"
    * "identifiant_seance","text"
    * "commentaire_seance","string"
    * "type_seance","text"
* Seance, champs calcules
    * "nombre_acteur_seance","text"
* Seance, champs normalisés
    * "date_seance_lettres","text"
    * "heure_seance","text"
    * "date_seance","date"
    * "hh_seance","string"
    * "mm_seance","string"
* Infosup (Seance, champs et types de données dynamiques)

#### Itérations
* AvisSeance
    * "AvisSeance.{n}.commentaire","string"
* Convoques
    * "nom_acteur_convoque_seance","text"
    * "prenom_acteur_convoque_seance","text"
    * "salutation_acteur_convoque_seance","text"
    * "titre_acteur_convoque_seance","text"
    * "note_acteur_convoque_seance","text"
* Projets
    * ?? "Projets.{n}.maquette_delibere.odt","application/vnd.oasis.opendocument.text"
    * ActeursAbsents
    * ActeursAbstention
    * ActeursContre
    * ActeursMandates
    * ActeursPour
    * ActeursPresents
    * ActeursSansParticipation
    * Deliberations
        * "libelle_multi_delib","text"
        * "id_multi_delib","text"
    * Historique
        * ".log","text"
    * Projet, champs calculés
        * "nombre_seance","text"
        * "nombre_pour","text"
        * "nombre_abstention","text"
        * "nombre_contre","text"
        * "nombre_sans_participation","text"
        * "nombre_votant","text"
        * "nombre_annexe","text"
    * Projet
        * "identifiant_projet","text"
        * "date_seance_lettres","text"
        * "position_projet","text"
        * "titre_projet","string"
        * "objet_projet","string"
        * "libelle_projet","string"
        * "objet_delib","string"
        * "libelle_delib","string"
        * "etat_projet","text"
        * "numero_deliberation","text"
        * "numero_acte","text"
        * "classification_deliberation","text"
        * "date_envoi_signature","date"
        * "service_emetteur","text"
        * "service_avec_hierarchie","text"
        * "T3_theme","text"
        * "T4_theme","text"
        * "T5_theme","text"
        * "T6_theme","text"
        * "T7_theme","text"
        * "T8_theme","text"
        * "T9_theme","text"
        * "T10_theme","text"
        * "T1_theme","text"
        * "T2_theme","text"
        * "theme_projet","text"
        * "critere_trie_theme","text"
        * "prenom_redacteur","text"
        * "nom_redacteur","text"
        * "email_redacteur","text"
        * "telmobile_redacteur","text"
        * "telfixe_redacteur","text"
        * "note_redacteur","text"
        * "acte_adopte","text"
        * "date_reception","text"
        * "commentaire_vote","string"
        * "categorie","text"
        * "texte_projet","text"
        * "texte_deliberation","text"
        * "note_synthese","text"
        * "debat_deliberation","text"
        * "debat_commission","text"
    * Séance délibérante (ou ??)
        * "heure_seance","text"
        * "date_seance","date"
        * "hh_seance","string"
        * "mm_seance","string"
        * "date_convocation_seance","date"
        * "identifiant_seance","text"
        * "commentaire_seance","string"
        * "type_seance","text"
    * Seances
        * Seance
            * "date_convocation_seances","date" // s
            * "identifiant_seances","text" // s
            * "commentaire_seances","string" // s
            * "type_seances","text" // s
        * Seance, champs calcules
            * "nombre_acteur_seances","text" // s
            * "date_seance_lettres","text"
            * "heure_seances","text" // s
            * "date_seances","date" // s
            * "hh_seances","string" // s
            * "mm_seances","string" // s
        * AvisSeance
            * "commentaire","string"
        * Convoques
            * "nom_acteur_convoque_seances","text" // s
            * "prenom_acteur_convoque_seances","text" // s
            * "salutation_acteur_convoque_seances","text" // s
            * "titre_acteur_convoque_seances","text" // s
            * "note_acteur_convoque_seances","text" // s
        * Infosup (Seance, champs et types de données dynamiques)