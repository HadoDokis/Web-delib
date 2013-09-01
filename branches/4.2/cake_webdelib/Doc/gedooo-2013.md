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
* Collectivité
    * "nom_collectivite","string"
    * "adresse_collectivite","string"
    * "cp_collectivite","string"
    * "ville_collectivite","string"
    * "telephone_collectivite","string"
    * "date_jour_courant","string"
    * "date_du_jour","date"

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
