<?php
/*
	Affiche le texte d'une délib
	Paramètres :
		string	type : type de texte 'projet', 'synthese', 'deliberation'
		string	$delib : delib contenant les textes
*/

/* Initialisation des paramètres */
if (empty($type))
    return;

switch ($type) {
    case 'projet' :
        $libelle = 'Texte de projet';
        $textKey = 'texte_projet';
        $filename = $delib['texte_projet_name'];
        break;
    case 'synthese' :
        $libelle = 'Note de synthèse';
        $textKey = 'texte_synthese';
        $filename = $delib['texte_synthese_name'];
        break;
    case 'deliberation' :
        $libelle = 'Texte d&apos;acte';
        $textKey = 'deliberation';
        $filename = $delib['deliberation_name'];
}

if (!empty($delib[$textKey])) {
    echo $this->Html->tag('dd', null, array('style'=>'text-indent:0;'));
    echo $this->Html->tag('span', $libelle);
    echo ' : ';
    echo $this->Html->link($filename, array('action' => 'download', $delib['id'], $textKey));
    echo $this->Html->tag('/dd');
}