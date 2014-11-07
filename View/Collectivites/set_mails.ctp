<h2>Paramétrage des e-mails</h2>
<?php echo $this->Form->create('Collectivites',array('url'=>$this->webroot.'collectivites/setMails','type'=>'file')); ?>

<table>
  <tr> 
    <td> Projet refusé </td>
    <td> <?php echo $this->Form->input('Mail.refus', array('type'=>'file', 'size' => '40'))?></td>
    <td> <?php echo $this->Html->link(SHY, $email_path.'refus', array('class'=>'link_voir', 'title'=>'Voir'), false, false) ?></td>
  </tr>
  <tr>
    <td> Projet &agrave; traiter  </td>
    <td> <?php echo $this->Form->input('Mail.traiter', array('type'=>'file', 'size' => '40'))?> </td>
    <td> <?php echo $this->Html->link(SHY, $email_path.'traiter', array('class'=>'link_voir', 'title'=>'Voir'), false, false) ?></td>
  </tr>
  <tr>
    <td> Notification d'insertion dans le circuit  </td>
    <td> <?php echo $this->Form->input('Mail.insertion', array('type'=>'file', 'size' => '40'))?> </td>
    <td> <?php echo $this->Html->link(SHY, $email_path.'insertion', array('class'=>'link_voir', 'title'=>'Voir'), false, false) ?></td>
  </tr>
  <tr>
    <td> Convocation  </td>
    <td> <?php echo $this->Form->input('Mail.convocation', array('type'=>'file', 'size' => '40'))?></td>
    <td> <?php echo $this->Html->link(SHY, $email_path.'convocation', array('class'=>'link_voir', 'title'=>'Voir'), false, false) ?></td>
  </tr>
</table>

    <p> <br/><i>(au format .txt)</i> </p>                                                                      
    <br/><br/>
    <div class="submit">
        <?php $this->Html2->boutonsSaveCancel(); ?>
    </div>
          
<?php echo $this->Form->end(); ?>
