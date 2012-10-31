<h2>Saisie des débats généraux :</h2>
<?php echo $this->Html->script('ckeditor/ckeditor'); ?>
<?php echo ('<div class="optional">');

echo $this->Form->create('Seances',array('url'=>'/seances/saisirDebatGlobal/'.$seance['Seance']['id'],'type'=>'file'));
if (!Configure::read('GENERER_DOC_SIMPLE')){
    echo '<br>Nom fichier : '.$seance['Seance']['debat_global_name'];
    echo '<br>Taille : '.$seance['Seance']['debat_global_size'];
    if ($seance['Seance']['debat_global_size']>0) { 
        echo '<br>'.$this->Html->link('Telecharger',"/seances/download/$seance_id/debat_global");
        echo ' '.$this->Html->link('Supprimer', "/seances/deleteDebatGlobal/$seance_id");
    }
    echo '<br><br><br>';
    echo $this->Form->input("Seance.texte_doc", array('label'=>'', 'type'=>'file'));
//    echo $this->Form->submit('Importer', array('class'=>'bt_add', 'name'=>'importer', 'div'=>false));
    echo '<br><br>';
}
else {          ?>
    <div class="optional">
        <?php echo $this->Form->input('Seance.debat_global', array('label'=>'', 'type'=>'textarea', 'cols' => '10', 'rows' => '20'));?>
        <?php echo $this->Fck->load('SeanceDebatGlobal'); ?>
    </div>
<?php  } // fin du else ?>

<div class="optional">
	<?php if(!empty($annexes)){  ?>
	<?php echo $this->Form->label('Annexe.titre', 'Annexe(s) :');?>
	<?php foreach ($annexes as $annexe) :
			echo '<br>Titre : '.$annexe['Annex']['titre'];
			echo '<br>Nom fichier : '.$annexe['Annex']['filename'];
			echo '<br>Taille : '.$annexe['Annex']['size'];
			echo '<br>'.$this->Html->link('Telecharger','/annexes/download/'.$annexe['Annex']['id']);?> | <?php echo $this->Html->link('Supprimer','/annexes/delete/'.$annexe['Annex']['id']);?><br/><br/>
	<?php endforeach; } ?>
</div>
<div id="cible">

</div>
<div>
<!--<a href="javascript:add_field()" id="lien_annexe" class="link_annexe">Joindre une annexe</a>-->
</div>
<br>
<div class="submit">
<?php
     echo $this->Form->submit('Enregistrer', array('class'=>'bt_add', 'div'=>false, 'name'=>'saisir'));
     echo $this->Form->end();
     echo $this->Html->link('Retour aux seances', "/seances/listerFuturesSeances", array('class'=>'link_annuler', 'name'=>'Annuler'), 'Etes vous sur de vous quitter cette page ?');
?>
 </div>