<?php echo $this->Form->create('Deliberation',array('type'=>'post','url'=>"/deliberations/listerPresents/$delib_id/$seance_id")); ?>
<table cellpadding="0" cellspacing="0">
<tr>
	<th>Elu</th>
	<th>Présent</th>
	<th>Mandataire</th>
</tr>
<?php foreach ($presents as $present):
     $options = array();
     $suppleant_id = $present['Acteur']['suppleant_id'];
     $pres = $present['Acteur']['id']; 
     if (($suppleant_id != null) || isset($present['Acteur']['is_suppleant'])) {
         if (isset($present['Suppleant']['id'])){
             $options[$suppleant_id] = "Suppléant : ".$present['Suppleant']['prenom'].' '.$present['Suppleant']['nom'];
             $options[$pres] = "Titulaire : ".$present['Acteur']['prenom'].' '.$present['Acteur']['nom'];
         }
         else {
             $options[$present['Acteur']['id']] = "Suppléant : ".$present['Acteur']['prenom'].' '.$present['Acteur']['nom'];
             $options[$present['Titulaire']['id']] = "Titulaire : ".$present['Titulaire']['prenom'].' '.$present['Titulaire']['nom'];
         }
     }
   
?>
<tr>
    <td>
        <?php 
        
        if (($suppleant_id != null) || isset($present['Acteur']['is_suppleant'])) {
            echo $this->Form->input('Acteur.'.$present['Acteur']['id'].'.suppleant_id', array('options' =>  $options, 'label' => false));             
        }
        else
            echo $present['Acteur']['prenom'].' '.$present['Acteur']['nom']; 
        ?>
    </td>
	<td>
           <?php 
               $selected = $present['Listepresence']['present'];
 echo $this->Form->input("Acteur.$pres.present", array('label'=>false, 'fieldset'=>false, 'legend'=>false, 'div'=>false, 'type'=>'radio', 'value' => $selected,'options'=>array(1=>'oui',0=>'non'),  'onclick'=>"javascript:disable('liste_$pres', $(this).val() );"));
 ?>
	</td>
	<td>
 	   <?php
	   if (empty($present['Acteur']['id']))
	       echo $this->Form->input("Acteur.".$present['Acteur']['id'].'.mandataire', array('label'=>false, 'options'=>$mandataires, 'readonly'=>'readonly', "id"=>"liste_".$present['Acteur']['id'],'empty'=>true));
	   else
               if($present['Listepresence']['mandataire']!= 0) 
	           echo $this->Form->input("Acteur.".$present['Acteur']['id'].'.mandataire', array('label'=>false, 'options'=>$mandataires, "id"=>"liste_".$present['Acteur']['id'], 'empty'=>true, 'selected' => $present['Listepresence']['mandataire']));
               else
	           echo $this->Form->input("Acteur.".$present['Acteur']['id'].'.mandataire', array('label'=>false, 'options'=>$mandataires, "id"=>"liste_".$present['Acteur']['id'], 'empty'=>true));
	   ?>
    </td>
</tr>
<?php endforeach; ?>
</table>
<br />
<div class="submit">
	<?php echo $this->Form->submit('Enregistrer la liste des présents', array('div'=>false, 'class'=>'bt_add', 'name'=>'modifier'));   ?>
    <?php 
          echo $this->Html->link('Récupérer la liste des présents de la délibération précédente', 
                                 "/deliberations/copyFromPrevious/$delib_id/$seance_id",
                                 array('class' => 'bt_add')); 
    ?>
</div>
<br />
<?php echo $this->Form->end(); ?>
