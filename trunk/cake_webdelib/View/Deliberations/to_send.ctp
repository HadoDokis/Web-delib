<div class="deliberations">
<?php echo $this->Html->script('utils.js'); ?>
<?php
    if ((@$this->params['filtre'] != 'hide' ) &&
        ($this->params['action'] !='mesProjetsRecherche') &&
        ($this->params['action'] !='tousLesProjetsRecherche') )
        echo $this->element('filtre');
?>


<?php if (isset($message))  echo ($message); 
  if ($this->action=='autreActesAEnvoyer')
        echo ('<h2>Télétransmission des actes</h2>');
    elseif ($this->action == 'toSend')
        echo ('<h2>Télétransmission des délibérations</h2>');
?>
<?php echo $this->Form->create('Deliberation',array('type'=>'file','url'=>'/deliberations/sendActe')); ?>
    La Classification enregistrée date du <?php echo $this->Html->link($dateClassification,'/deliberations/getClassification/', array('title'=>'Date classification'))?><br /><br />
	<table width='100%'>
<tr>
	<th></th>
 	<th>Numéro Généré</th>
 	<th>Libellé de l'acte</th>
 	<th>Titre</th>
 	<th>Classification</th>
 	<th>statut</th>
</tr>
<?php
           $numLigne = 1;
           foreach ($deliberations as $delib) {
		             $rowClass = ($numLigne & 1)?array('height' => '36px'):array( 'height' => '36px', 'class'=>'altrow');
	          echo $this->Html->tag('tr', null, $rowClass);
	          $numLigne++;

		if ($delib['Deliberation']['etat']!= 5)
			echo("<td>".$this->Form->checkbox('Deliberation.id_'.$delib['Deliberation']['id'])."</td>");
		else
		    echo("<td></td>");

                echo "<td>".$this->Html->link($delib['Deliberation']['num_delib'], '/deliberations/downloadDelib/'.$delib['Deliberation']['id']);
		?>
		</td>
		<td><?php echo $delib['Deliberation']['objet_delib']; ?></td>
		<td><?php echo $delib['Deliberation']['titre']; ?></td>
                       
		<td><?php 
                          $id_num_pref = $delib['Deliberation']['id'].'_num_pref';
                          echo $this->Form->input('Deliberation.'.$id_num_pref, array('label'=>false, 
                                                                                      'div'=>false, 
                                                                                      'id'=>$delib['Deliberation']['id'].'classif1', 
                                                                                      'size' => '60',
                                                                                      'disabled'=>'disabled', 
                                                                                      'value' => $delib['Deliberation'][$id_num_pref] ));?><br/>
		<a class="list_form" href="#add" onclick="javascript:window.open('<?php echo $this->base;?>/deliberations/classification?id=<?php echo $delib['Deliberation']['id'];?>', 'Classification', 'scrollbars=yes,,width=570,height=450');" id="<?php echo $delib['Deliberation']['id']; ?> _classification_text">[Choisir la classification]</a>
		 <?php 
		         echo $this->Form->hidden('Deliberation.'.$delib['Deliberation']['id'].'_num_pref',array('id'=>$delib['Deliberation']['id'].'classif2','name'=>$delib['Deliberation']['id'].'classif2')); 
                 ?>
		 </td>
		   <?php
		        if ($delib['Deliberation']['etat']== 5) {
			   $tdt_id = $delib['Deliberation']['tdt_id'];
			   echo  ("<td><a href='https://$host/modules/actes/actes_transac_get_status.php?transaction=$tdt_id'>envoye</a></td>");
			}
		        else
 		            echo("<td>non envoyé</td>");
		   ?>
		</tr>
<?php	 } ?>

	</table>
	<br />

	<div class="submit">
            <?php echo $this->Form->button('<i class="icon-cloud-upload"></i> Envoyer',array('escape'=>false, 'type'=>'submit','class'=>'btn btn-primary'));?>
	</div>

<?php echo $this->Form->end(); ?>
</div>
