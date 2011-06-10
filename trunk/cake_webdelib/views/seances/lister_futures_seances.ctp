<?php
    if (Configure::read('CONVOCS_MODIFIABLES')) {
        $urlPage =  FULL_BASE_URL . $this->webroot;
        echo ("<br />Choix du format de sortie des &eacute;ditions : ");
        $format = $session->read('user.format.sortie');
        echo $form->select('User.Sortie', array (0=>'pdf', 1=>'odt') , $format, array('id' => "$urlPage", 'onChange'=>'changeFormat(this)'),null,false);
    }
?>
<br /><br />
<div class="seances">
<h2>S&eacute;ances &agrave; traiter</h2>


<table width='100%' cellpadding="0" cellspacing="0" border="0">
	<tr>
		<th width='150px'>Type</th>
		<th width='190px'>Date S&eacute;ance</th>
		<th width='20%'>Préparation'</th>
		<th width='20%'>En cours</th>
		<th width='20%'>Finition</th>
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
		 	<?php echo $html->link(SHY,'/seances/afficherProjets/' . $seance['Seance']['id'], array('class'=>'link_classer_odj', 'title'=>'Voir l\'ordre des projets', 'alt'=>'odj'), false, false)?>
		<?php
                       $urlConvoc = '/models/generer/null/'.$seance['Seance']['id'].'/'.$seance['Typeseance']['modelconvocation_id'].'/null/';
		       $urlOdj = '/models/generer/null/'.$seance['Seance']['id'].'/'.$seance['Typeseance']['modelordredujour_id'].'/null/';
                       $urlConvocUnique = '/models/generer/null/'.$seance['Seance']['id'].'/'.$seance['Typeseance']['modelconvocation_id'].'/null/0/retour/0/true';
		       $urlOdjUnique = '/models/generer/null/'.$seance['Seance']['id'].'/'.$seance['Typeseance']['modelordredujour_id'].'/null/0/retour/0/true';
                    
                     if (Configure::read('AFFICHE_CONVOCS_ANONYME'))
		        echo $html->link(SHY, $urlConvocUnique, array('class'=>'link_convocation_unique', 'title'=>"Apercu d'une convocation"), 'Etes-vous sur de vouloir lancer la génération de l\'apercu  ?', false);

		    echo $html->link(SHY, $urlConvoc, array('class'=>'link_convocation', 'title'=>'Générer la liste des convocations'), 'Etes-vous sur de vouloir lancer la génération des documents ?', false);
                    if (Configure::read('AFFICHE_CONVOCS_ANONYME'))
		        echo $html->link(SHY, $urlOdjUnique, array('class'=>'link_ordre_jour_unique', 'title'=>"Apercu de l'ordre jour"),  'Etes-vous sur de vouloir lancer la génération de l\'apercu ?', false);
		    echo $html->link(SHY, $urlOdj, array('class'=>'link_ordre_jour', 'title'=>'Générer l\'ordre du jour détaillé'),  'Etes-vous sur de vouloir lancer la génération des documents  ?', false);
		?>
		</td>
		<td class="actions">
			<?php echo $html->link(SHY,'/seances/saisirSecretaire/' . $seance['Seance']['id'], array('class'=>'link_secretaire', 'title'=>'Choix du secrétaire de séance'), false, false); ?>
			<?php echo $html->link(SHY,'/seances/saisirDebatGlobal/' . $seance['Seance']['id'], array('class'=>'link_debat', 'title'=>'Saisir les débats généraux de la séance'), false, false); ?>
			<?php
				if ($seance['Typeseance']['action']==0)
					echo $html->link(SHY,'/seances/details/' . $seance['Seance']['id'],array('class'=>'link_voter', 'title'=>'Afficher les projets et voter'), false, false);
				elseif ($seance['Typeseance']['action']==1)
					echo $html->link(SHY,'/seances/detailsAvis/' . $seance['Seance']['id'],array('class'=>'link_donnerAvis', 'title'=>'Afficher les projets et donner un avis'), false, false);
				elseif ($seance['Typeseance']['action']==2)
					echo $html->link(SHY,'/seances/details/' . $seance['Seance']['id'],array('class'=>'link_actes', 'title'=>'Afficher les projets'), false, false);

			echo $html->link(SHY,'/seances/saisirCommentaire/' . $seance['Seance']['id'], array('class'=>'link_commentaire_seance', 'title'=>'Saisir un commentaire pour la séance'), false, false);
                      echo ('</td>');
                      echo ('<td class="actions">');
                      if ($canSign) 
	                  echo $html->link(SHY,'/deliberations/sendToParapheur/' . $seance['Seance']['id'].'/', 
                                           array('class'=>'link_signer', 
                                                 'title'=>'Envoi au parapheur électronique'), null, false);

	               echo $html->link(SHY,'/models/generer/null/' . $seance['Seance']['id'].'/'.$seance['Typeseance']['modelpvsommaire_id'].'/null/0/retour/1/', array('class'=>'link_pvsommaire', 'title'=>'Generation du pv sommaire'),  'Etes-vous sur de vouloir lancer la génération des documents ?', false);
                      echo $html->link(SHY,'/models/generer/null/' . $seance['Seance']['id'].'/'.$seance['Typeseance']['modelpvdetaille_id'].'/null/0/retour/1/', array('class'=>'link_pvcomplet', 'title'=>'Generation du pv complet'),  'Etes-vous sur de vouloir lancer la génération des documents ?', false);

                      echo $html->link(SHY,'/seances/clore/' . $seance['Seance']['id'],  array('class'=>'link_clore_seance', 'title'=>'Clôture de la séance'),  'Etes-vous sur de vouloir clôturer la séance ?', false);

			?>
		</td>
	</tr>
	<?php endforeach; ?>

</table>

</div>
