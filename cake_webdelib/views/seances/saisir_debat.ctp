<h2>Saisie des débats :</h2>
<?php echo $javascript->link('fckeditor/fckeditor'); ?>
<?php echo $form->create('Seance',array('url'=>'/seances/saisirDebat/'.$delib['Deliberation']['id'],'type'=>'file')); ?>

<?php
    $seance_id = $delib['Deliberation']['seance_id'];
    if (!Configure::read('GENERER_DOC_SIMPLE')){
        if ($isCommission) {
            echo '<br>Nom fichier : '.$delib['Deliberation']['commission_name'];
            echo '<br>Taille : '.$delib['Deliberation']['commission_size'];
            if ($delib['Deliberation']['commission_size'] >0){
                echo '<br>'.$html->link('Telecharger le débat','/deliberations/download/'.$delib['Deliberation']['id'].'/commission');
                echo ' '.$html->link('Supprimer le débat','/deliberations/deleteDebat/'.$delib['Deliberation']['id']."/$isCommission");
            }
            echo '<br><br><br>';
            echo  $form->input("Deliberation.texte_doc",array('label'=>'', 'type'=>'file'));
           // echo $form->submit('Importer', array('class'=>'bt_add', 'name'=>'importer', 'div'=>false));
            echo '<br><br>';
        }
        else {
            echo '<br>Nom fichier : '.$delib['Deliberation']['debat_name'];
            echo '<br>Taille : '.$delib['Deliberation']['debat_size'];
            if ($delib['Deliberation']['debat_size'] >0) {
                echo '<br>'.$html->link('Télécharger le débat','/deliberations/download/'.$delib['Deliberation']['id'].'/debat');
                echo ' '.$html->link('Supprimer le débat','/deliberations/deleteDebat/'.$delib['Deliberation']['id']."/$isCommission");
            }
            echo '<br><br><br>';
            echo  $form->input("Deliberation.texte_doc",array('label'=>'', 'type'=>'file'));
          //  echo $form->submit('Importer', array('class'=>'bt_add', 'name'=>'importer', 'div'=>false));
            echo '<br><br>';
        }
       
    }
    if (Configure::read('GENERER_DOC_SIMPLE')) {
      if (!$isCommission) {
?>   

<div class="optional">
    <?php echo $form->input('Deliberation.debat', array('type'=>'textarea', 'cols' => '10', 'rows' => '20'));?>
    <?php echo $form->error('Deliberation.debat', 'Entrer le texte de debat.');?>
    <?php echo $fck->load('data[Deliberation][debat]'); ?>
</div>

<?php   } 
        else {
?>
   <div class="optional">
    <?php echo $form->input('Deliberation.commission', array('type'=>'textarea', 'cols' => '10', 'rows' => '20'));?>
    <?php echo $fck->load('data[Deliberation][commission]'); ?>
</div>



<?php   } 
     } // fin du if ?>



<div class="submit">
   	<?php echo $form->submit('Enregistrer', array('div'=>false, 'class'=>'bt_add', 'name'=>'saisir'));?>
<?php echo $form->end(); ?>
      <?php 
       if($delib['Seance']['traitee']==0) {
           if (!$isCommission) {
                echo $html->link('Retour aux votes', "/seances/details/$seance_id", array('class'=>'link_annuler', 'name'=>'Annuler'), 'Etes vous sur de vous quitter cette page ?');
           } 
	   	   else {
               echo $html->link('Retour aux avis', "/seances/detailsAvis/$seance_id", array('class'=>'link_annuler', 'name'=>'Annuler'), 'Etes vous sur de vous quitter cette page ?');
           }
       }
       else {
               echo $html->link('Retour aux délibérations', "/postseances/afficherProjets/$seance_id", array('class'=>'link_annuler', 'name'=>'Annuler'), 'Etes vous sur de vous quitter cette page ?');
       }
	   
	   ?>
    <?php // echo $form->submit('Annuler', array('class'=>'bt_annuler', 'name'=>'retour', 'onclick'=>"javascript:FermerFenetre2()"));?>
</div>
<?php //$form->end(); ?>
