<h2>Param&eacute;trage des e-mails</h2>
<?php echo $form->create('Collectivites',array('url'=>$this->webroot.'collectivites/setMails','type'=>'file')); ?>

<table>
  <tr> 
    <td> Projet refus&eacute; </td>
    <td> <?php echo $form->input('Mail.refus', array('type'=>'file', 'size' => '40'))?></td>
    <td> <?php echo $html->link(SHY, $email_path.'refus', array('class'=>'link_voir', 'title'=>'Voir'), false, false) ?></td>
  </tr>
  <tr>
    <td> Projet &agrave; traiter  </td>
    <td> <?php echo $form->input('Mail.traiter', array('type'=>'file', 'size' => '40'))?> </td>
    <td> <?php echo $html->link(SHY, $email_path.'traiter', array('class'=>'link_voir', 'title'=>'Voir'), false, false) ?></td>
  </tr>
  <tr>
    <td> Notification d'insertion dans le circuit  </td>
    <td> <?php echo $form->input('Mail.insertion', array('type'=>'file', 'size' => '40'))?> </td>
    <td> <?php echo $html->link(SHY, $email_path.'insertion', array('class'=>'link_voir', 'title'=>'Voir'), false, false) ?></td>
  </tr>
  <tr>
    <td> Convocation  </td>
    <td> <?php echo $form->input('Mail.convocation', array('type'=>'file', 'size' => '40'))?></td>
    <td> <?php echo $html->link(SHY, $email_path.'convocation', array('class'=>'link_voir', 'title'=>'Voir'), false, false) ?></td>
  </tr>
</table>

    <p> <br/><i>(au format .txt)</i> </p>                                                                      
    <br/><br/>
    <div class="submit">
		<?php echo $form->submit('Enregistrer', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Ajouter'));?>
		<?php echo $html->link('Annuler', '/collectivites/index', array('class'=>'link_annuler', 'name'=>'Annuler'))?>
    </div>
          
<?php echo $form->end(); ?>
