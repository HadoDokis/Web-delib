<script>
    document.getElementById("pourcentage").style.display='none';
    document.getElementById("progrbar").style.display='none';
    document.getElementById("affiche").style.display='none';
    document.getElementById("contTemp").style.display='none';
</script>

<?php echo $this->Html->script('utils.js'); ?>
<div class="deliberations">
<?php
        echo $this->element('filtre');
?>

<?php 
    if ($this->action=='autreActesEnvoyes')
        echo ('<h2>T&eacute;l&eacute;transmission des actes</h2>');
    elseif ($this->action == 'transmit')
        echo ('<h2>T&eacute;l&eacute;transmission des d&eacute;lib&eacute;rations</h2>');
?>
    La Classification enregistrée date du <?php echo $dateClassification ?> <br /><br />
    <table width="100%">
<tr>
 	<th><?php 
               if ($this->action=='autreActesEnvoyes')
                  echo  $this->Paginator->sort('num_delib', 'N° de l\'acte').'</th>';
               else
                  echo  $this->Paginator->sort('num_delib', 'N° délibération'); ?></th>
 	<th><?php echo  $this->Paginator->sort('objet_delib', "Libellé de l'acte"); ?></th>
        
 	<th>
        <?php 
            if ($this->action == 'autreActesEnvoyes')
                echo  $this->Paginator->sort('Deliberation.date_acte', 'Date de décision'); 
            else
                echo  $this->Paginator->sort('Seance.date', 'Date de séance'); 
        ?>
        </th>
 	<th><?php echo  $this->Paginator->sort('titre', 'Titre'); ?></th>
 	<th><?php echo  $this->Paginator->sort('num_pref', 'Classification'); ?></th>
 	<th><?php echo  $this->Paginator->sort('tdt_id', 'Statut'); ?></th>
 	<th>Courrier Ministériel</th>
</tr>
<?php
           $numLigne = 1;
           foreach ($deliberations as $delib) {
               $rowClass = ($numLigne & 1)?array('height' => '36px'):array( 'height' => '36px', 'class'=>'altrow');
	       echo $this->Html->tag('tr', null, $rowClass);
	       $numLigne++;
	       echo "<td>".$this->Html->link($delib['Deliberation']['num_delib'], '/deliberations/getTampon/'.$delib['Deliberation']['tdt_id']);
?>
		</td>
		<td><?php echo $delib['Deliberation']['objet_delib']; ?></td>
		<td>
                <?php 
                   if ($this->action == 'autreActesEnvoyes')
                       echo $this->Form2->ukToFrenchDateWithHour($delib['Deliberation']['date_acte']); 
                   else
                       echo $this->Html2->ukToFrenchDateWithHour($delib['Seance']['date']); 
                ?>
                </td>
		<td><?php echo $delib['Deliberation']['titre']; ?></td>
		<td><?php echo $delib['Deliberation']['num_pref']; ?></td>
		<td>
		   <?php 
                    if (isset($delib['Deliberation']['code_retour'])) {
		       if ($delib['Deliberation']['code_retour'] ==4)
		           echo $this->Html->link("Acquitement reçu le ".$delib['Deliberation']['dateAR'], '/deliberations/getAR/'.$delib['Deliberation']['tdt_id']); 
		       elseif($delib['Deliberation']['code_retour']==3)
		           echo 'Transmis';
		       elseif ($delib['Deliberation']['code_retour']==2)
		           echo 'En attente de transmission';
		       elseif ($delib['Deliberation']['code_retour']==1)
		           echo 'Posté';
                    }
	           ?>
		</td> 
                <td>
                    <?php
                        if (!empty($delib['TdtMessage'])) {
                            foreach ($delib['TdtMessage'] as $message){
                                $url_newMessage = "https://".Configure::read("HOST")."/modules/actes/actes_transac_show.php?id=".$message['message_id'];
                                if ($message['type_message'] ==2 )
                                    echo $this->Html->link("Courrier simple", $url_newMessage,array('target' => '_blank'))."<br />";
                                if ($message['type_message'] ==3 )
                                    echo $this->Html->link("Demande de pièces complémentaires", $url_newMessage,array('target' => '_blank'))."<br />";
                                if ($message['type_message'] == 4 )
                                    echo $this->Html->link("Lettre d'observation", $url_newMessage,array('target' => '_blank'))."<br />";
                                if ($message['type_message'] == 5 )
                                    echo $this->Html->link("Déféré au tribunal administratif", $url_newMessage,array('target' => '_blank'))."<br />";
                            }
                        }
                    ?>
                </td>
	</tr>
<?php	 } ?>

	</table>
<div class='paginate'>
        <!-- Affiche les numéros de pages -->
        <?php echo $this->Paginator->numbers(); ?>
        <!-- Affiche les liens des pages précédentes et suivantes -->
        <?php
                echo $this->Paginator->prev('« Précédent ', null, null, array( 'tag' => 'span', 'class' => 'disabled'));
                echo $this->Paginator->next(' Suivant »', null, null, array( 'tag' => 'span', 'class' => 'disabled'));
        ?>
        <!-- Affiche X de Y, où X est la page courante et Y le nombre de pages -->
        <?php echo $this->Paginator->counter(array('format'=>'Page %page% sur %pages%')); ?>
</div>


	<br />
         <?php 
	     if (isset($previous))
	         echo $this->Html->link('<--    ', "/deliberations/transmit/null/$previous"); 
	     if (isset($next))
	     echo $this->Html->link('    -->', "/deliberations/transmit/null/$next"); 
	 ?>
	     
</div>
