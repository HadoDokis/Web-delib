<?php
// Affichage du titre de la vue
if (!empty($contenuVue['titreVue']))
    echo '<h1>' . $contenuVue['titreVue'] . '</h1>';

// Affichage de l'entête des onglets si il y en a plus d'un
if (count($contenuVue['onglets']) > 1) {
    $onglets = array();
    foreach ($contenuVue['onglets'] as $i => $onglet) {
        $onglets[] = empty($onglet['titre']) ? 'Onglet ' . $i : $onglet['titre'];
    }
    echo $this->element('onglets', array('listeOnglets' => $onglets));
}

// Affichage du contenu de chaque onglet
foreach ($contenuVue['onglets'] as $i => $onglet) {
    echo $this->Html->tag('div', null, array('id' => 'vue_detaille'));
    // Affichage des sections principales
    foreach ($onglet['sections'] as $section) {
        // affichage du titre de la section
        if (!empty($section['titre']))
            echo $this->Html->tag($section['tag'], $section['titre'], $section['htmlAttributes']);
        echo '<dl>';
        // Parcours des lignes de la section
        foreach ($section['lignes'] as $iLigne => $ligne) {
            echo $this->element('viewLigne', array('ligne' => $ligne, 'altrow' => ($iLigne & 1)));
        }
        echo '</dl>';
    }
    echo $this->Html->tag('/div');
}
// Affichage du lien de retour
echo $this->Html->tag('div', null, array('class' => 'submit btn-group'));
echo $this->Html->link('<i class="fa fa-arrow-left"></i> ' . $contenuVue['lienRetour']['title'], $contenuVue['lienRetour']['url'], array('class' => 'btn', 'escape' => false));
if ($this->request->data['Cron']['lock'])
    echo $this->Html->link('<i class="fa fa-unlock"></i> Déverrouiller', array('action'=>'unlock', $this->request->data['Cron']['id']), array('class' => 'btn btn-danger', 'escape' => false));
else
    echo $this->Html->link('<i class="fa fa-cog"></i> Exécuter', array('action'=>'executer', $this->request->data['Cron']['id']), array('class' => 'btn btn-primary', 'escape' => false));
echo $this->Html->tag('/div');
?>
<style>
    dt{
        margin: 10px;
    }
    #vue_detaille{
        padding: 10px;
    }
</style>