<div id='loading' style="display:none;">&nbsp;</div>
<div id="buttons">
<?php
    echo $this->Html->script('utils.js');
    echo $this->Html->script('noback.js');
	echo $this->Html->script('ckeditor/ckeditor');
	echo $this->Html->script('ckeditor/adapters/jquery');
?>
<?php // Initialisation des boutons action de la vue
	$defBarre = array();
	$defBarre[] = array('title'=>'Valider', 'url'=>'/deliberations/traiter/'.$deliberation['Deliberation']['id'].'/1', 'htmlAttributes'=>array('class'=>'link_valider_avec_border', 'title'=>'Valider le projet', 'onclick'=>"disableDiv('buttons');"));
	$defBarre[] = array('title'=>'Refuser', 'url'=>'/deliberations/traiter/'.$deliberation['Deliberation']['id'].'/0', 'htmlAttributes'=>array('class'=>'link_refuser_avec_border', 'title'=>'Refuser le projet', 'onclick'=>"disableDiv('buttons');"));
	$defBarre[] = array('title'=>"Retourner à", 'url'=>'/deliberations/retour/'.$deliberation['Deliberation']['id'], 'htmlAttributes'=>array('class'=>'link_retour_avec_border', 'title'=>'Retourner le projet à'));
	if ($Droits->check($this->Session->read('user.User.id'), 'Deliberations:rebond'))
		$defBarre[] = array('title'=>'Envoyer à', 'url'=>'/deliberations/rebond/' . $deliberation['Deliberation']['id'], 'htmlAttributes'=>array('class'=>'link_rebond_avec_border','title'=>'Envoyer à'));
	$defBarre[] = array();
	$defBarre[] = array('title'=>'Ajouter un commentaire', 'url'=>'/commentaires/add/'.$deliberation['Deliberation']['id'], 'htmlAttributes'=>array('class'=>'link_commentaire_avec_border', 'title'=>'Ajouter un commentaire (affichés plus bas)'));
	$defBarre[] = array();

	if ($Droits->check($this->Session->read('user.User.id'), 'Deliberations:edit'))
		$defBarre[] = array('title'=>'Modifier', 'url'=>'/deliberations/edit/' . $deliberation['Deliberation']['id'], 'htmlAttributes'=>array('class'=>'link_modifier_avec_border','title'=>'Modifier'));

	$defBarre[] = array('title'=>'Annuler', 'url'=>'/deliberations/mesProjetsATraiter', 'htmlAttributes'=>array('class'=>'link_annuler', 'title'=>'Annuler'));

	$linkBarre  = "<table class='table_action' cellspacing='0' cellpadding='0'><tr>";
	$linkBarre .= $this->Menu->linkBarre($defBarre, 'td');
	$linkBarre .= "</tr></table>";
	
	echo $this->Html->tag('div', null, array('id'=>"vue_cadre"));
	// affichage  du titre
	$listeIds = $deliberation['Deliberation']['id'];
	foreach($deliberation['Multidelib'] as $delibRattachee) {
		$listeIds .= ', '.$delibRattachee['id'];
	}
	echo $this->Html->tag('h3', 'Valider ou refuser les projets '.$deliberation['Typeacte']['libelle'].' (id : '.$listeIds.')');
	echo $linkBarre;
?>
</br>

<dl>
	<div class="imbrique">
	<?php
	    if (empty($deliberation['Multidelib'])) {
	    	echo $this->Html->tag('dt', 'Libellé');
	    	echo $this->Html->tag('dd', '&nbsp;'.$deliberation['Deliberation']['objet']);
	    } else {
			echo $this->element('viewDelibRattachee', array(
				'delib'=>$deliberation['Deliberation'],
				'annexes'=>$deliberation['Annex'],
				'natureLibelle'=>$deliberation['Nature']['libelle']));
	    	foreach($deliberation['Multidelib'] as $delibRattachee) {
				echo $this->element('viewDelibRattachee', array(
					'delib'=>$delibRattachee,
					'annexes'=>$delibRattachee['Annex'],
					'natureLibelle'=>$deliberation['Nature']['libelle']));
	    	}
	    	echo $this->Html->tag('h2', 'Informations du projet (communes aux délibérations)');
	    }
	?>
		<dt>Titre</dt>
		<dd>&nbsp;<?php echo $deliberation['Deliberation']['titre']?></dd>
	</div>

<div class="imbrique">
	<div class="gauche">
		<dt>Thème</dt>
		<dd>&nbsp;<?php echo $deliberation['Theme']['libelle']?><br> </dd>
	</div>
	<div class="droite">
		<dt>Service émetteur</dt>
		<dd>&nbsp;<?php echo $deliberation['Service']['libelle']?></dd>
	</div>
</div>


<div class="imbrique">
	<div class="gauche">
		<dt>Num Pref</dt>
		<dd>&nbsp;<?php echo $deliberation['Deliberation']['num_pref']?></dd>
	</div>
	<div class="droite">
	    <dt>Date Séance</dt>
            <dd>
                <?php
                    if(isset($deliberation['Seance'][0])){
                        foreach( $deliberation['Seance'] as  $seance) {
                            echo($seance['Typeseance']['libelle']." : ");
                            echo($this->Html2->ukToFrenchDateWithHour($seance['date']).'<br>');
                        }
                    }
                ?>
	    </dd>
	</div>
</div>

<div class="imbrique">
	<dt>Circuit : <?php echo $deliberation['Circuit']['libelle']?></dt>
		<?php echo $visu;?>

</dd>
</div>
<br/>
<br/>
<br/>

<div class="imbrique">
	<div class="gauche">
		<dt>Rédacteur</dt>
		<dd>&nbsp;<?php echo $this->Html->link($deliberation['Redacteur']['prenom'].' '.$deliberation['Redacteur']['nom'], '/users/view/' .$deliberation['Redacteur']['id'])?></dd>
	</div>
	<div class="droite">
		<dt>Rapporteur</dt>
		<dd>&nbsp;<?php echo $this->Html->link($deliberation['Rapporteur']['prenom'].' '.$deliberation['Rapporteur']['nom'], '/acteurs/view/' .$deliberation['Rapporteur']['id'])?></dd>
	</div>
</div>

<div class="imbrique">
	<div class="gauche">
		<dt>Date création</dt>
		<dd>&nbsp;<?php echo $deliberation['Deliberation']['created']?></dd>
	</div>
	<div class="droite">
		<dt>Date modification</dt>
		<dd>&nbsp;<?php echo $deliberation['Deliberation']['modified']?></dd>
	</div>
</div>

<?php
	echo $this->element('viewTexte', array('type'=>'projet', 'delib'=>$deliberation['Deliberation']));
	echo $this->element('viewTexte', array('type'=>'synthese', 'delib'=>$deliberation['Deliberation']));
	if (empty($deliberation['Multidelib']))
		echo $this->element('viewTexte', array('type'=>'deliberation', 'delib'=>$deliberation['Deliberation']));

	if ($tab_anterieure!=null)
	{
		echo"<dt>Versions Antérieures</dt>";
		foreach ($tab_anterieure as $anterieure)
		{
			echo "<dd>&nbsp;<a href=".$anterieure['lien'].">Version du ".$anterieure['date_version']."</a></dd>";
		}
	}

	if(!empty($commentaires)) {
		echo"<dt>Commentaires</dt><br />";
		foreach ($commentaires as $commentaire){
			echo '<dd><u>'. $commentaire['Commentaire']['prenomAgent'].' '.$commentaire['Commentaire']['nomAgent'] .' <br/></u>';
		    echo $commentaire['Commentaire']['texte'].' ';
		    if ($commentaire['Commentaire']['agent_id'] == $this->Session->read('user.User.id'))
		    	echo $this->Html->link('Supprimer','/commentaires/delete/'.$commentaire['Commentaire']['id'].'/'.$deliberation['Deliberation']['id']);
		    else
		    	echo $this->Html->link('Prendre en compte','/commentaires/prendreEnCompte/'.$commentaire['Commentaire']['id'].'/'.$deliberation['Deliberation']['id']);
			echo '</dd>';
		}
	}

	if(!empty($infosupdefs)) {
		echo '<dt>Informations Suppl&eacute;mentaires </dt>';
		echo '<dd><br>';
		foreach ($infosupdefs as $infosupdef) {
			echo $infosupdef['Infosupdef']['nom'].' : ';
			if (array_key_exists($infosupdef['Infosupdef']['code'], $this->data['Infosup'])) {
				if ($infosupdef['Infosupdef']['type'] == 'richText') {
					if (!empty($this->data['Infosup'][$infosupdef['Infosupdef']['code']])) {
						echo $this->Html->link('[Afficher le texte]', 'javascript:afficheMasqueTexteEnrichi(\'afficheMasque'.$infosupdef['Infosupdef']['code'].'\', \''.$infosupdef['Infosupdef']['code'].'\')', array(
							'id'=> 'afficheMasque'.$infosupdef['Infosupdef']['code'], 'affiche'=>'masque'));
						echo '<div class="annexesGauche"></div>';
						echo '<div class="fckEditorProjet">';
							echo $this->Form->input($infosupdef['Infosupdef']['code'], array('label'=>'', 'type'=>'textarea', 'style'=>'display:none;', 'value'=>$this->data['Infosup'][$infosupdef['Infosupdef']['code']]));
						echo '</div>';
						echo '<div class="spacer"></div>';
					}
				} else
					echo $this->data['Infosup'][$infosupdef['Infosupdef']['code']];
			}
			echo '<br>';
		}
		echo '</dd>';
	}

	if (!empty($historiques)) {
		echo"<dt>Historique</dt><br />";
		foreach ($historiques as $historique){
			echo '<dd>'.$this->Html2->ukToFrenchDateWithHour($historique['Historique']['created']).' '.$historique['Historique']['commentaire'];
			echo '</dd>';
		}
	}
?>

<?php
	if(empty($deliberation['Multidelib']) && !empty($deliberation['Annex'])) {
		echo '<dt>Annexes</dt>';
		echo '<dd><br>';
	 	foreach ($deliberation['Annex'] as $annexe) {
			if ($annexe['titre']) echo '<br>Titre : '.$annexe['titre'];
			echo '<br>Nom fichier : '.$annexe['filename'];
			echo '<br>Joindre au contrôle de légalité : '.($annexe['joindre_ctrl_legalite']?'oui':'non');
			echo '<br>'.$this->Html->link('Telecharger','/annexes/download/'.$annexe['id']).'<br>';
 		}
		echo '</dd>';
	}
?>

</dl>

<?php echo $linkBarre; ?>

</div>
</div>
<script type="text/javascript">
function afficheMasqueTexteEnrichi(lienId, inputId) {
	var lienAfficherMasquer = $('#'+lienId);
	if(lienAfficherMasquer.attr('affiche') == 'masque') {
		var config = {
			readOnly : true,
			toolbar : 'Basic',
			toolbarStartupExpanded : false
		};
		$('#'+inputId).ckeditor(config);
		lienAfficherMasquer.attr('affiche', 'affiche');
		lienAfficherMasquer.html('[Masquer le texte]');
	} else {
		$('#'+inputId).ckeditor(function(){this.destroy();});
		$('#'+inputId).hide();
		lienAfficherMasquer.attr('affiche', 'masque');
		lienAfficherMasquer.html('[Afficher le texte]');
	}
}
</script>
