<?php
$this->Html->addCrumb(__('Planification de la tâche'), array('controller' => 'crons', 'action' => 'index'));
$this->Html->addCrumb('Circuits de traitement');
echo $this->Bs->tag('h3', $contenuVue['titreVue']);

// Affichage du contenu de chaque onglet
foreach ($contenuVue['onglets'] as $i => $onglet) {
    // Affichage des sections principales
    foreach ($onglet['sections'] as $section) {
        // affichage du titre de la section
        echo $this->Bs->panel($section['titre']);
        // Parcours des lignes de la section
        foreach ($section['lignes'] as $iLigne => $ligne) {
            echo '<b>'.$ligne[0]['libelle'].' : </b>'.$ligne[0]['valeur'].'<br>';
        }
        echo $this->Bs->endPanel();
    }
}

echo $this->Bs->div('text-center') .
$this->Bs->div('btn-group') .
$this->Bs->btn($this->Bs->icon('arrow-left') . ' Retour', $previous, 
        array('type' => 'default', 'escape' => false, 
            'title' => $contenuVue['lienRetour']['title']));

if ($this->request->data['Cron']['lock'])
    echo $this->Bs->btn('Déverrouiller', array(
        'controller' => 'crons', 
        'action' => 'unlock', 
        $this->request->data['Cron']['id']),
        array('type' => 'danger', 
            'icon' => 'fa fa-unlock', 
            'title' => 'Déverrouiller'));
else
    echo $this->Bs->btn('Exécuter', array(
        'controller' => 'crons', 
        'action' => 'executer', 
        $this->request->data['Cron']['id']),
        array('type' => 'primary', 
            'icon' => 'fa fa-cog', 
            'title' => 'Exécuter'));

echo $this->Bs->close(2);