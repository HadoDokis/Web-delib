<h2><?php echo $html->image('/img/icons/synthese.png')?> Note de Synthèse : </h2>

<div class="pave">
<?php 

    if (Configure::read('GENERER_DOC_SIMPLE'))
        echo $deliberation['Deliberation']['texte_synthese']; 
    else {
         echo '<br>Nom fichier : '.$deliberation['Deliberation']['texte_synthese_name'];
         echo '<br>Taille : '.$deliberation['Deliberation']['texte_synthese_size'];
         echo '<br>'.$html->link('Telecharger','/deliberations/download/'.$deliberation['Deliberation']['id'].'/texte_synthese').'<br><br><br>';
    }
	
?>
</div>

<br/><br/>

<div class="optional">
	<?php if(!empty($annexes)){  ?>
	<?php echo $html->image('/img/icons/bookmark.png').'Annexe(s) :';?>
	<?php foreach ($annexes as $annexe) :
			echo '<br>Nom fichier : '.$annexe['Annex']['filename'];
			echo '<br>Taille : '.$annexe['Annex']['size'];
			echo '<br>'.$html->link('Telecharger','/annexes/download/'.$annexe['Annex']['id']);?><br/><br/>
	<?php endforeach; } ?>
</div>

<div class="submit">
	<?php echo $html->link(SHY, $session->read('user.User.lasturl'), array('class'=>'link_annuler_sans_border', 'title'=>'Retour fiche'), false, false);?>

</div>
