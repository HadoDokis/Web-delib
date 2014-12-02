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

$filename = '';
if (isset($names[$type . "_name"]) && !empty($names[$type . "_name"])) {
    $delib['Deliberation'][$type . "_name"] = $names[$type . "_name"];
    $filename = $names[$type . "_name"];
}

if (empty($delib['Deliberation'][$type . "_name"]) || isset($validationErrorsArray[$type . '_type'])) {
    echo $this->Bs->row().
            $this->Bs->col('xs8').
    $this->BsForm->input("Deliberation.{$type}_upload", 
            array('label' => $libelle, 
                'type' => 'file', 
                'data-buttonText'=>'Choisir un fichier',
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
    if (isset($id))
        $url = Configure::read('PROTOCOLE_DL') . "://" . $_SERVER['SERVER_NAME'] . "/files/generee/projet/$id/$type.odt";
    else
        $url = '#';

    echo '<div class="input file">';
    echo "<label class='libelle'>$libelle</label>";
    echo '<span id="Deliberation' . $type . 'AfficheFichierJoint">';
    echo ': '.$this->Html->link( (stripos(Configure::read('PROTOCOLE_DL'), 'http') === true?'<i class=\'fa fa-pencil\'></i>':'').$filename, $url, array('download'=>$filename));

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