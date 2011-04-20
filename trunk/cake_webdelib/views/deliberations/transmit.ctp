<script>
    document.getElementById("pourcentage").style.display='none';
    document.getElementById("progrbar").style.display='none';
    document.getElementById("affiche").style.display='none';
    document.getElementById("contTemp").style.display='none';
</script>

<?php echo $javascript->link('utils.js'); ?>
<div class="deliberations">

<?php if (isset($message))  echo ($message); ?>
<h2>T&eacute;l&eacute;transmission des d&eacute;lib&eacute;rations</h2>
    La Classification enregistrée date du <?php echo $dateClassification ?> <br /><br />
    <?php echo  $nbDelibs ?> d&eacute;lib&eacute;rations t&eacute;l&eacute;transmises <br /><br />
	<table>
 	<th>N° délibération</th>
 	<th>Objet</th>
 	<th>Titre</th>
 	<th>Classification</th>
 	<th>statut</th>
 	<th>Courrier Ministériel</th>
<tr>
<?php
	foreach ($deliberations as $delib) {
	     echo "<td>".$html->link($delib['Deliberation']['num_delib'], '/deliberations/downloadDelib/'.$delib['Deliberation']['id']);
		?>
		</td>
		<td>
		<?php echo $delib['Deliberation']['objet']; ?>
		</td>
		<td>
		<?php echo ($delib['Deliberation']['titre']); ?>
		</td>

		<td><?php echo $delib['Deliberation']['num_pref']; ?></td>
		<td>
		   <?php 
		       if ($delib['Deliberation']['code_retour'] ==4)
		           echo $html->link("Acquitement reçu le ".$delib['Deliberation']['dateAR'], '/deliberations/getAR/'.$delib['Deliberation']['tdt_id']); 
		       elseif($delib['Deliberation']['code_retour']==3)
		           echo 'Transmis';
		       elseif ($delib['Deliberation']['code_retour']==2)
		           echo 'En attente de transmission';
		       elseif ($delib['Deliberation']['code_retour']==1)
		           echo 'Posté';
	           ?>
		</td> 
                <td>
                    <?php
                        if (!empty($delib['TdtMessage'])) {
                            foreach ($delib['TdtMessage'] as $message){
                                $url_newMessage = "https://".Configure::read("HOST")."/modules/actes/actes_transac_show.php?id=".$message['message_id'];
                                if ($message['type_message'] ==2 )
                                    echo $html->link("Courrier simple", $url_newMessage)."<br />";
                                if ($message['type_message'] ==3 )
                                    echo $html->link("Demande de pièces complémentaires", $url_newMessage)."<br />";
                                if ($message['type_message'] == 4 )
                                    echo $html->link("Lettre d'observation", $url_newMessage)."<br />";
                                if ($message['type_message'] == 5 )
                                    echo $html->link("Déféré au tribunal administratif", $url_newMessage)."<br />";
                            }
                        }
                    ?>
                </td>
	</tr>
<?php	 } ?>

	</table>
	<br />
         <?php 
	     if (isset($previous))
	         echo $html->link('<--    ', "/deliberations/transmit/null/$previous"); 
	     if (isset($next))
	     echo $html->link('    -->', "/deliberations/transmit/null/$next"); 
	 ?>
	     
</div>
