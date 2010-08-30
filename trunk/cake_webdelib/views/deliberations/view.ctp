<div id="vue_cadre">

<dl>
    <?php
        if ($this->data['Deliberation']['etat']==3 || $this->data['Deliberation']['etat']==5)
	    echo '<h3>D&eacute;lib&eacute;ration n&deg; '.$this->data['Deliberation']['num_delib'].'</h3>';
	else
	    echo '<h3>Identifiant projet '.$this->data['Deliberation']['id'].'</h3>';
	?>

	<div class="imbrique">
		<dt>Libellé</dt>
		<dd>&nbsp;<?php echo $this->data['Deliberation']['objet']?></dd>

		<dt>Titre</dt>
		<dd>&nbsp;<?php echo $this->data['Deliberation']['titre']?></dd>

		<dt>Etat</dt>
		<dd>&nbsp;<?php echo $this->data['Deliberation']['libelleEtat']?></dd>
	</div>

	<div class="imbrique">
		<div class="gauche">
			<dt>Thème</dt>
			<dd>&nbsp;<?php echo $html->link($this->data['Theme']['libelle'], '/themes/view/' .$this->data['Theme']['id'])?><br> </dd>
		</div>
		<div class="droite">
			<dt>Service émetteur</dt>
			<dd>&nbsp;<?php echo $html->link($this->data['Service']['libelle'], '/services/view/' .$this->data['Service']['id'])?></dd>
		</div>
	</div>

	<div class="imbrique">
		<div class="gauche">
			<dt>Num Pref</dt>
			<dd>&nbsp;<?php echo $this->data['Deliberation']['num_pref']?></dd>
		</div>
		<div class="droite">
			<dt>Date Séance</dt>
			<dd>&nbsp;<?php echo $this->data['Seance']['date']?></dd>
		</div>
	</div>


	<div class="imbrique">
		<dt>Circuit : <?php echo $this->data['Circuit']['libelle']?></dt>
		<dd><?php echo $visu; ?></dd>
	</div>
	<br/>
	<br/>
	<br/>

	<div class="imbrique">
		<div class="gauche">
			<dt>Rédacteur</dt>
			<dd>&nbsp;<?php echo $html->link($this->data['Redacteur']['prenom'].' '.$this->data['Redacteur']['nom'], '/users/view/' .$this->data['Redacteur']['id'])?></dd>
		</div>
		<div class="droite">
			<dt>Rapporteur</dt>
			<dd>&nbsp;<?php echo $this->data['Rapporteur']['prenom'].' '.$this->data['Rapporteur']['nom']?></dd>
		</div>
	</div>



<div class="imbrique">
	<div class="gauche">
		<dt>Date création</dt>
		<dd>&nbsp;<?php echo $this->data['Deliberation']['created']?></dd>
	</div>
	<div class="droite">
		<dt>Date modification</dt>
		<dd>&nbsp;<?php echo $this->data['Deliberation']['modified']?></dd>
	</div>
</div>

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

<?php if(!empty($this->data['Annex'])) { ?>
	<dt>Pi&egrave;ces jointes envoy&eacute;es au contr&ocirc;le de l&eacute;galit&eacute; </dt>
	<dd>
 <?php foreach ($this->data['Annex'] as $annexe) {
			if ($annexe['type'] == "G"){
				echo '<br>Nom fichier : '.$annexe['filename'];
				echo '<br>Taille : '.$annexe['size'];
				echo '<br>'.$html->link('Telecharger','/annexes/download/'.$annexe['id']).'<br>';
			}
 		}	  ?></dd>
 <?php } ?>

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
	if(!empty($commentaires)) {
		echo"<dt>Commentaires</dt><br />";
		foreach ($commentaires as $commentaire){
			echo '<dd><u>'. $commentaire['Commentaire']['prenomAgent'].' '.$commentaire['Commentaire']['nomAgent'] .' </u><br/>';
		    echo $commentaire['Commentaire']['texte'];
			echo '</dd>';
		}
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


</dl>
<ul id="actions_fiche">
	<li><?php echo $html->link(SHY, $session->read('user.User.lasturl'), array('class'=>'link_annuler_sans_border', 'title'=>'Retour fiche'), false, false);?></li>
	<li><?php echo $html->link(SHY,'/deliberations/textprojetvue/' . $this->data['Deliberation']['id'], array('class'=>'link_projet', 'title'=>'Projet'), false, false)?></li>
	<li><?php echo $html->link(SHY,'/deliberations/textsynthesevue/' . $this->data['Deliberation']['id'], array('class'=>'link_synthese', 'title'=>'Synthese'), false, false)?></li>
	<li><?php echo $html->link(SHY,'/deliberations/deliberationvue/' . $this->data['Deliberation']['id'], array('class'=>'link_deliberation', 'title'=>'Deliberation'), false, false)?></li>
	<li>
	<?php
	    if ($userCanEdit)
		    echo $html->link(SHY, '/deliberations/edit/' . $this->data['Deliberation']['id'],array('class'=>'link_modifier','title'=>'Modifier'), false, false);?>
	</li>

</ul>

</div>
