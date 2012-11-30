<h2><?php echo $this->Html->image('/img/icons/synthese.png')?> Déliberation : </h2>

<div class="pave">
<?php
    if (Configure::read('GENERER_DOC_SIMPLE'))
        echo $deliberation['Deliberation']['deliberation']; 
    else {
        echo '<br>Nom fichier : '.$deliberation['Deliberation']['deliberation_name'];
        echo '<br>Taille : '.$deliberation['Deliberation']['deliberation_size'];
	echo '<br>'.$this->Html->link('Telecharger','/deliberations/download/'.$deliberation['Deliberation']['id'].'/deliberation').'<br><br><br>';
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

<div class="submit">
	<?php echo $this->Html->link(SHY, $this->Session->read('user.User.lasturl'), array('class'=>'link_annuler_sans_border', 'title'=>'Retour fiche'), false, false);?>

</div>
