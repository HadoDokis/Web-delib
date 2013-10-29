<div class="seances">
<?php 
   $endDiv = false;
   if (@$this->params['filtre'] == 'hide') {
        $endDiv = true;
        echo $this->Html->tag('div', null, array('class'=>'ouvrable', 'id'=>'seanceATraiter'));
        echo $this->Html->tag('h2', "S&eacute;ances &agrave; traiter");
    }
    else {
        echo $this->Html->tag('h2', "S&eacute;ances &agrave; traiter");
        echo $this->Form->create('Seance', array('url'=>'/seances/multiodj/', 'type' => 'file'));
    }
?>
<table width='100%' cellpadding="0" cellspacing="0" border="0">
    <tr>
      <?php    if (!$endDiv)  echo ("<th width='2px'>&nbsp;</th>"); ?>
        <th width='22px'> </th>
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
       echo $this->Html->tag('tr', null, $rowClass); 
       $numLigne++;
?>
<?php     if (!$endDiv) echo("<td>".$this->Form->checkbox('Seance.id_'.$seance['Seance']['id'], array('checked'=> false, 'class' => 'choix_seance_generer'))."</td>"); ?>

                <td>
<?php
                     echo $this->Html->link(SHY,
                                     '/seances/delete/' . $seance['Seance']['id'],
                                     array('class'=>'link_supprimer',
                                           'title'=>'Supprimer la séance du '.$seance['Seance']['date'],
                                           'escape' => false,
                                           'alt'=>'Supprimer la séance du '.$seance['Seance']['date']),
                                           "Voulez-vous supprimer la séance du : ".$seance['Seance']['date']);
?>

                </td>
		<td><b><?php echo $seance['Typeseance']['libelle']; ?></b></td>
		<td><?php 
                        echo $this->Html->link($seance['Seance']['date'], "/seances/edit/".$seance['Seance']['id']); 
                      ?></td>
		<td class="actions" width="110px"> <!-- largeur en fonction des icones -->
<?php
                    echo $this->Html->link(SHY,
                                     '/seances/afficherProjets/' . $seance['Seance']['id'], 
                                     array('class'=>'link_classer_odj', 
					   'title'=>'Voir l\'ordre des projets de la séance du '.$seance['Seance']['date'], 
                                           'escape' => false,
                                           'alt'=>'Voir l\'ordre des projets de la séance du '.$seance['Seance']['date']), 
                                           false);
			$urlConvoc = '/seances/sendConvocations/'.$seance['Seance']['id'].'/'.$seance['Typeseance']['modelconvocation_id'];
			$urlOdj    = '/seances/sendOrdredujour/'.$seance['Seance']['id'].'/'.$seance['Typeseance']['modelordredujour_id'];
			$urlConvocUnique = '/models/generer/null/'.$seance['Seance']['id'].'/'.$seance['Typeseance']['modelconvocation_id']."/$format/0/retour/0/1/1";
			$urlOdjUnique = '/models/generer/null/'.$seance['Seance']['id'].'/'.$seance['Typeseance']['modelordredujour_id']."/$format/0/retour/0/1/1";
			if (Configure::read('AFFICHE_CONVOCS_ANONYME'))
				echo $this->Html->link(SHY, $urlConvocUnique, array(
					'class'=>'link_convocation_unique',
					'title'=>"Apercu d'une convocation pour la séance du ".$seance['Seance']['date'],
                                        'escape' => false,
					'alt'=>"Apercu d'une convocation pour la séance du ".$seance['Seance']['date'],
//					'onClick'=>"return avantGeneration('Etes-vous sur de vouloir lancer la génération de l\'apercu ?');"
                                    ), false);
		    echo $this->Html->link(SHY, $urlConvoc, array(
				'class'=>'link_convocation',
				'title'=>'Générer la liste des convocations pour la séance du '.$seance['Seance']['date'],
                                'escape' => false,
				'alt'=>'Générer la liste des convocations pour la séance du '.$seance['Seance']['date'],
//				'onClick'=>"return avantGeneration('Etes-vous sur de vouloir lancer la génération des documents ?');"
                        ), false);
			if (Configure::read('AFFICHE_CONVOCS_ANONYME'))
				echo $this->Html->link(SHY, $urlOdjUnique, array(
					'class'=>'link_ordre_jour_unique',
                                        'escape' => false,
					'title'=>"Apercu de l'ordre jour pour la séance du ".$seance['Seance']['date'],
					'alt'=>"Apercu de l'ordre jour pour la séance du ".$seance['Seance']['date'],
//					'onClick'=>"return avantGeneration('Etes-vous sur de vouloir lancer la génération de l\'apercu ?');"
                                    ), false);
			echo $this->Html->link(SHY, $urlOdj, array(
					'class'=>'link_ordre_jour',
					'title'=>'Générer l\'ordre du jour détaillé pour la séance du '.$seance['Seance']['date'],
                                        'escape' => false,
					'alt'=>'Générer l\'ordre du jour détaillé pour la séance du '.$seance['Seance']['date'],
//					'onClick'=>"return avantGeneration('Etes-vous sur de vouloir lancer la génération des documents ?');"
                            ), false);

                   echo $this->Html->link(SHY, '/seances/sendToIdelibre/'.$seance['Seance']['id'], array(
                                        'class'=>'link_tablet',
                                        'title'=>'Envoyer à Idelibre la séance du '.$seance['Seance']['date'],
                                        'escape' => false,
                                        'alt'=>'Envoyer à Idelibre la séance du '.$seance['Seance']['date'],
//                                        'onClick'=>"return avantGeneration('Etes-vous sur de vouloir envoyer les documents ?');"
                       ), false);

?>
		</td>
		<td class="actions">
			<?php echo $this->Html->link(SHY,
					       '/seances/saisirSecretaire/' . $seance['Seance']['id'], 
                                               array('class'=>'link_secretaire', 
						     'title'=>'Choix du secrétaire de séance du '.$seance['Seance']['date'],
                                                     'escape' => false,
                                                     'alt'=>'Choix du secrétaire de séance du '.$seance['Seance']['date']), 
						     false); ?>

			<?php echo $this->Html->link(SHY,
					      '/seances/saisirDebatGlobal/'.$seance['Seance']['id'], 
                                              array('class'=>'link_debat', 
						    'title'=>'Saisir les débats généraux de la séance du '.$seance['Seance']['date'],
                                                    'escape' => false,
                                                    'alt'=>'Saisir les débats généraux de la séance du '.$seance['Seance']['date']), 
                                                    false); ?>

			<?php
                             if ($seance['Typeseance']['action']==0)
                                 echo $this->Html->link(SHY,
                                                  '/seances/details/' . $seance['Seance']['id'],
                                                  array('class'=>'link_voter', 
						  'alt'=>'Afficher les projets et voter pour la séance du '.$seance['Seance']['date'], 
                                                  'escape' => false,
                                                  'title'=>'Afficher les projets et voter pour la séance du '.$seance['Seance']['date']), 
						  false);

				elseif ($seance['Typeseance']['action']==1)
                                    echo $this->Html->link(SHY,
                                                    '/seances/detailsAvis/'.$seance['Seance']['id'],
                                                    array('class'=>'link_donnerAvis', 
                                                          'title'=>'Afficher les projets et donner un avis pour la séance du '.$seance['Seance']['date'],	
                                                          'escape' => false,
                                                          'alt'=>'Afficher les projets et donner un avis pour la séance du '.$seance['Seance']['date']), 
                                                     false);
				elseif ($seance['Typeseance']['action']==2)
					echo $this->Html->link(SHY,'/seances/details/' . $seance['Seance']['id'],array('class'=>'link_actes', 'escape' => false, 'title'=>'Afficher les projets pour la séance du '.$seance['Seance']['date'], 'alt'=>'Afficher les projets pour la séance du '.$seance['Seance']['date']),false);

			echo $this->Html->link(SHY,'/seances/saisirCommentaire/' . $seance['Seance']['id'], array('class'=>'link_commentaire_seance', 'title'=>'Saisir un commentaire pour la séance du '.$seance['Seance']['date'], 'escape' => false, 'alt'=>'Saisir un commentaire pour la séance du '.$seance['Seance']['date']),  false);
                      echo ('</td>');
                      echo ('<td class="actions">');
		      if ($canSign) {
                          if (!$use_pastell) {
	                      echo $this->Html->link(SHY,'/deliberations/sendToParapheur/' . $seance['Seance']['id'].'/', 
                                              array('class'=>'link_signer', 
				                    'title'=>'Envoi des actes à la signature pour la séance du '.$seance['Seance']['date'],
                                                     'escape' => false,
                                                    'alt'=>'Envoi des actes à la signature pour la séance du '.$seance['Seance']['date'] 
						    ));
                          }
			  else {
                              echo $this->Html->link(SHY,'/deliberations/sendToPastell/' . $seance['Seance']['id'].'/',
                                               array('class'=>'link_signer',
                                                     'title'=>'Envoi des actes à Pastell pour la séance du '.$seance['Seance']['date'],
                                                     'alt'=>'Envoi des actes à Pastell pour la séance du '.$seance['Seance']['date'],
                                                      'escape' => false,
                                                     ));
                          }
                      }

			echo $this->Html->link(SHY,'/models/generer/null/' . $seance['Seance']['id'].'/'.$seance['Typeseance']['modelpvsommaire_id']."/$format/1/retour/1/1/1", array(
				'class'=>'link_pvsommaire',
				'title'=>'Génération du pv sommaire pour la séance du '.$seance['Seance']['date'],
				'alt'=>'Génération du pv sommaire pour la séance du '.$seance['Seance']['date'],
                                'escape' => false,
//				'onClick'=>"return avantGeneration('Etes-vous sur de vouloir lancer la génération des documents ?');"
                            ),  false);
			echo $this->Html->link(SHY,'/models/generer/null/' . $seance['Seance']['id'].'/'.$seance['Typeseance']['modelpvdetaille_id']."/$format/1/retour/1/1/1", array(
				'class'=>'link_pvcomplet',
                                'escape' => false,
				'title'=>'Génération du pv complet pour la séance du '.$seance['Seance']['date'],
				'alt'=>'Génération du pv complet pour la séance du '.$seance['Seance']['date'],
//				'onClick'=>"return avantGeneration('Etes-vous sur de vouloir lancer la génération des documents ?');"
                            ), false);

		      echo $this->Html->link(SHY,
                                       '/seances/clore/'.$seance['Seance']['id'],  
                                       array('class'=>'link_clore_seance', 
                                             'title'=>'Clôture de la séance du '.$seance['Seance']['date'], 
                                             'escape' => false,
                                             'alt'=>'Clôture de la séance du '.$seance['Seance']['date']),  
                                       'Etes-vous sur de vouloir clôturer la séance ?', false);
			?>
		</td>
	</tr>
	<?php endforeach; ?>

</table>
<div class='spacer'> </div>
<?php 
    if (!$endDiv)  {
        if(isset($models) && !empty($models)){
        echo $this->Form->input('Seance.model_id', array('options' => $models, 'label' => 'Modèle')); 
        echo $this->Form->button('<i class="icon-cogs"></i> Générer', array('type'=>'submit', 'class'=>'btn btn-primary', 'title' => "Lancer la génération multi-séances (Attention : l'exécution peut être longue)", 'id' => 'generer_multi_seance'));
        }
    }    
?>
</div>
<script type="text/javascript">

function overlayResize() {
	var overlayEle = $('#overlay');
	if (overlayEle.length > 0) {
		ovPosition = $('#container').offset();
		ovHeight = $('#container').outerHeight();
		ovWidth = $('#container').outerWidth();
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
		overlayOn();
		return true;
	} else
		return false;
}

$( document ).ready(function(){
    $('input.choix_seance_generer').change(function(){
        if (!$('input.choix_seance_generer').prop('checked')){
            $('#generer_multi_seance').addClass('disabled');
            $('#generer_multi_seance').click(function(){
                return false;
            })
        }else{
            $('#generer_multi_seance').removeClass('disabled');
            $('#generer_multi_seance').click(function(){
                $('form#SeanceListerFuturesSeancesForm').submit();
            })
        }
    });
    $('input.choix_seance_generer').change();
});

</script>
<?php
     if ($endDiv)
         echo ('</div>');
?>
