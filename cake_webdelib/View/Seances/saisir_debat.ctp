<h2>Saisie des débats :</h2>
<?php echo $this->Html->script('ckeditor/ckeditor'); ?>
<?php echo $this->Form->create('Seance',array('url'=>"/seances/saisirDebat/$delib_id/$seance_id",'type'=>'file')); ?>

<?php
    if (!Configure::read('GENERER_DOC_SIMPLE')){
        if ($isCommission) {
            echo '<br>Nom fichier : '.$delib['Deliberation']['commission_name'];
            echo '<br>Taille : '.$delib['Deliberation']['commission_size'];
            if ($delib['Deliberation']['commission_size'] >0){
                echo '<br>'.$this->Html->link('Telecharger le débat','/deliberations/download/'.$delib['Deliberation']['id'].'/commission');
                echo ' '.$this->Html->link('Supprimer le débat','/deliberations/deleteDebat/'.$delib['Deliberation']['id']."/$isCommission/$seance_id");
            }
            echo '<br><br><br>';
            echo  $this->Form->input("Deliberation.texte_doc",array('label'=>'', 'type'=>'file'));
           // echo $this->Form->submit('Importer', array('class'=>'bt_add', 'name'=>'importer', 'div'=>false));
            echo '<br><br>';
        }
        else {
            echo '<br>Nom fichier : '.$delib['Deliberation']['debat_name'];
            echo '<br>Taille : '.$delib['Deliberation']['debat_size'];
            if ($delib['Deliberation']['debat_size'] >0) {
                echo '<br>'.$this->Html->link('Télécharger le débat','/deliberations/download/'.$delib['Deliberation']['id'].'/debat');
                echo ' '.$this->Html->link('Supprimer le débat','/deliberations/deleteDebat/'.$delib['Deliberation']['id']."/$isCommission/$seance_id");
            }
            echo '<br><br><br>';
            echo  $this->Form->input("Deliberation.texte_doc",array('label'=>'', 'type'=>'file'));
          //  echo $this->Form->submit('Importer', array('class'=>'bt_add', 'name'=>'importer', 'div'=>false));
            echo '<br><br>';
        }
       
    }
    if (Configure::read('GENERER_DOC_SIMPLE')) {
      if (!$isCommission) {
?>   

<div class="optional">
    <?php echo $this->Form->input('Deliberation.debat', array('type'=>'textarea', 'label'=>''));?>
    <?php echo $this->Form->error('Deliberation.debat', 'Entrer le texte de debat.');?>
    <?php echo $this->Fck->load('DeliberationDebat'); ?>
</div>

<?php   } 
        else {
?>
   <div class="optional">
    <?php echo $this->Form->input('Deliberation.commission', array('type'=>'textarea', 'label'=>''));?>
    <?php echo $this->Fck->load('DeliberationCommission'); ?>
</div>



<?php   } 
     } // fin du if ?>



<div class="submit">
   	<?php echo $this->Form->submit('Enregistrer', array('div'=>false, 'class'=>'bt_add', 'name'=>'saisir'));?>
<?php echo $this->Form->end(); ?>
      <?php 
       if($seance['Seance']['traitee']==0) {
           if (!$isCommission) {
                echo $this->Html->link('Retour aux votes', "/seances/details/$seance_id", array('class'=>'link_annuler', 'name'=>'Annuler'), 'Etes vous sur de vous quitter cette page ?');
           } 
	   	   else {
               echo $this->Html->link('Retour aux avis', "/seances/detailsAvis/$seance_id", array('class'=>'link_annuler', 'name'=>'Annuler'), 'Etes vous sur de vous quitter cette page ?');
           }
       }
       else {
               echo $this->Html->link('Retour aux délibérations', "/postseances/afficherProjets/$seance_id", array('class'=>'link_annuler', 'name'=>'Annuler'), 'Etes vous sur de vous quitter cette page ?');
       }
	   
	   ?>
    <?php // echo $this->Form->submit('Annuler', array('class'=>'bt_annuler', 'name'=>'retour', 'onclick'=>"javascript:FermerFenetre2()"));?>
</div>
<?php //$this->Form->end(); ?>
