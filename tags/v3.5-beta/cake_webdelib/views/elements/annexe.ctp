<div class="annexesGauche">
<?php 
     if ($typeAnnexes=='G'){
	    $libelle = 'Pi�ces jointes au contr�le de l�galit�';
	    $info = 'Attention, uniquement PDF, JPG ET PNG';
	 }
	 else{
	    $libelle = 'Information compl�mentaire';
            $info = 'Ces donn�es ne peuvent r�-utilis� dans la g�n�ration des documents';
	 }
	// echo $form->label('Annexes'.$typeAnnexes.'.titre', $libelle, $info); 
	 echo ("<label for='Annexes'.$typeAnnexes.'.titre', title='$info'>$libelle</label>");
?>


</div>
<div class="annexesDroite">
	<?php if(!empty($this->data['Annex'])){
		foreach ($this->data['Annex'] as $annexe) {
			if ($annexe['type'] == $typeAnnexes) {
				$divId = 'afficheAnnexe'.$annexe['id'];
				echo '<div id="'.$divId.'">';
					echo '[ '.$html->link($annexe['filename'] ,'/annexes/download/'.$annexe['id']).' ]&nbsp;&nbsp;';
					echo $html->link('Supprimer', "javascript:supprimerAnnexe(".$annexe['id'].")", null, 'Voulez-vous vraiment supprimer cette annexe ?');
				echo '</div>';
			}
		};
	}; ?>
	<div id="inputAnnexes<?php echo $typeAnnexes;?>"></div>
	<br/>
	<a href="javascript:ajouterAnnexe('inputAnnexes<?php echo $typeAnnexes;?>', 'Annexes<?php echo $typeAnnexes;?>')" class="link_annexe">Joindre une annexe</a>
</div>
<div class='spacer'></div>
