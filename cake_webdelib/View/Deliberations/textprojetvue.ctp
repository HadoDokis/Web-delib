<h2><?php echo $this->Html->image('/img/icons/projet.png')?> Texte Projet : </h2>
<div class="pave">

<?php
    if (Configure::read('GENERER_DOC_SIMPLE'))
        echo $deliberation['Deliberation']['texte_projet'];
    else {
		echo '<br>Nom fichier : '.$deliberation['Deliberation']['texte_projet_name'];
		echo '<br>Taille : '.$deliberation['Deliberation']['texte_projet_size'];
		echo '<br>'.$this->Html->link('Telecharger','/deliberations/download/'.$deliberation['Deliberation']['id'].'/texte_projet').'<br><br><br>';
	 //   echo  $this->Form->file("Deliberation.texte_doc");
     //   echo $this->Form->submit('Importer', array('class'=>'bt_add', 'name'=>'importer'));
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
			echo '<br>'.$this->Html->link('Telecharger','/annexes/download/'.$annexe['Annex']['id']);?> <br/><br/>
	<?php endforeach; } ?>
</div>

<div class="actions">
	<?php 
        echo $this->Html->link('<i class="icon-circle-arrow-left"></i> Retour', 'javascript:history.go(-1)', array('class'=>'btn', 'title'=>'Retour fiche', 'escape' => false))?>

</div>
