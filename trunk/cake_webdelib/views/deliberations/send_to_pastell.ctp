<?php echo $javascript->link('utils.js'); ?>
<div class="deliberations">
<?php if (isset($message))  echo ($message); ?>
<h2>Actes envoy&eacute;s &agrave; la signature</h2>
<?php echo $form->create('Deliberation',array('url'=>'/deliberations/sendToPastell/'.$seance_id,'type'=>'file')); ?>
<table width='100%'>
    <tr>
	<th></th>
 	<th>Numéro D&eacute;lib&eacute;ration</th>
 	<th>Libellé de l'acte</th>
 	<th>Titre</th>
 	<th width='65px'>statut</th>
    </tr>

<?php
       $numLigne = 1;
       foreach ($deliberations as $delib) {
          $rowClass = ($numLigne & 1)?array('height' => '36px'):array( 'height' => '36px', 'class'=>'altrow');
       echo $html->tag('tr', null, $rowClass);
       $numLigne++;

		if ((($delib['Deliberation']['pastell_id']==null) && ($delib['Deliberation']['num_pref'] != "") && 
                     (($delib['Deliberation']['etat']>=3) || ($delib['Deliberation']['nature_id']>1 && $delib['Deliberation']['etat']>=2)))) 
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
		   if ($delib['Deliberation']['pastell_id']!=null) {
		       echo  ("<td>Envoyé à Pastell</td>");
		   }
                   elseif (($delib['Deliberation']['etat']>-1) && ($delib['Deliberation']['etat']<2) && ($delib['Deliberation']['nature_id']==1) ) {
                       echo "<td>En cours d'élaboration</td>";
                   }
                   elseif (($delib['Deliberation']['etat']>=1) && ($delib['Deliberation']['etat']<3) && ($delib['Deliberation']['nature_id']==1) ) {
                       echo '<td>A faire voter</td>';
		   }
		   elseif($delib['Deliberation']['num_pref'] == null) {
                       echo '<td><a href="/deliberations/edit/'.$delib['Deliberation']['id'].'">Compléter la classification</a></td>';
                   }
                   else {
 		        echo("<td>A envoyer dans Pastell</td>");
                    }
		  ?>
		</tr>
<?php	 } ?>

	</table>
	<br />
<?php
        echo ('<div class="submit">');
        echo $form->submit('Envoyer',array('div'=>false));
        echo ('</div>');

        echo $form->end(); 
?>
</div>
