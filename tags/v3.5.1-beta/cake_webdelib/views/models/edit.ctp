<?php
echo $javascript->link('ckeditor/ckeditor');

echo $html->tag('h2', 'Modification du mod&egrave;le : '.$libelle);

echo $form->create('Model',array('url'=>'/models/edit/'.$html->value('Model.id'),'type'=>'post'));
	echo $html->tag('div', null, array('class'=>'optional'));
		echo $form->input('Model.content', array('type'=>'textarea', 'label' => ''));
		echo $fck->load('ModelContent');
	echo $html->tag('/div');
	echo '<br/><br/><br/>';
		echo $html->tag('div', null, array('class'=>'submit'));
		echo $form->hidden('Model.id',array('label'=>'&nbsp;'));
		echo $form->submit('Modifier', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Ajouter'));
		echo $html->link('Annuler', '/models/index', array('class'=>'link_annuler', 'name'=>'Annuler'));
	echo $html->tag('/div');
echo $form->end();
?>
