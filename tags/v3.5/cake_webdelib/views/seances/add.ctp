<?php echo $javascript->link('calendrier.js'); ?>
<h2>Nouvelle s&eacute;ance</h2>
<?php echo $form->create('Seance',array('action'=>'add','type'=>'post','name'=>'Session')); ?>
<div class="required">
    <label>Date : </label>
    <input name="date" size="9"
    <?php
        if (isset($date))    echo ("value =\"$date\"");
     ?>/>&nbsp;
	<a href="javascript:show_calendar('Session.date','f');">	<?php echo $html->image("calendar.png", array('style'=>"border='0'")); ?></a> &agrave;
	<?php echo $form->hour('Seance.date',true,null); ?>h<?php echo $form->minute('Seance.date',null,null); ?>min
</div>
<br />
<div class="required">
	<?php echo $form->input('Seance.type_id',array('label'=>'Type de s&eacute;ance <acronym title="obligatoire">(*)</acronym>','options'=>$typeseances,'value'=>$html->value('Seance.type_id'),'empty'=>true)); ?>
</div>
<br/>
<div class="submit">
	<?php echo $form->label('Seance.id', '&nbsp;'); ?>
	<?php echo $form->submit('Ajouter', array('div'=>false, 'class'=>'bt_add', 'name'=>'Ajouter')); ?>
	<?php echo $html->link('Annuler', '/seances/listerFuturesSeances', array('class'=>'link_annuler', 'title'=>'Annuler')); ?>
</div>
<?php $form->end(); ?>
