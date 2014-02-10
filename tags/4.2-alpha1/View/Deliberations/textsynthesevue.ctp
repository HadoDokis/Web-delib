<h2><?php echo $this->Html->image('/img/icons/synthese.png')?> Note de Synthèse : </h2>

<div class="pave">
<?php 

    if (Configure::read('GENERER_DOC_SIMPLE'))
        echo $deliberation['Deliberation']['texte_synthese']; 
    else {
         echo '<br>Nom fichier : '.$deliberation['Deliberation']['texte_synthese_name'];
         echo '<br>Taille : '.$deliberation['Deliberation']['texte_synthese_size'];
         echo '<br>'.$this->Html->link('Telecharger','/deliberations/download/'.$deliberation['Deliberation']['id'].'/texte_synthese').'<br><br><br>';
    }
	
?>
</div>

<br/><br/>

<div class="optional">
	<?php if(!empty($annexes)){  ?>
	<?php echo $this->Html->image('/img/icons/bookmark.png').'Annexe(s) :';?>
	<?php foreach ($annexes as $annexe) :
			echo '<br>Nom fichier : '.$annexe['Annex']['filename'];
			echo '<br>Taille : '.$annexe['Annex']['size'];
			echo '<br>'.$this->Html->link('Telecharger','/annexes/download/'.$annexe['Annex']['id']);?><br/><br/>
	<?php endforeach; } ?>
</div>

<div class="actions">
	<?php echo $this->Html->link('<i class="fa fa-arrow-left"></i> Retour', 'javascript:history.go(-1)', array('class'=>'btn', 'title'=>'Retour fiche', 'escape' => false));?>

</div>
