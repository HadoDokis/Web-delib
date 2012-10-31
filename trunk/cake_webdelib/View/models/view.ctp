<h2><?php echo $html->image('/img/icons/synthese.png')?> Modèle de génération : </h2>

<div>
    <?php echo $model['Model']['content']; ?>
</div>

<br/><br/>


<div class="submit">
	<?php echo $html->link(SHY, '/models/index', array('class'=>'link_annuler_sans_border', 'title'=>'Retour fiche'), false, false);?>
</div>
