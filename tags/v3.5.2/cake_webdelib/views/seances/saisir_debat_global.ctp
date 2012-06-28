<h2>Saisie des débats généraux :</h2>
<?php echo $javascript->link('ckeditor/ckeditor'); ?>
<?php echo ('<div class="optional">');

echo $form->create('Seances',array('url'=>'/seances/saisirDebatGlobal/'.$seance['Seance']['id'],'type'=>'file'));
if (!Configure::read('GENERER_DOC_SIMPLE')){
    echo '<br>Nom fichier : '.$seance['Seance']['debat_global_name'];
    echo '<br>Taille : '.$seance['Seance']['debat_global_size'];
    if ($seance['Seance']['debat_global_size']>0) { 
        echo '<br>'.$html->link('Telecharger','/seances/download/'.$html->value('Seance.id').'/debat_global');
        echo ' '.$html->link('Supprimer','/seances/deleteDebatGlobal/'.$html->value('Seance.id').'/');
    }
    echo '<br><br><br>';
    echo $form->input("Seance.texte_doc", array('label'=>'', 'type'=>'file'));
//    echo $form->submit('Importer', array('class'=>'bt_add', 'name'=>'importer', 'div'=>false));
    echo '<br><br>';
}
else {          ?>
    <div class="optional">
        <?php echo $form->input('Seance.debat_global', array('label'=>'', 'type'=>'textarea', 'cols' => '10', 'rows' => '20'));?>
        <?php echo $fck->load('SeanceDebatGlobal'); ?>
    </div>
<?php  } // fin du else ?>

<div class="optional">
	<?php if(!empty($annexes)){  ?>
	<?php echo $form->label('Annexe.titre', 'Annexe(s) :');?>
	<?php foreach ($annexes as $annexe) :
			echo '<br>Titre : '.$annexe['Annex']['titre'];
			echo '<br>Nom fichier : '.$annexe['Annex']['filename'];
			echo '<br>Taille : '.$annexe['Annex']['size'];
			echo '<br>'.$html->link('Telecharger','/annexes/download/'.$annexe['Annex']['id']);?> | <?php echo $html->link('Supprimer','/annexes/delete/'.$annexe['Annex']['id']);?><br/><br/>
	<?php endforeach; } ?>
</div>
<div id="cible">

</div>
<div>
<!--<a href="javascript:add_field()" id="lien_annexe" class="link_annexe">Joindre une annexe</a>-->
</div>
<br>
<div class="submit">
    <?php echo $form->submit('Enregistrer', array('class'=>'bt_add', 'div'=>false, 'name'=>'saisir'));?>
    <?php echo $form->submit('Annuler',     array('class'=>'bt_annuler', 'div'=>false, 'name'=>'annuler', 'onclick'=>"javascript:FermerFenetre2()"));?>
</div>
<?php echo $form->end(); ?>
