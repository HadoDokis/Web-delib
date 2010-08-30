<div id='loading' style="display:none;">&nbsp;</div>
<div id="buttons">
<?php 
    echo $javascript->link('utils.js');     
    echo $javascript->link('noback.js');     
?>
<?php // Initialisation des boutons action de la vue
	$defBarre = array();
	$defBarre[] = array('title'=>'Valider', 'url'=>'/deliberations/traiter/'.$deliberation['Deliberation']['id'].'/1', 'htmlAttributes'=>array('class'=>'link_valider_avec_border', 'title'=>'Valider le projet', 'onclick'=>"disableDiv('buttons');"));
	$defBarre[] = array('title'=>'Refuser', 'url'=>'/deliberations/traiter/'.$deliberation['Deliberation']['id'].'/0', 'htmlAttributes'=>array('class'=>'link_refuser_avec_border', 'title'=>'Refuser le projet', 'onclick'=>"disableDiv('buttons');"));
	 $defBarre[] = array('title'=>"Renvoyer à", 'url'=>'/deliberations/retour/'.$deliberation['Deliberation']['id'], 'htmlAttributes'=>array('class'=>'link_retour_avec_border', 'title'=>'Renvoi du projet'));
	if ($Droits->check($session->read('user.User.id'), 'Deliberations:rebond'))
		$defBarre[] = array('title'=>'Envoyer à', 'url'=>'/deliberations/rebond/' . $deliberation['Deliberation']['id'], 'htmlAttributes'=>array('class'=>'link_rebond_avec_border','title'=>'Envoyer à'));
	$defBarre[] = array();
	$defBarre[] = array('title'=>'Ajouter un commentaire', 'url'=>'/commentaires/add/'.$deliberation['Deliberation']['id'], 'htmlAttributes'=>array('class'=>'link_commentaire_avec_border', 'title'=>'Ajouter un commentaire (affichés plus bas)'));
	$defBarre[] = array();

	if ($Droits->check($session->read('user.User.id'), 'Deliberations:edit'))
		$defBarre[] = array('title'=>'Modifier', 'url'=>'/deliberations/edit/' . $deliberation['Deliberation']['id'], 'htmlAttributes'=>array('class'=>'link_modifier_avec_border','title'=>'Modifier'));


	$defBarre[] = array('title'=>'Annuler', 'url'=>'/deliberations/mesProjetsATraiter', 'htmlAttributes'=>array('class'=>'link_annuler', 'title'=>'Annuler'));

	$linkBarre  = "<table class='table_action' cellspacing='0' cellpadding='0'><tr>";
	$linkBarre .= $menu->linkBarre($defBarre);
	$linkBarre .= "</tr></table>";
?>
<div id="vue_cadre">
	<h3>Valider ou refuser les projets de d&eacute;lib&eacute;ration (id : <?php echo $deliberation['Deliberation']['id']?>)</h3>

<?php echo $linkBarre; ?>
</br>

<dl>
<div class="imbrique">
	<dt>Libellé</dt>
	<dd>&nbsp;<?php echo $deliberation['Deliberation']['objet']?></dd>

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
		<dd>&nbsp;<?php echo $deliberation['Seance']['date']?></dd>
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
		<dd>&nbsp;<?php echo $html->link($deliberation['Redacteur']['prenom'].' '.$deliberation['Redacteur']['nom'], '/users/view/' .$deliberation['Redacteur']['id'])?></dd>
	</div>
	<div class="droite">
		<dt>Rapporteur</dt>
		<dd>&nbsp;<?php echo $html->link($deliberation['Rapporteur']['prenom'].' '.$deliberation['Rapporteur']['nom'], '/acteurs/view/' .$deliberation['Rapporteur']['id'])?></dd>
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

<div class="imbrique">
	<dt>Textes</dt>
	<dd>&nbsp;
		<?php echo $html->link('Projet','/deliberations/textprojetvue/' . $deliberation['Deliberation']['id'], array('class'=>'link_projet', 'title'=>'Projet'), false, false)?>
		&nbsp;
		<?php echo $html->link('Synthèse','/deliberations/textsynthesevue/' . $deliberation['Deliberation']['id'], array('class'=>'link_synthese', 'title'=>'Synthese'), false, false)?>
		&nbsp;
		<?php echo $html->link('Déliberation','/deliberations/deliberationvue/' . $deliberation['Deliberation']['id'], array('class'=>'link_deliberation', 'title'=>'Deliberation'), false, false)?>
	</dd>
</div>

<?php
	if ($tab_anterieure!=null)
	{
		echo"<dt>Versions Antérieures</dt>";
		foreach ($tab_anterieure as $anterieure)
		{
			echo "<dd>&nbsp;<a href=".$anterieure['lien'].">Version du ".$anterieure['date_version']."</a></dd>";
		}
	}
?>

<?php
	if(!empty($commentaires))
	{
		echo"<dt>Commentaires</dt><br />";
		foreach ($commentaires as $commentaire){
			echo '<dd><u>'. $commentaire['Commentaire']['prenomAgent'].' '.$commentaire['Commentaire']['nomAgent'] .' <br/></u>';
		    echo $commentaire['Commentaire']['texte'].' ';
		    if ($commentaire['Commentaire']['agent_id'] == $session->read('user.User.id'))
		    	echo $html->link('Supprimer','/commentaires/delete/'.$commentaire['Commentaire']['id'].'/'.$deliberation['Deliberation']['id']);
		    else
		    	echo $html->link('Prendre en compte','/commentaires/prendreEnCompte/'.$commentaire['Commentaire']['id'].'/'.$deliberation['Deliberation']['id']);
			echo '</dd>';
		}
	}
?>

<?php
    if(!empty($infosupdefs)) {
        echo '<dt>Informations Suppl&eacute;mentaires </dt>';
        echo '<dd><br>';
        foreach ($infosupdefs as $infosupdef) {
            echo $infosupdef['Infosupdef']['nom'].' : ';
            if (array_key_exists($infosupdef['Infosupdef']['code'], $this->data['Infosup'])) {
                if ($infosupdef['Infosupdef']['type'] == 'richText')
                    echo '[Texte enrichi]';
                else
                    echo $this->data['Infosup'][$infosupdef['Infosupdef']['code']];
                }
                echo '<br>';
        }
        echo '</dd>';
    }
?>

<?php
      if (!empty($historiques)) {
          echo"<dt>Historique</dt><br />";
          foreach ($historiques as $historique){
              echo '<dd>'.$html2->ukToFrenchDateWithHour($historique['Historique']['created']).' '.$historique['Historique']['commentaire'];
              echo '</dd>';
          }
      }
?>

<?php if(!empty($deliberation['Annex'])) { ?>
	<dt>Pi&egrave;ces jointes au contr&ocirc;le de l&eacute;galit&eacute;</dt>
	<dd>
 		<?php foreach ($deliberation['Annex'] as $annexe) {
			if ($annexe['type'] == "G"){
				echo '<br>Nom fichier : '.$annexe['filename'];
				echo '<br>Taille : '.$annexe['size'];
				echo '<br>'.$html->link('Telecharger','/annexes/download/'.$annexe['id']).'<br>';
			}
 		}	  ?>
 	</dd>
 <?php } ?>

</dl>

<?php echo $linkBarre; ?>

</div>
</div>
