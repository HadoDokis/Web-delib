<?php echo $this->Html->script('calendrier.js'); ?>
<?php echo $this->Html->script('utils.js'); ?>
<?php echo $this->Html->script('deliberation.js'); ?>
<?php echo $this->Html->script('ckeditor/ckeditor'); ?>
<?php echo $this->Html->script('ckeditor/adapters/jquery'); ?>
<?php echo $this->Html->script('multidelib.js'); ?>

<?php
echo "<h1>Ajout d'un projet</h1>";
echo $this->Form->create('Deliberation', array('url'=>'/deliberations/add','type'=>'file', 'name'=>'Deliberation'));
?>
<div class='onglet'>
       <a href="#" id="emptylink" alt=""></a>
 <?php      echo $this->HTML->link('Informations principales', '#',array('class'=>'ongletCourant','id'=>'lienTab1','onClick' => 'javascript:afficheOngletNew(document.Deliberation,1);'));
            echo $this->HTML->link('Textes', '#',array('id'=>'lienTab2','onClick' => 'javascript:afficheOngletNew(document.Deliberation,2);'));
            echo $this->HTML->link('Annexe(s)', '#',array('id'=>'lienTab3','onClick' => 'javascript:afficheOngletNew(document.Deliberation,3);'));
            if (!empty($infosupdefs))
            echo $this->HTML->link('Informations supplémentaires', '#',array('id'=>'lienTab4','onClick' => 'javascript:afficheOngletNew(document.Deliberation,4);'));
            if (Configure::read('DELIBERATIONS_MULTIPLES'))
            echo $this->HTML->link('Délibérations rattachées', '#',array('id'=>'lienTab5','onClick' => 'javascript:afficheOngletNew(document.Deliberation,5);','style'=>'display: none'));
            echo $this->Html->useTag('tagend', 'div'); 
?>



<div id="tab1">
        <fieldset id='info'>
	<div class='demi'>
		<?php echo '<b><u>Rédacteur</u></b> : <i>'.$this->Html->value('Redacteur.prenom').' '.$this->Html->value('Redacteur.nom').'</i>';?>
		<br/>
		<?php echo '<b><u>Service émetteur</u></b> : <i>'.$this->Html->value('Service.libelle').'</i>'; ?>
	</div>
	<div class='demi'>
	</div>
        </fieldset>
	<div class='spacer'></div>
        <?php echo $this->Form->input('Deliberation.typeacte_id', array('label'    => 'Type d\'acte <abbr title="obligatoire">(*)</abbr>',
                                                                        'options'  => $this->Session->read('user.Nature'), 
                                                                        'empty'    => '(sélectionner le type d\'acte)', 
                                                                        'id'       => 'listeTypeactesId',
                                                                        'onChange' => "updateTypeseances(this);", 
                                                                        'escape'   => false));  ?>
	<div class='spacer'></div>
 	<?php echo $this->Form->input('Deliberation.objet', array('type'=>'textarea','label'=>'Libellé <abbr title="obligatoire">(*)</abbr>','cols' => '60','rows'=> '2'));?>

	<div class='spacer'></div>
 	<?php echo $this->Form->input('Deliberation.titre', array('type'=>'textarea','label'=>'Titre','cols' => '60','rows'=> '2'));?>

	<div class='spacer'></div>

        <div id='selectTypeseances' class='gauche'>
        <?php
          if (!empty( $typeseances))
             echo $this->Form->input('Typeseance', array('options'  => $typeseances,
                                                      'label'    => 'Types de séance',
                                                      'size'     => 10,
                                                      'onchange' => "updateDatesSeances(this);",
                                                      'multiple' => true));
        ?>
        </div>
        <div id='selectDatesSeances' class='droite'>
        <?php
          if (!empty($seances))
                echo $this->Form->input('Seance', array( 'options'  => $seances,
                                                         'label'    => 'Dates de séance',
                                                         'size'     => 10,
                                                         'multiple' => true));
        ?>  
        </div>

	<div class='spacer'></div>
	<?php echo $this->Form->input('Deliberation.rapporteur_id', array('label'=>'Rapporteur', 'options'=>$rapporteurs, 'empty'=>true)); ?>

	<div class='spacer'></div>
	<?php echo $this->Form->input('Deliberation.theme_id', array('label'=>'Thème <abbr title="obligatoire">(*)</abbr>', 'empty'=>'(sélectionner le thème)', 'escape'=>false)); ?>
	<div class='spacer'></div>

	<?php 
        if ($USE_PASTELL)
                echo $this->Form->input('Deliberation.num_pref_libelle', array('label'=>'Nomenclature', 'options'=>$nomenclatures, 'default'=>$this->Html->value('Deliberation.num_pref'), 'disabled' =>  empty($nomenclatures), 'empty' => "Aucune", 'escape'=>false)); 
        else {
                echo $this->Form->input( 'Deliberation.num_pref_libelle',
				   array('div'      => false,
                                         'label'    => 'Num Pref',
                                         'id'       => 'classif1', 
                                         'size'     => '60',
					 'readonly' => 'readonly'));
        ?>

                <a class="list_form" href="#add" onclick="javascript:window.open('<?php echo $this->base; ?>/deliberations/classification', 'Select_attribut', 'scrollbars=yes,width=570,height=450');" id="classification_text">[Choisir la classification]</a>
        <?php 
               echo $this->Form->hidden('Deliberation.num_pref',array('id'=>'num_pref'));
        }
        ?>
	<div class='spacer'></div>

	<?php echo $this->Form->label('Deliberation.date_limite', 'Date limite');?>
	<?php
		if (!empty($date_limite) && $date_limite != '01/01/1970')
			$value = "value='".$date_limite."'";
		else
			$value = "value=''";
	?>
	<input name="date_limite" size="9" id="DeliberationDateLimite" <?php echo $value; ?> />&nbsp;<a href="javascript:show_calendar('Deliberation.date_limite','f');" title="Sélectionner une date à l'aide du calendrier" id="afficheCalendrier"><?php echo $this->Html->image("calendar.png", array('alt' => "afficher le calendrier")); ?></a>
	<div class='spacer'></div>


<?php 
        if ($DELIBERATIONS_MULTIPLES) {
           echo $this->Form->input('Deliberation.is_multidelib', array(
	                     'type'=>'checkbox',
                              'autocomplete'=>'off',
		             'label'=>'Multi Délibération',
                               'onClick'=>'multiDelib(this)'));
        }
?>

	<div class='spacer'></div>
</div>
       
<?php if (!empty($infosupdefs)): ?>
<div id="tab4" <?php echo isset($lienTab) && $lienTab==4 ? '' : 'style="display: none;"' ?>>
	<?php
	foreach($infosupdefs as $infosupdef) {
		// Amélioration 4.1 : on ne peut modifier une infosup qu'en fonction du profil
		$disabled = true;
		foreach ($infosupdef['Profil'] as $profil) 
			if ($profil['id'] == $profil_id) 
                            $disabled = false;
                        
                if ($infosupdef['Infosupdef']['type'] == 'file' && $disabled) continue;
                
		$fieldName = 'Infosup.'.$infosupdef['Infosupdef']['code'];
		$fieldId = 'Infosup'.Inflector::camelize($infosupdef['Infosupdef']['code']);
		echo "<div class='required'>";
			echo $this->Form->label($fieldName, $infosupdef['Infosupdef']['nom'], array('name'=>'label'.$infosupdef['Infosupdef']['code']));
			if ($infosupdef['Infosupdef']['type'] == 'text') {
				echo $this->Form->input($fieldName, array('label' => false, 'type'=> 'textarea', 'title'=>$infosupdef['Infosupdef']['commentaire'], 'readonly'=> $disabled  ));
			} elseif ($infosupdef['Infosupdef']['type'] == 'boolean') {
                if (!$disabled)
				    echo $this->Form->input($fieldName, array('label' => false, 'type'=>'checkbox', 'title'=>$infosupdef['Infosupdef']['commentaire'], 'div'=>array('class'=>'input')));
                else {
                    echo $this->Form->input($fieldName, array('label' => false, 'type'=>'checkbox', 'title'=>$infosupdef['Infosupdef']['commentaire'], 'disabled'=>$disabled, 'div'=>array('class'=>'input')));
                    echo $this->Form->input($fieldName, array('type'=>'hidden', 'id'=>false));
                }
            } elseif ($infosupdef['Infosupdef']['type'] == 'date') {
                $fieldSelector = preg_replace("#[^a-zA-Z]#", "", $fieldId);
				echo $this->Form->input($fieldName, array('type'=>'text',  'readonly'=> $disabled,  'div' => false, 'label' => false, 'size'=>'9', 'id'=>$fieldSelector, 'title'=>$infosupdef['Infosupdef']['commentaire']));
				echo '&nbsp;';
				if (!$disabled)
					echo $this->Html->link($this->Html->image("calendar.png", array('style'=>"border='0'")), "javascript:show_calendar('Deliberation.$fieldSelector', 'f');", array('escape' =>false), false);
				else
					echo($this->Html->image("calendar.png", array('style'=>"border='0'")));
			} elseif ($infosupdef['Infosupdef']['type'] == 'richText') {
                                    echo '<div class="annexesGauche"></div>';
                                if (!$disabled){
                                    echo '<div class="fckEditorProjet">';
                                            echo $this->Form->input($fieldName, array('label'=>false, 'type'=>'textarea'));
                                            echo $this->Fck->load($fieldId);
                                    echo '</div>';
                                    echo '<div class="spacer"></div>';
                                }
                                else{
                                    echo $this->Form->input($fieldName, array('label'=>false, 'type' => 'textarea', 'readonly' => true));
                                }
			} elseif ($infosupdef['Infosupdef']['type'] == 'file') {
				if (empty($this->data['Infosup'][$infosupdef['Infosupdef']['code']]))
					echo  $this->Form->input($fieldName, array('label'=>false, 'type'=>'file', 'size'=>'60', 'title'=>$infosupdef['Infosupdef']['commentaire'],  'readonly'=> $disabled));
				else {
					echo '<span id="'.$infosupdef['Infosupdef']['code'].'InputFichier" style="display: none;"></span>';
					echo '<span id="'.$infosupdef['Infosupdef']['code'].'AfficheFichier">';
					echo '['.$this->Html->link($this->data['Infosup'][$infosupdef['Infosupdef']['code']], '/infosups/download/'.$this->data['Deliberation']['id'].'/'.$infosupdef['Infosupdef']['id'], array('title'=>$infosupdef['Infosupdef']['commentaire'])).']';
					echo '&nbsp;&nbsp;';
					if (!$disabled)
						echo $this->Html->link('Supprimer', "javascript:infoSupSupprimerFichier('".$infosupdef['Infosupdef']['code']."', '".$infosupdef['Infosupdef']['commentaire']."')", null, 'Voulez-vous vraiment supprimer le fichier joint ?\n\nAttention : ne prendra effet que lors de la sauvegarde\n');
					echo '</span>';
				}
			} elseif ($infosupdef['Infosupdef']['type'] == 'odtFile') {
				if (empty($this->data['Infosup'][$infosupdef['Infosupdef']['code']])
                                    || empty($this->data['Infosup'][$infosupdef['Infosupdef']['code']]['tmp_name'])
                                    || isset($errors_Infosup[$infosupdef['Infosupdef']['code']]))
					echo  $this->Form->input($fieldName, array('label'=>false, 'type'=>'file', 'size'=>'60', 'title'=>$infosupdef['Infosupdef']['commentaire'], 'readonly'=> $disabled));
				else {
					echo '<span id="'.$infosupdef['Infosupdef']['code'].'InputFichier" style="display: none;"></span>';
					echo '<span id="'.$infosupdef['Infosupdef']['code'].'AfficheFichier">';
					if (Configure::read('GENERER_DOC_SIMPLE')) {
						echo '['.$this->Html->link($this->data['Infosup'][$infosupdef['Infosupdef']['code']], '/infosups/download/'.$this->data['Deliberation']['id'].'/'.$infosupdef['Infosupdef']['id'], array('title'=>$infosupdef['Infosupdef']['commentaire'],  'readonly'=> $disabled)).']';
					} else {
						$name = $this->data['Infosup'][$infosupdef['Infosupdef']['code']] ;
						if (!$disabled){
						    $url = Configure::read('PROTOCOLE_DL')."://".$_SERVER['SERVER_NAME']."/files/generee/projet/".$this->data['Deliberation']['id']."/$name"; 
                                                    echo $this->Form->hidden($fieldName);
                                                }
                                              else 
                                                    $url = "http://".$_SERVER['SERVER_NAME']."/files/generee/projet/".$this->data['Deliberation']['id']."/$name";
						echo "<a href='$url'>$name</a> ";
					}
					echo '&nbsp;&nbsp;';
					if (!$disabled)
						echo $this->Html->link('Supprimer', "javascript:infoSupSupprimerFichier('".$infosupdef['Infosupdef']['code']."', '".$infosupdef['Infosupdef']['commentaire']."')", null, 'Voulez-vous vraiment supprimer le fichier joint ?\n\nAttention : ne prendra effet que lors de la sauvegarde\n');
					echo '</span>';
				}
			} elseif ($infosupdef['Infosupdef']['type'] == 'list') {
                            if (!$disabled){
				echo $this->Form->input($fieldName, array('label'=>false, 'options'=>$infosuplistedefs[$infosupdef['Infosupdef']['code']], 'empty'=>true, 'title'=>$infosupdef['Infosupdef']['commentaire'], 'readonly'=> $disabled));
                            }
                            else{
                                echo $this->Form->input($fieldName, array('label'=>false, 'options'=>$infosuplistedefs[$infosupdef['Infosupdef']['code']], 'empty'=>true, 'title'=>$infosupdef['Infosupdef']['commentaire'], 'disabled'=> $disabled));
                                echo $this->Form->input($fieldName, array('id'=> false, 'type'=> 'hidden'));
                            }
                        }
		echo '</div>';
		echo '<br>';
                echo "<div class='spacer'> </div>";
	};?>
</div>
<?php endif; ?>

<div class="spacer" style="border-top: solid 1px #e0ef90;"></div>

<div class="submit">
<?php 
echo $this->Html->tag("div", null, array("class" => "btn-group"));
echo $this->Html->link('<i class="fa fa-arrow-left"></i> Annuler', array('action' => 'mesProjetsRedaction'), array('class' => 'btn', 'escape' => false, 'title' => 'Annuler', 'name'=>'Annuler'));
echo $this->Form->button('<i class="fa fa-save"></i> Sauvegarder', array('type' => 'submit', 'id' => 'boutonValider', 'class' => 'btn btn-primary', 'escape' => false, 'title' => 'Enregistrer le circuit de traitement'));
echo $this->Html->tag('/div', null);
?>
</div>
<?php echo $this->Form->end(); ?>
<script>
//Pour savoir quel onglet on a coché
function afficheOngletNew(obj, afficheTextId){
        $(obj).append('<input type="hidden" name="lienTab" value="'+afficheTextId+'" />');
        $(obj).submit();
}
</script>