<?php echo $javascript->link('utils.js'); ?>
<h1>Modification d'une s&eacute;ance</h1>
<?php echo $form->create('Seance',array('url'=>'/seances/edit/'.$html->value('Seance.id'),'type'=>'post')); ?>

<div class='onglet'>
        <a href="#" id="emptylink" alt=""></a>
        <a href="javascript:afficheOnglet(1)" id='lienTab1' class="ongletCourant">Date de séance</a>
        <a href="javascript:afficheOnglet(2)" id='lienTab2'>Informations supplémentaires</a>
</div>

<div id="tab1">
<div class="required">
    <?php echo $form->label('Seance.date', 'Date');?>
    <?php echo $form->day('Seance.date',null,null,false); ?>-
    <?php echo $form2->monthOptionTagFr('Seance.date',null,$html->value('Seance.date'),null,null,false); ?>-
    <?php echo $form->year('Seance.date',2009,date('Y')+10); ?>&agrave; 
    <?php echo $form->hour('Seance.date',true, null, null); ?>h
    <?php echo $form->minute('Seance.date',null,null,false); ?>min
</div>
<br />

<div class="required">
    <?php echo $form->input('Seance.type_id', array('label'=>'Type de s&eacute;ance',
                                                    'options'=>$typeseances, 
                                                    'default'=>$html->value('Seance.type_id'), 
                                                    'empty'=>true));
    ?>
</div>

<br />

<div class="submit">
	<?php echo $form->hidden('Seance.id');?>
		<?php echo $form->submit('Enregistrer', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Ajouter'));?>
	<?php echo $html->link('Annuler', '/seances/listerFuturesSeances', array('class'=>'link_annuler', 'title'=>'Annuler'))?>
</div>
</div>
<div id="tab2"  style="display: none;">
    Commentaire : <br />
    Annexe : <br />
</div>
<!--
<ul class="actions">
	<li><?php echo $html->link('Supprimer','/seances/delete/' . $html->value('Seance.id'), null, 'Etes-vous sur de vouloir supprimer la seance du "' . $html->value('Seance.date').'" ?');?> 
	
	
	<li><?php echo $html->link('Annuler', '/seances/index')?></li>
</ul>
-->
<?php echo $form->end(); ?>
