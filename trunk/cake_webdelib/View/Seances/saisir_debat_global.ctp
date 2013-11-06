<h2>Saisie des débats généraux</h2>
<?php //echo $this->Html->script('ckeditor/ckeditor'); ?>
<?php echo ('<div class="optional">');
echo $this->Form->create('Seances', array('url'=>'/seances/saisirDebatGlobal/'.$seance['Seance']['id'],'type'=>'file'));
if (!Configure::read('GENERER_DOC_SIMPLE')){
    if ($seance['Seance']['debat_global_size'] > 0) {
        echo '<br>Nom fichier : '.$seance['Seance']['debat_global_name'];
        echo '<br>Taille : '. round($seance['Seance']['debat_global_size']/1000, 2). 'ko';
        echo '<br>'.$this->Html->link('[Telecharger]',"/seances/download/$seance_id/debat_global");
        echo ' '.$this->Html->link('[Supprimer]', "/seances/deleteDebatGlobal/$seance_id");
        echo '<br><br>';
    }
    echo $this->Form->input("Seance.texte_doc", array('label'=>'Nouveau fichier : ', 'type'=>'file'));
}
else {
?>
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
     echo $this->Html->tag("div", null, array("class" => "btn-group"));
     echo $this->Html->link('<i class="icon-circle-arrow-left"></i> Retour aux seances', "/seances/listerFuturesSeances", array('class'=>'btn', 'name'=>'Annuler','escape'=>false), 'Etes vous sur de vous quitter cette page ?');
     echo $this->Form->button('<i class="icon-save"></i> Enregistrer', array('class'=>'btn btn-primary', 'name'=>'saisir','escape'=>false));
     echo $this->Form->end();
     echo $this->Html->tag('/div', null);
 ?>
 </div>