<h2><?php echo $this->Html->image('/img/icons/synthese.png')?> Modèle de génération : </h2>

<div>
    <?php echo $model['Model']['content']; ?>
</div>

<br/><br/>


<div class="submit">
	<?php $this->Html2->boutonRetour('index', 'float:none;');
//        echo $this->Html->link(SHY, '/models/index', array('class'=>'link_annuler_sans_border', 'title'=>'Retour fiche'), false, false);
        ?>
</div>
