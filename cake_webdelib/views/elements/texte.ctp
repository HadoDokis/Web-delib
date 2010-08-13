<?php
	echo $javascript->link('fckeditor/fckeditor');
	echo $javascript->link('utils');
	$delib = $this->data;
	if (!empty($delib['Deliberation']['id']))
             $id = $delib['Deliberation']['id'] ;

	if ($key=='texte_projet'){
		$cible = 1;
		$libelle = 'Texte projet';
        if (!empty($delib['Deliberation']['texte_projet_name'])) $filename = $delib['Deliberation']['texte_projet_name'];
		else $filename = '';
		$type = 'P';
	}
	elseif ($key=='texte_synthese'){
		$cible = 2;
		$libelle = 'Note synth&egrave;se';
        if (!empty($delib['Deliberation']['texte_synthese_name'])) $filename = $delib['Deliberation']['texte_synthese_name'];
		else $filename = '';
		$type = 'S';
	}
	elseif ($key=='deliberation'){
		$cible = 3;
		$libelle = 'Texte d&eacute;lib&eacute;ration';
        if (!empty($delib['Deliberation']['deliberation_name'])) $filename = $delib['Deliberation']['deliberation_name'];
		else $filename = '';
		$type = 'D';
    }

	if (Configure::read('GENERER_DOC_SIMPLE')){
		echo '<div class="annexesGauche"></div>';
		echo '<div class="fckEditorProjet">';
			echo $form->input("Deliberation.".$key, array('label'=>'', 'type'=>'textarea'));
			echo $fck->load('data[Deliberation]['.$key.']');
		echo '</div>';
	} else {
	    if (empty($delib['Deliberation'][$key."_name"]))
                echo  $form->input("Deliberation.".$key, array('label'=>'', 'type'=>'file', 'size'=>'60', 'title'=>$libelle));
            else {
                $url =    Configure::read('PROTOCOLE_DL')."://".$_SERVER['SERVER_NAME']."/files/generee/projet/$id/$key.odt";
   	        echo '<span id="Deliberation'.$key.'InputFichierJoint" style="display: none;"></span>';
	        echo '<span id="Deliberation'.$key.'AfficheFichierJoint">'; 
                echo "<a href='$url'>$filename</a>";
	        echo '&nbsp;&nbsp;';
	        echo $html->link('Supprimer', "javascript:supprimerFichierJoint('Deliberation', '".$key."', '".$libelle."')", null, 'Voulez-vous vraiment supprimer le fichier ?');
                echo '</span>';
	    }
	}
?>
