<?php
// Affichage du titre de la vue
$this->Html->addCrumb(__('Liste des circuits'), array('controller' => 'circuits', 'action' => 'index'));
$this->Html->addCrumb(__('Étapes du circuit'), $contenuVue['lienRetour']['url']);
$this->Html->addCrumb(__('Vue détaillée '));

 $affichage = $this->Bs->row();
 $affichage .= $this->Bs->col('lg12');
 if (!empty($contenuVue['titreVue']))
    $affichage .= $this->Html->tag('h3', $contenuVue['titreVue']);
 $affichage .= $this->Bs->close();
 $affichage .= $this->Bs->close();
echo $affichage;

 $affichage = $this->Bs->row();
 $affichage .= $this->Bs->col('lg12');
// Affichage des sections principales
foreach($contenuVue['sections'] as $section) {
	// affichage du titre de la section
	if (!empty($section['titreSection']))
        $affichage .=  $this->Html->tag('h4', $section['titreSection']);
    $dl = '';
    // Parcours des lignes de la section
    foreach ($section['lignes'] as $iLigne => $ligne) {
        $dl .= $this->element('viewLigne', array('ligne' => $ligne, 'altrow' => ($iLigne & 1)));
    }
    $dl =  $this->Html->tag('div', $dl, array('class' => 'well'));
    $affichage .=  $this->Html->tag('dl', $dl);
}
$affichage .= $this->Bs->close();
echo $affichage;
$affichage = $this->Bs->row();
$affichage .= $this->Bs->col('lg12');
$bouton = $this->Html2->btnCancel($contenuVue['lienRetour']['url']);
$affichage .=  $this->Html->tag('div', $bouton, array('class' => 'actions'));
$affichage .= $this->Bs->close();
$affichage .= $this->Bs->close();
echo $affichage;