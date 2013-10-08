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
        <?php echo $this->Form->input('Deliberation.typeacte_id', array('label'    => 'Type d\'acte <acronym title="obligatoire">(*)</acronym>', 
                                                                        'options'  => $this->Session->read('user.Nature'), 
                                                                        'empty'    => '(sélectionner le type d\'acte)', 
                                                                        'id'       => 'listeTypeactesId',
                                                                        'onChange' => "updateTypeseances(this);", 
                                                                        'escape'   => false));  ?>
	<div class='spacer'></div>
 	<?php echo $this->Form->input('Deliberation.objet', array('type'=>'textarea','label'=>'Libellé <acronym title="obligatoire">(*)</acronym>','cols' => '60','rows'=> '2'));?>

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
	<?php echo $this->Form->input('Deliberation.theme_id', array('label'=>'Thème <acronym title="obligatoire">(*)</acronym>', 'empty'=>'(sélectionner le thème)', 'escape'=>false)); ?>
	<div class='spacer'></div>

	<?php 
        if ($USE_PASTELL)
                echo $this->Form->input('Deliberation.num_pref_libelle', array('label'=>'Nomenclature', 'options'=>$nomenclatures, 'default'=>$this->Html->value('Deliberation.num_pref'), 'empty'=>true, 'escape'=>false)); 
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
	<input name="date_limite" size="9" <?php echo $value; ?>"/>&nbsp;<a href="javascript:show_calendar('Deliberation.date_limite','f');" alt="" id="afficheCalendrier"><?php echo $this->Html->image("calendar.png", array('style'=>"border='0'")); ?></a>
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

<div class="spacer" style="border-top: solid 1px #e0ef90;"></div>

<div class="submit">
<?php 
echo $this->Html->tag("div", null, array("class" => "btn-group"));
echo $this->Html->link('<i class="icon-circle-arrow-left"></i> Annuler', array('action' => 'mesProjetsRedaction'), array('class' => 'btn', 'escape' => false, 'title' => 'Annuler', 'name'=>'Annuler'));
echo $this->Form->button('<i class="icon-save"></i> Sauvegarder', array('type' => 'submit', 'id' => 'boutonValider', 'class' => 'btn btn-primary', 'escape' => false, 'title' => 'Enregistrer le circuit de traitement'));
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