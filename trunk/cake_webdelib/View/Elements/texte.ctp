<?php

$delib = $this->data;
if (!empty($delib['Deliberation']['id']))
    $id = $delib['Deliberation']['id'];

switch ($type) {
    case 'texte_projet':
        $libelle = 'Texte projet';
        break;

    case 'texte_synthese':
        $libelle = 'Note synthèse';
        break;
    
    case 'deliberation':
        $libelle = 'Texte acte';
        break;
    default:
        break;
}
    /*if (!empty($delib['Deliberation'][$type.'_name']))
        $filename = $delib['Deliberation'][$type.'_name'];
    else $filename = '';*/

//$this->BsForm->input('Seance.texte_doc', 
//            array('label' => false, 
//                'type' => 'file', 
//                'data-buttonText'=>'Nouveau Débats généraux',
//                'data-iconName'=>'fa fa-file-text-o',
//                'data-badge'=> false,
//                'help' => 'Les modifications apportées ici ne prendront effet que lors de la sauvegarde du projet.',
//                'title' => 'Choisir un fichier', 
//                'class' => 'filestyle')).$this->Bs->close().
//        $this->Bs->col('xs4').
//        $this->Bs->div('btn-group btn-group-right').
//        $this->Bs->btn('Effacer' , '#', array(
//            'type'=>'danger',
//            'class'=>'btn-danger-right',
//            'icon'=>'glyphicon glyphicon-floppy-remove',
//            'onclick'=>'$("#SeanceTexteDoc").filestyle(\'clear\');',
//        )).
//        $this->Bs->close(3);


$filename = '';
if (isset($names[$type . "_name"]) && !empty($names[$type . "_name"])) {
    $delib['Deliberation'][$type . "_name"] = $names[$type . "_name"];
    $filename = $names[$type . "_name"];
}

if (empty($delib['Deliberation'][$type . "_name"]) || isset($validationErrorsArray[$type . '_type'])) {
    echo $this->Bs->row().
            $this->Bs->col('xs8').
    $this->BsForm->input("Deliberation.{$type}_upload", 
            array(
                'label' => $libelle, 
                'type' => 'file', 
                'data-buttonText'=>'Choisir un fichier',
                'data-iconName'=>'fa fa-file-text-o',
                'data-badge'=> false,
                'title' => $libelle, 
                'class' => 'filestyle')).$this->Bs->close().
            $this->Bs->col('xs4').
            $this->Bs->btn('Effacer' , '#', array(
                'type'=>'danger',
                'icon'=>'glyphicon glyphicon-floppy-remove',
                'onclick'=>'$("#Deliberation'.Inflector::camelize($type).'Upload").filestyle(\'clear\');',
            )).
            $this->Bs->close(2);
} else {
    echo '<div class="input file">';
    echo "<label class='libelle'>$libelle</label>";
    echo '<span id="Deliberation' . $type . 'AfficheFichierJoint">';
    echo $linkFile_{$type};
    echo ': '.$this->Html->link( (stripos(Configure::read('PROTOCOLE_DL'), 'http') === true?'<i class=\'fa fa-pencil\'></i>':'').$filename, null, array('download'=>$filename));

    echo '&nbsp;&nbsp;';
    echo $this->Html->link('<i class="fa fa-trash-o"></i> Supprimer', "javascript:supprimerFichierJoint('Deliberation', '" . $type . "', '" . $libelle . "')", 
            array('class' => 'btn btn-danger btn-mini', 
                'escape' => false, 
                'title' => 'Supprimer le fichier',
                'confirm'=>'Voulez-vous vraiment supprimer '.$filename.' du projet ?'
                ));
    echo "</span>";
    echo '<span id="Deliberation' . $type . 'InputFichierJoint" style="display: none;"></span>';
    echo '</div>';
}
if (!empty($validationErrorsArray[$type . '_type'])) {
    echo("<div class='error-message'>" . $validationErrorsArray[$type . '_type'][0] . '</div>');
}