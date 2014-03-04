<div class="seances">
<h2>Séances traitées</h2>


<table style="border:0; width:'100%';">
	<tr>
		<th>Type</th>
		<th>Date Séance</th>
		<th>Action</th>
	</tr>
<?php
       $numLigne = 1;
       foreach ($seances as $seance):
           $rowClass = ($numLigne & 1)?array('height' => '36px'):array( 'height' => '36px', 'class'=>'altrow');
       echo $this->Html->tag('tr', null, $rowClass);
       $numLigne++;
?>
		<td><?php echo $seance['Typeseance']['libelle']; ?></td>
		<td><?php echo $seance['Seance']['date']; ?></td>
		<td class="actions">
			<?php echo $this->Html->link(SHY,'/seances/saisirDebatGlobal/' . $seance['Seance']['id'], array('class'=>'link_debat', 'escape' => false, 'title'=>'Saisir les débats généraux de la séance'), false); ?>
		</td>
	</tr>
	<?php endforeach; ?>

</table>

</div>
