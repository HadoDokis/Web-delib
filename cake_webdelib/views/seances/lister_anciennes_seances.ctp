<div class="seances">
<h2>S&eacute;ances trait&eacute;es</h2>


<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
		<th>Type</th>
		<th>Date S&eacute;ance</th>
		<th>Action</th>
	</tr>
<?php
       $numLigne = 1;
       foreach ($seances as $seance):
           $rowClass = ($numLigne & 1)?array('height' => '36px'):array( 'height' => '36px', 'class'=>'altrow');
       echo $html->tag('tr', null, $rowClass);
       $numLigne++;
?>
		<td><?php echo $seance['Typeseance']['libelle']; ?></td>
		<td><?php echo $seance['Seance']['date']; ?></td>
		<td class="actions">
			<?php echo $html->link(SHY,'/seances/saisirDebatGlobal/' . $seance['Seance']['id'], array('class'=>'link_debat', 'title'=>'Saisir les débats généraux de la séance'), false, false); ?>
		</td>
	</tr>
	<?php endforeach; ?>

</table>

</div>
