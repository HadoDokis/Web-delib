<?php echo $javascript->link('utils.js'); ?>
<div class="deliberations">
<?php if (isset($message))  echo ($message); ?>
<h2>Actes envoy&eacute;s &agrave; la signature</h2>
<?php echo $form->create('Deliberation',array('url'=>'/deliberations/sendToParapheur/'.$seance_id,'type'=>'file')); ?>
<table width='100%'>
    <tr>
	<th></th>
 	<th>Num�ro D&eacute;lib&eacute;ration</th>
 	<th>Libell� de l'acte</th>
 	<th>Titre</th>
 	<th width='65px'>statut</th>
    </tr>

<?php
       $numLigne = 1;
       foreach ($deliberations as $delib) {
          $rowClass = ($numLigne & 1)?array('height' => '36px'):array( 'height' => '36px', 'class'=>'altrow');
       echo $html->tag('tr', null, $rowClass);
       $numLigne++;

		if ((($delib['Deliberation']['etat_parapheur']==null) || ($delib['Deliberation']['etat_parapheur']== -1) ) && 
                    ($delib['Deliberation']['signee']!=1) &&  
                     (($delib['Deliberation']['etat']>=3) || ($delib['Deliberation']['nature_id']>1 && $delib['Deliberation']['etat']>=2) )) 
		    echo("<td>".$form->checkbox('Deliberation.id_'.$delib['Deliberation']['id'], array('checked'=> true))."</td>");
		else
		    echo("<td></td>");
                    echo "<td>";
                    echo $html->link($delib['Deliberation']['num_delib'],'/models/generer/' .$delib['Deliberation']['id'].'/null/'.$delib['Model']['id']);
                    
		?>
		</td>
		<td>
		<?php echo ($delib['Deliberation']['objet_delib']) ; ?>
		</td>
		<td>
		<?php echo ($delib['Deliberation']['titre']); ?>
		</td>
	   <?php
		   if ($delib['Deliberation']['etat_parapheur']==1) {
		       echo  ("<td>En cours de signature</td>");
		   }
		   elseif($delib['Deliberation']['etat_parapheur']==2) {
			   $delib_id = $delib['Deliberation']['id'];
                           if ($delib['Deliberation']['signature'] != '')
			       echo  ("<td><a href='/deliberations/downloadSignature/$delib_id'>Acte sign�</a></td>");
                           else
			       echo  ("<td>Acte sign�</td>");
		    }
                   elseif(($delib['Deliberation']['signee'] == 1) && ($delib['Deliberation']['etat_parapheur']==null)){
                       echo  ("<td>Acte d�clar� sign�</td>");
		    } 
                   elseif (($delib['Deliberation']['etat']>-1) && ($delib['Deliberation']['etat']<2) && ($delib['Deliberation']['nature_id']==1) ) {
                       echo "<td>En cours d'�laboration</td>";
                   }
                   elseif (($delib['Deliberation']['etat']>=1) && ($delib['Deliberation']['etat']<3) && ($delib['Deliberation']['nature_id']==1) ) {
                       echo '<td>A faire voter</td>';
                   }
		    elseif ($delib['Deliberation']['etat_parapheur']== -1) {
 		        echo("<td>Acte refus� � la signature</td>");
		    }
                    else {
 		        echo("<td>A faire signer</td>");
                    }
		  ?>
		</tr>
<?php	 } ?>

	</table>
	<br />
            
        <?php  
            if ($seance_id != null) {
                echo ('Circuit : ');
                echo ($form->input('Deliberation.circuit_id', array('options'=>$circuits, 'label'=>false, 'div'=> false)).'<br /><br />'); 
	        echo ('<div class="submit">');
                echo $form->submit('Envoyer',array('div'=>false));
        	echo ('</div>');
            }
        ?>

<?php echo $form->end(); ?>
</div>
