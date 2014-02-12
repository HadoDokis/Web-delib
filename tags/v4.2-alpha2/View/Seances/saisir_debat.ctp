<h2>Saisie des débats</h2>
<?php echo $this->Html->script('ckeditor/ckeditor'); ?>
<?php echo $this->Form->create('Seance',array('url'=>"/seances/saisirDebat/$delib_id/$seance_id",'type'=>'file')); ?>

<?php
    if (!Configure::read('GENERER_DOC_SIMPLE')){
        if ($isCommission) {
            $size = $delib['Deliberation']['commission_size'];
            $name = $delib['Deliberation']['commission_name'];
            $nature = 'commission';
        }
        else {
            $size = $delib['Deliberation']['debat_size'];
            $name = $delib['Deliberation']['debat_name'];
            $nature = 'debat';
        }
        if ($size >0) {
            echo '<br>Nom fichier : ' . $name;
            echo '<br>Taille : '. round($size/1000, 2) .'ko';
            echo '<br>'.$this->Html->link('[Télécharger le débat]','/deliberations/download/'.$delib['Deliberation']['id'].'/'.$nature);
            echo ' '.$this->Html->link('Supprimer le débat','/deliberations/deleteDebat/'.$delib['Deliberation']['id']."/$isCommission/$seance_id");
            echo '<br><br>';
        }
        echo  $this->Form->input('Deliberation.texte_doc', array('label'=>'Nouveau fichier : ', 'type'=>'file'));
    }
    if (Configure::read('GENERER_DOC_SIMPLE')) {
      if (!$isCommission) {
?>   

<div class="optional">
    <?php echo $this->Form->input('Deliberation.debat', array('type'=>'textarea', 'label'=>''));?>
    <?php echo $this->Form->error('Deliberation.debat', 'Entrer le texte de debat.');?>
    <?php echo $this->Fck->load('DeliberationDebat'); ?>
</div>

<?php } else { ?>
   <div class="optional">
    <?php echo $this->Form->input('Deliberation.commission', array('type'=>'textarea', 'label'=>''));?>
    <?php echo $this->Fck->load('DeliberationCommission'); ?>
</div>

<?php } } // fin du if ?>

<br>
<div class="submit">
      <?php
        echo $this->Html->tag("div", null, array("class" => "btn-group", 'style' => 'margin-top:10px;'));
        if(empty($seance['Seance']['traitee'])){
        if($seance['Typeseance']['action']==0)
            echo $this->Html->link('<i class="fa fa-arrow-left"></i> Retour aux votes', "/seances/details/$seance_id", array('class'=>'btn', 'name'=>'Annuler','escape'=>false));
        if($seance['Typeseance']['action']==1)
            echo $this->Html->link('<i class="fa fa-arrow-left"></i> Retour aux avis', "/seances/detailsAvis/$seance_id", array('class'=>'btn', 'name'=>'Annuler','escape'=>false));
        if($seance['Typeseance']['action']==2)
            echo $this->Html->link('<i class="fa fa-arrow-left"></i> Retour aux délibérations', "/seances/detailsAvis/$seance_id", array('class'=>'btn', 'name'=>'Annuler','escape'=>false));
        }else
            echo $this->Html->link('<i class="fa fa-arrow-left"></i> Retour aux délibérations', "/postseances/afficherProjets/$seance_id", array('class'=>'btn', 'name'=>'Annuler','escape'=>false));
	   
        echo $this->Form->button('<i class="fa fa-save"></i> Enregistrer', array('class'=>'btn btn-primary', 'name'=>'saisir','escape'=>false, 'title' => 'Enregistrer'));
        echo $this->Html->tag('/div', null);
        echo $this->Form->end(); 
        ?>
</div>
