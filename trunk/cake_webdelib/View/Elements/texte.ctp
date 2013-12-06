<?php

$filename = "";
$delib = $this->data;
if (!empty($delib['Deliberation']['id']))
    $id = $delib['Deliberation']['id'];

if ($key == 'texte_projet') {
    $cible = 1;
    $libelle = 'Texte projet';
    if (!empty($delib['Deliberation']['texte_projet_name']))
        $filename = $delib['Deliberation']['texte_projet_name'];
    else $filename = '';
    $type = 'P';
} elseif ($key == 'texte_synthese') {
    $cible = 2;
    $libelle = 'Note synthèse';
    if (!empty($delib['Deliberation']['texte_synthese_name']))
        $filename = $delib['Deliberation']['texte_synthese_name'];
    else $filename = '';
    $type = 'S';
} elseif ($key == 'deliberation') {
    $cible = 3;
    $libelle = 'Texte acte';
    if (!empty($delib['Deliberation']['deliberation_name']))
        $filename = $delib['Deliberation']['deliberation_name'];
    else $filename = '';
    $type = 'D';
}

if (Configure::read('GENERER_DOC_SIMPLE')) {
    echo '<div class="annexesGauche"></div>';
    echo '<label class="spacer">' . $libelle . '</label>';
    echo '<div class="fckEditorProjet">';
    echo $this->Form->input('Deliberation.' . $key, array('label' => '', 'type' => 'textarea'));
    echo $this->Fck->load('Deliberation' . Inflector::camelize($key));
    echo '</div>';
} else {
    if (isset($names[$key . "_name"]) && !empty($names[$key . "_name"])) {
        $delib['Deliberation'][$key . "_name"] = $names[$key . "_name"];
        $filename = $names[$key . "_name"];
    }

    if (empty($delib['Deliberation'][$key . "_name"]) || isset($validationErrorsArray[$key . '_type'])) {
        echo $this->Form->input("Deliberation." . $key, array('label' => $libelle, 'type' => 'file', 'size' => '60', 'title' => $libelle, 'class' => 'file-texte'));
    } else {
        if (isset($id))
            $url = Configure::read('PROTOCOLE_DL') . "://" . $_SERVER['SERVER_NAME'] . "/files/generee/projet/$id/$key.odt";
        else
            $url = '#';
        echo '<div class="input file">';
        echo "<label class='libelle'>$libelle</label>";
        echo '<span id="Deliberation' . $key . 'AfficheFichierJoint">';
        echo ": <a href='$url' download='$filename'>$filename</a>";
        echo '&nbsp;&nbsp;';
        echo $this->Html->link('<i class="fa fa-trash-o"></i> Supprimer', "javascript:supprimerFichierJoint('Deliberation', '" . $key . "', '" . $libelle . "')", array('class'=>'btn btn-danger', 'escape'=>false, 'title'=>'Supprimer le fichier'), "Voulez-vous vraiment supprimer $filename du projet ?");
        echo '</span>';
        echo '<span id="Deliberation' . $key . 'InputFichierJoint" style="display: none;"></span>';
        echo '</div>';
    }
    if (!empty($validationErrorsArray[$key . '_type'])) {
        echo("<div class='error-message'>" . $validationErrorsArray[$key . '_type'][0] . '</div>');
    }
}