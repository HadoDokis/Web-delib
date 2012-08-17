<div class="seances">
<?php 
   $endDiv = false;
   if (@$this->params['filtre'] == 'hide') {
        $endDiv = true;
        echo $html->tag('div', null, array('class'=>'ouvrable', 'id'=>'seanceATraiter'));
        echo $html->tag('h2', "S&eacute;ances &agrave; traiter");
    }
    else 
        echo $html->tag('h2', "S&eacute;ances &agrave; traiter");
?>
<table width='100%' cellpadding="0" cellspacing="0" border="0">
    <tr>
        <th width='150px'>Type</th>
	<th width='190px'>Date S&eacute;ance</th>
	<th width='20%'>Pr&eacute;paration</th>
        <th width='20%'>En cours</th>
        <th width='20%'>Finalisation</th>
    </tr>
<?php 
       $numLigne = 1;
       foreach ($seances as $seance): 
          $rowClass = ($numLigne & 1)?array('height' => '36px'):array( 'height' => '36px', 'class'=>'altrow');
       echo $html->tag('tr', null, $rowClass); 
       $numLigne++;
?>

		<td><b><?php echo $seance['Typeseance']['libelle']; ?></b></td>
		<td><?php echo ($html->link($seance['Seance']['date'], "/seances/edit/".$seance['Seance']['id'])); ?></td>
		<td class="actions" width="110px"> <!-- largeur en fonction des icones -->
<?php
                     echo $html->link(SHY,
                                     '/seances/delete/' . $seance['Seance']['id'],
                                     array('class'=>'link_supprimer',
                                           'title'=>'Supprimer la séance du '.$seance['Seance']['date'],
                                           'alt'=>'Supprimer la séance du '.$seance['Seance']['date']),
                                           false,
                                           false);
                    echo $html->link(SHY,
                                     '/seances/afficherProjets/' . $seance['Seance']['id'], 
                                     array('class'=>'link_classer_odj', 
					   'title'=>'Voir l\'ordre des projets de la séance du '.$seance['Seance']['date'], 
                                           'alt'=>'Voir l\'ordre des projets de la séance du '.$seance['Seance']['date']), 
                                           false, 
                                           false);
			$urlConvoc = '/models/generer/null/'.$seance['Seance']['id'].'/'.$seance['Typeseance']['modelconvocation_id']."/$format/";
			$urlOdj = '/models/generer/null/'.$seance['Seance']['id'].'/'.$seance['Typeseance']['modelordredujour_id']."/$format/";
			$urlConvocUnique = '/models/generer/null/'.$seance['Seance']['id'].'/'.$seance['Typeseance']['modelconvocation_id']."/$format/0/retour/0/true";
			$urlOdjUnique = '/models/generer/null/'.$seance['Seance']['id'].'/'.$seance['Typeseance']['modelordredujour_id']."/$format/0/retour/0/true";
			if (Configure::read('AFFICHE_CONVOCS_ANONYME'))
				echo $html->link(SHY, $urlConvocUnique, array(
					'class'=>'link_convocation_unique',
					'title'=>"Apercu d'une convocation pour la séance du ".$seance['Seance']['date'],
					'alt'=>"Apercu d'une convocation pour la séance du ".$seance['Seance']['date'],
					'onClick'=>'return avantGeneration("Etes-vous sur de vouloir lancer la génération de l\'apercu ?");'), false, false);
		    echo $html->link(SHY, $urlConvoc, array(
				'class'=>'link_convocation',
				'title'=>'Générer la liste des convocations pour la séance du '.$seance['Seance']['date'],
				'alt'=>'Générer la liste des convocations pour la séance du '.$seance['Seance']['date'],
				'onClick'=>'return avantGeneration("Etes-vous sur de vouloir lancer la génération des documents ?");'), false, false);
			if (Configure::read('AFFICHE_CONVOCS_ANONYME'))
				echo $html->link(SHY, $urlOdjUnique, array(
					'class'=>'link_ordre_jour_unique',
					'title'=>"Apercu de l'ordre jour pour la séance du ".$seance['Seance']['date'],
					'alt'=>"Apercu de l'ordre jour pour la séance du ".$seance['Seance']['date'],
					'onClick'=>'return avantGeneration("Etes-vous sur de vouloir lancer la génération de l\'apercu ?");'), false, false);
			echo $html->link(SHY, $urlOdj, array(
					'class'=>'link_ordre_jour',
					'title'=>'Générer l\'ordre du jour détaillé pour la séance du '.$seance['Seance']['date'],
					'alt'=>'Générer l\'ordre du jour détaillé pour la séance du '.$seance['Seance']['date'],
					'onClick'=>'return avantGeneration("Etes-vous sur de vouloir lancer la génération des documents ?");'), false, false);
?>
		</td>
		<td class="actions">
			<?php echo $html->link(SHY,
					       '/seances/saisirSecretaire/' . $seance['Seance']['id'], 
                                               array('class'=>'link_secretaire', 
						     'title'=>'Choix du secrétaire de séance du '.$seance['Seance']['date'],
                                                     'alt'=>'Choix du secrétaire de séance du '.$seance['Seance']['date']), 
                                                     false, 
						     false); ?>

			<?php echo $html->link(SHY,
					      '/seances/saisirDebatGlobal/'.$seance['Seance']['id'], 
                                              array('class'=>'link_debat', 
						    'title'=>'Saisir les débats généraux de la séance du '.$seance['Seance']['date'],
                                                    'alt'=>'Saisir les débats généraux de la séance du '.$seance['Seance']['date']), 
						    false, 
                                                    false); ?>

			<?php
                             if ($seance['Typeseance']['action']==0)
                                 echo $html->link(SHY,
                                                  '/seances/details/' . $seance['Seance']['id'],
                                                  array('class'=>'link_voter', 
						  'alt'=>'Afficher les projets et voter pour la séance du '.$seance['Seance']['date'], 
                                                  'title'=>'Afficher les projets et voter pour la séance du '.$seance['Seance']['date']), 
                                                  false, 
						  false);

				elseif ($seance['Typeseance']['action']==1)
                                    echo $html->link(SHY,
                                                    '/seances/detailsAvis/'.$seance['Seance']['id'],
                                                    array('class'=>'link_donnerAvis', 
                                                    'title'=>'Afficher les projets et donner un avis pour la séance du '.$seance['Seance']['date'],	
                                                    'alt'=>'Afficher les projets et donner un avis pour la séance du '.$seance['Seance']['date']), false, false);
				elseif ($seance['Typeseance']['action']==2)
					echo $html->link(SHY,'/seances/details/' . $seance['Seance']['id'],array('class'=>'link_actes', 'title'=>'Afficher les projets pour la séance du '.$seance['Seance']['date'], 'alt'=>'Afficher les projets pour la séance du '.$seance['Seance']['date']), false, false);

			echo $html->link(SHY,'/seances/saisirCommentaire/' . $seance['Seance']['id'], array('class'=>'link_commentaire_seance', 'title'=>'Saisir un commentaire pour la séance du '.$seance['Seance']['date'], 'alt'=>'Saisir un commentaire pour la séance du '.$seance['Seance']['date']), false, false);
                      echo ('</td>');
                      echo ('<td class="actions">');
		      if ($canSign) {
                          if (!$use_pastell) {
	                      echo $html->link(SHY,'/deliberations/sendToParapheur/' . $seance['Seance']['id'].'/', 
                                              array('class'=>'link_signer', 
				                    'title'=>'Envoi des actes à la signature pour la séance du '.$seance['Seance']['date'],
                                                    'alt'=>'Envoi des actes à la signature pour la séance du '.$seance['Seance']['date'] 
						    ), null, false);
                          }
			  else {
                              echo $html->link(SHY,'/deliberations/sendToPastell/' . $seance['Seance']['id'].'/',
                                               array('class'=>'link_signer',
                                                     'title'=>'Envoi des actes à Pastell pour la séance du '.$seance['Seance']['date'],
                                                     'alt'=>'Envoi des actes à Pastell pour la séance du '.$seance['Seance']['date']
                                                     ), null, false);
                          }
                      }

			echo $html->link(SHY,'/models/generer/null/' . $seance['Seance']['id'].'/'.$seance['Typeseance']['modelpvsommaire_id']."/$format/1/retour/1/true", array(
				'class'=>'link_pvsommaire',
				'title'=>'Génération du pv sommaire pour la séance du '.$seance['Seance']['date'],
				'alt'=>'Génération du pv sommaire pour la séance du '.$seance['Seance']['date'],
				'onClick'=>'return avantGeneration("Etes-vous sur de vouloir lancer la génération des documents ?");'),  false, false);
			echo $html->link(SHY,'/models/generer/null/' . $seance['Seance']['id'].'/'.$seance['Typeseance']['modelpvdetaille_id']."/$format/1/retour/1/true", array(
				'class'=>'link_pvcomplet',
				'title'=>'Génération du pv complet pour la séance du '.$seance['Seance']['date'],
				'alt'=>'Génération du pv complet pour la séance du '.$seance['Seance']['date'],
				'onClick'=>'return avantGeneration("Etes-vous sur de vouloir lancer la génération des documents ?");'), false, false);

		      echo $html->link(SHY,
                                       '/seances/clore/'.$seance['Seance']['id'],  
                                       array('class'=>'link_clore_seance', 
                                             'title'=>'Clôture de la séance du '.$seance['Seance']['date'], 
                                             'alt'=>'Clôture de la séance du '.$seance['Seance']['date']),  
                                       'Etes-vous sur de vouloir clôturer la séance ?', false);


			?>
		</td>
	</tr>
	<?php endforeach; ?>

</table>

</div>
<script type="text/javascript">

function overlayResize() {
	var overlayEle = $('#overlay');
	if (overlayEle.length > 0) {
		ovPosition = $('#centre').offset();
		ovHeight = $('#centre').outerHeight();
		ovWidth = $('#centre').outerWidth();
		overlayEle
			.css('left', ovPosition.left)
			.css('top', ovPosition.top)
			.width(ovWidth)
			.height(ovHeight);
	}
}
function overlayOn() {
	$('<div></div>').appendTo(document.body).attr('id', 'overlay');
	overlayResize();
}
function avantGeneration(message) {
	if (confirm(message)) {
		$('<div></div>').appendTo(document.body).attr('id', 'overlay');
		overlayResize();
		return true;
	} else
		return false
}

</script>
<?php
     if ($endDiv)
         echo ('</div>');
?>
