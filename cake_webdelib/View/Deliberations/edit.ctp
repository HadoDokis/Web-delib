<?php 
echo $this->Html->script('utils.js'); 
echo $this->Html->script('deliberation');

if ($DELIBERATIONS_MULTIPLES)
    echo $this->Html->script('multidelib'); 

echo $this->Html->script('ckeditor/ckeditor'); 
echo $this->Html->script('ckeditor/adapters/jquery'); 

echo $this->Html->script('/components/smalot-bootstrap-datetimepicker/js/bootstrap-datetimepicker.min');
echo $this->Html->script('/components/smalot-bootstrap-datetimepicker/js/locales/bootstrap-datetimepicker.fr');
echo $this->Html->css('/components/smalot-bootstrap-datetimepicker/css/bootstrap-datetimepicker.min');

echo $this->Html->script('/components/bootstrap-filestyle/src/bootstrap-filestyle');
echo $this->Html->script('/components/jquery-sortable/source/js/jquery-sortable-min');

echo $this->Html->css('/components/bootstrap-table/dist/bootstrap-table.min');
echo $this->Html->script('/components/bootstrap-table/dist/bootstrap-table.min');


$this->Html->addCrumb('Mes projets', array('controller'=>'deliberations', 'action'=>'mesProjetsRedaction'));
$this->Html->addCrumb('Modification d\'un projet');

echo $this->Bs->tag('h3', 'Modification du projet : ' . $this->Html->value('Deliberation.id'));

echo $this->BsForm->create('Deliberation', array(
    'url' => array('controller' => 'deliberations', 
        'action' => 'edit'), 'type' => 'file', 'novalidate' => true/*, 'name' => 'Deliberation'*/));

$aTab=array(
    'infos' => 'Informations principales',
    'textes' => 'Textes',
    'annexes' => 'Annexe(s)');

if (!empty($infosupdefs))
    $aTab['Infos_suppl']='Informations supplémentaires';
if (Configure::read('DELIBERATIONS_MULTIPLES'))
    $aTab['multidelib']='Délibérations rattachées';//'style' => 'display: none'
    
echo $this->Bs->tab($aTab, array(   'active' => isset($nameTab) ? $nameTab : 'infos', 
                                    'class' => '-justified')) .
 $this->Bs->tabContent();

echo $this->Bs->tabPane('infos', array('class' => isset($nameTab) ? $nameTab : 'active'));
//$this->BsForm->setLeft(0);
echo $this->Html->tag(null, '<br />') .
$this->Html->tag('div', null, array('class' => 'well well-lg')) .
        $this->Bs->row().$this->Bs->col('xs6').
'<b><u>Service émetteur</u></b> : <i>' . $this->Html->value('Service.name') . '</i>' .
$this->Html->tag(null, '<br />') .
'<b><u>Rédacteur</u></b> : <i>' . $this->Html->value('Redacteur.prenom') . ' ' . $this->Html->value('Redacteur.nom') . '</i>'.
$this->Bs->close().
$this->Bs->col('xs6').
'<b><u>Date création</u></b> : <i>' . $this->Time->i18nFormat($this->Html->value('Deliberation.created'), '%d/%m/%Y à %k:%M') . '</i>' .
$this->Html->tag(null, '<br />') .
'<b><u>Date de modification</u></b> : <i>' .  $this->Time->i18nFormat($this->Html->value('Deliberation.modified'), '%d/%m/%Y à %k:%M') . '</i>' .
$this->Bs->close(3);


echo $this->Bs->row().
    $this->Bs->col('xs6');
echo    $this->BsForm->select('Deliberation.typeacte_id', $typeActes, array(
            'label' => 'Type d\'acte <abbr title="obligatoire">*</abbr>',
            'escape' => false,
            'class' => 'select2 selectone',
            'required' => true,
            'empty' => false,
            'selected'=> $this->Html->value('Deliberation.typeacte_id')
        )).
//        $this->Bs->scriptBlock('
//            updateTypeseances($("#DeliberationTypeacteId"));
//        ').
$this->BsForm->input('Deliberation.objet', array(
    'type' => 'textarea', 
    'label' => 'Libellé <abbr title="obligatoire">*</abbr>', 
    'rows' => '2', 
    'required'
    )).
$this->BsForm->input('Deliberation.titre', array(
    'type' => 'textarea', 
    'label' => 'Titre', 
    'rows' => '2'
    )).
 $this->BsForm->select('Deliberation.theme_id', $themes, array(
            'label' => 'Thème <abbr title="obligatoire">*</abbr>',
            'selected' => $this->Html->value('Deliberation.theme_id'),
            'empty' => false,
            'class' => 'select2 selectone',
            'escape' => false));

    echo $this->BsForm->select('Deliberation.num_pref', $nomenclatures, array(
        'label' => 'Classification',
        'selected' => $this->Html->value('Deliberation.num_pref'),
        'placeholder' => 'Cliquer ici pour choisir la classification',
        'disabled' => empty($nomenclatures),
        'empty' => true,
        'class' => 'select2 selectone',
        'escape' => false));
    
    echo $this->BsForm->select('multiRedactor',$redacteurs,array('label' => 'Autre(s) rédacteur(s)','title' => 'Ajouter un rédacteur ayant les droits d\'incérer le projet dans un circuit','class' => 'select2 selectone','multiple' => true, 'style' => 'width:100%')); 
    //if ($DELIBERATIONS_MULTIPLES)
echo $this->BsForm->checkbox('Deliberation.is_multidelib', array(
     'autocomplete' => 'off',
     'label' => 'Multi-Délibération',
 ));
    
if ($DELIBERATIONS_MULTIPLES)
echo $this->BsForm->checkbox('Deliberation.is_multidelib', array(
     'autocomplete' => 'off',
     'label' => 'Multi-Délibération',
 ));

echo $this->Bs->close().
    $this->Bs->col('xs6');

echo $this->BsForm->select('Deliberation.rapporteur_id', $rapporteurs, array(
            'label' => 'Rapporteur',
            'class' => 'select2 selectone',
            'selected' => $this->Html->value('Deliberation.raporteur_id'),
            'empty' => true)).
     $this->BsForm->datetimepicker('Deliberation.date_limite', array('language'=>'fr', 'autoclose'=>'true','format' => 'dd/mm/yyyy',), array(
    'label' => 'Date limite',
    'title' => 'Choisissez une date',
    'style' => 'cursor:pointer',
    'help' => 'Cliquez sur le champs ci-dessus pour choisir la date',
    'readonly' => 'readonly',
    'value' => $this->Html->value('Deliberation.date_limite')
        )
    );

   $selectTypeseances='';
   if (!empty($typeseances)){
        $selectTypeseances .=   $this->BsForm->select('Typeseance', $typeseances, array(
                 'class' => 'select2 selectmultiple',
                 'label' => 'Types de séance',
                 'placeholder'=> __('Choisir un type de séance'),
                'autocomplete' => 'off',
                 'onchange' => "updateDatesSeances(this);",
                 'multiple' => true)
         );
   }
   $selectDatesSeances='';
   if (!empty($seances)){
                $selectDatesSeances .=  $this->BsForm->select('Seance', $seances, array(
                        'class' => 'select2 selectmultiple',
                        'label' => 'Dates de séance',
                        'autocomplete' => 'off',
                        'multiple' => true)); 
   }
echo $this->Bs->div('',$selectTypeseances,array('id'=>'selectTypeseances'));
echo $this->Bs->div('',$selectDatesSeances,array('id'=>'selectDatesSeances'));
echo $this->Bs->close(2);
echo $this->Bs->tabClose();

echo  $this->Bs->tabPane('textes', array('class' => (isset($nameTab) && $nameTab=='textes' ? 'active' : ''))); 
echo $this->Html->tag(null, '<br />') .    
    $this->element('texte', array('type' => 'texte_projet'));    
    echo $this->element('texte', array('type' => 'texte_synthese')); 
     
    echo '<div id="texteDelibOngletTextes"><div id="texteDeliberation">';
    echo $this->element('texte', array('type' => 'deliberation')); 
    echo '</div></div>';
    echo $this->Html->tag('span', 'Note : les modifications apportées ici ne prendront effet que lors de la sauvegarde du projet.',array('class'=>'help-block'));
    
 echo $this->Bs->tabClose();

    echo  $this->Bs->tabPane('annexes', array('class' => (isset($nameTab) && $nameTab=='annexes' ? 'active' : ''))); 
    echo $this->Html->tag(null, '<br />') .   
        '<div id="DelibOngletAnnexes"><div id="DelibPrincipaleAnnexes">';
    echo $this->element('annexe_edit', array_merge(array('ref' => 'delibPrincipale'), array('annexes' => $this->Html->value('Annex'))));
    echo '</div></div>';
    echo $this->Html->tag('span', 'Note : les modifications apportées ici ne prendront effet que lors de la sauvegarde du projet.',array('class'=>'help-block'));
    
 echo $this->Bs->tabClose();

if (!empty($infosupdefs)){
    echo  $this->Bs->tabPane('Infos_suppl', array('class' => (isset($nameTab) && $nameTab=='Infos_suppl' ? 'active' : ''))); 
    echo $this->Html->tag('br /');  
        foreach ($infosupdefs as $infosupdef) {
            // Amélioration 4.1 : on ne peut modifier une infosup qu'en fonction du profil
            $disabled = true;
            foreach ($infosupdef['Profil'] as $profil) {
            if ($profil['id'] == AuthComponent::user('profil_id'))
                $disabled = false;
            }

        if ($infosupdef['Infosupdef']['type'] == 'file' && $disabled) continue;

            $fieldName = 'Infosup.' . $infosupdef['Infosupdef']['code'];
            $fieldId = 'Infosup' . Inflector::camelize($infosupdef['Infosupdef']['code']);
            if ($infosupdef['Infosupdef']['type'] == 'text') {
                
                echo $this->BsForm->input($fieldName, array(
                    'label'=> $infosupdef['Infosupdef']['nom'],
                    'type' => 'textarea',
                    'rows' => '2',
                    'title' => $infosupdef['Infosupdef']['commentaire'], 
                    'readonly' => $disabled, 
                    'id' => 'infosup_' . $infosupdef['Infosupdef']['code']));
            
                
            } elseif ($infosupdef['Infosupdef']['type'] == 'boolean') {
                if (!$disabled)
                    echo $this->BsForm->checkbox($fieldName, array('label' => false, 
                        'title' => $infosupdef['Infosupdef']['commentaire'], 
                        'label'=>$infosupdef['Infosupdef']['nom'],
                        'id' => 'infosup_' . $infosupdef['Infosupdef']['code']));
                else {
                    echo $this->BsForm->checkbox($fieldName, array( 
                        'title' => $infosupdef['Infosupdef']['commentaire'], 
                        'label'=>$infosupdef['Infosupdef']['nom'],
                        'disabled' => $disabled, 
                        'id' => 'infosup_' . $infosupdef['Infosupdef']['code']));
                    echo $this->Form->input($fieldName, array('type' => 'hidden', 'id' => false));
                }
            } elseif ($infosupdef['Infosupdef']['type'] == 'date') {
                //$disabled
                    echo $this->BsForm->datetimepicker($fieldName, array('language'=>'fr', 'autoclose'=>'true','format' => 'dd/mm/yyyy','startView'=>'decade','minView'=>'day'), array(
                    'label'=>$infosupdef['Infosupdef']['nom'],
                    'title' => 'Choisissez une date',
                    'style' => 'cursor:pointer',
                    'help' => $disabled?'':'Cliquez sur le champs ci-dessus pour choisir la date ',//$infosupdef['Infosupdef']['commentaire'],
                    'readonly' => 'readonly',
                    'disabled' => $disabled,
                    'value'=>isset($date)?$date:''));
                    
            } elseif ($infosupdef['Infosupdef']['type'] == 'richText') {
                
                //TODO rich texte by Webodf
                echo '<div class="annexesGauche"></div>';
                if (!$disabled) {
                    echo '<div class="fckEditorProjet">';
                    echo $this->BsForm->input($fieldName, array(
                        'label'=>$infosupdef['Infosupdef']['nom'],
                        'type' => 'textarea', 
                        'id' => $fieldId));
                    echo $this->Fck->load($fieldId);
                    echo '</div>';
                } else {
                    echo $this->BsForm->input($fieldName, array(
                        'label'=>$infosupdef['Infosupdef']['nom'],
                        'type' => 'textarea', 
                        'readonly' => true, 
                        'id' => $fieldId));
                }
            } elseif ($infosupdef['Infosupdef']['type'] == 'file') {
                
                if (empty($this->data['Infosup'][$infosupdef['Infosupdef']['code']]))
                    echo $this->BsForm->input($fieldName, array(
                        'label'=>$infosupdef['Infosupdef']['nom'],
                        'type' => 'file', 
                        'class' => 'filestyle',
                        'data-buttonText'=>'Choisir un fichier',
                        'title' => $infosupdef['Infosupdef']['commentaire'], 
                        'disabled' => $disabled, 
                        'id' => 'infosup_' . $infosupdef['Infosupdef']['code']));
                else {
                    $name = $this->data['Infosup'][$infosupdef['Infosupdef']['code']];
                    if (is_array($name)) $name = $name['name'];
                    echo '<span id="' . $infosupdef['Infosupdef']['code'] . 'InputFichier" style="display: none;"></span>';
                    echo '<span id="' . $infosupdef['Infosupdef']['code'] . 'AfficheFichier">';
                    echo '[' . $this->Html->link($name, '/infosups/download/' . $this->data['Deliberation']['id'] . '/' . $infosupdef['Infosupdef']['id'], array('title' => $infosupdef['Infosupdef']['commentaire'])) . ']';
                    echo '&nbsp;&nbsp;';
                    if (!$disabled)
                        echo $this->Html->link('Supprimer', "javascript:infoSupSupprimerFichier('" . $infosupdef['Infosupdef']['code'] . "', '" . $infosupdef['Infosupdef']['commentaire'] . "')", null, 'Voulez-vous vraiment supprimer le fichier joint ?\n\nAttention : ne prendra effet que lors de la sauvegarde\n');
                    echo '</span>';
                }
            } elseif ($infosupdef['Infosupdef']['type'] == 'odtFile') {
                
                if (empty($this->data['Infosup'][$infosupdef['Infosupdef']['code']])
                    || empty($this->data['Infosup'][$infosupdef['Infosupdef']['code']]['tmp_name'])
                    || isset($errors_Infosup[$infosupdef['Infosupdef']['code']])
                )
                    echo $this->BsForm->input($fieldName, array(
                        'label'=>$infosupdef['Infosupdef']['nom'],
                        'type' => 'file', 
                        'class' => 'filestyle',
                        'help' => 'Fichier Odt seulement',
                        'data-buttonText'=>'Choisir un fichier',
                        'title' => $infosupdef['Infosupdef']['commentaire'], 
                        'disabled' => $disabled, 
                        'id' => 'infosup_' . $infosupdef['Infosupdef']['code']));
                else {
                    echo '<span id="' . $infosupdef['Infosupdef']['code'] . 'InputFichier" style="display: none;"></span>';
                    echo '<span id="' . $infosupdef['Infosupdef']['code'] . 'AfficheFichier">';
                        $name = $this->data['Infosup'][$infosupdef['Infosupdef']['code']];
                        if (!$disabled) {
                            $url = Configure::read('PROTOCOLE_DL') . "://" . $_SERVER['SERVER_NAME'] . "/files/generee/projet/" . $this->data['Deliberation']['id'] . "/$name";
                            echo $this->Form->hidden($fieldName);
                        } else
                            $url = "http://" . $_SERVER['SERVER_NAME'] . "/files/generee/projet/" . $this->data['Deliberation']['id'] . "/$name";
                        echo "[<a href='$url'>$name</a>]";
                    echo '&nbsp;&nbsp;';
                    if (!$disabled)
                        echo $this->Html->link('Supprimer', "javascript:infoSupSupprimerFichier('" . $infosupdef['Infosupdef']['code'] . "', '" . $infosupdef['Infosupdef']['commentaire'] . "')", null, 'Voulez-vous vraiment supprimer le fichier joint ?\n\nAttention : ne prendra effet que lors de la sauvegarde\n');
                    echo '</span>';
                }
            } elseif ($infosupdef['Infosupdef']['type'] == 'list') {
                echo $this->BsForm->select($fieldName,array_merge(array(''=>''),$infosuplistedefs[$infosupdef['Infosupdef']['code']]),  array(
                        'label'=>$infosupdef['Infosupdef']['nom'],
                        'data-placeholder'=>'Sélectionnez un : '.$infosupdef['Infosupdef']['nom'],
                        'empty' => true, 'title' => $infosupdef['Infosupdef']['commentaire'], 
                        'disabled' => $disabled, 
                        'id' => 'infosup_' . $infosupdef['Infosupdef']['code'],
                        'class'=>'select2 selectone'));
                
                if ($disabled) {
                    echo $this->Form->input($fieldName, array('id' => false, 'type' => 'hidden'));
                }
            } elseif ($infosupdef['Infosupdef']['type'] == 'listmulti') {
                $selected_values = !empty($this->request->data['Infosup'][$infosupdef['Infosupdef']['code']]) ? $this->request->data['Infosup'][$infosupdef['Infosupdef']['code']] : null;
                
                    echo $this->BsForm->select($fieldName,array_merge(array(''=>''), $infosuplistedefs[$infosupdef['Infosupdef']['code']]), 
                        array(
                        'label'=>$infosupdef['Infosupdef']['nom'],
                        'selected' => $selected_values, 
                       // 'empty' => true, 
                        'disabled' => $disabled, 
                        'help' => $infosupdef['Infosupdef']['commentaire'], 
                        'multiple' => true, 
                        'class' => 'select2 selectmultiple', 
                        'data-placeholder'=>'Sélectionnez un ou plusieurs : '.$infosupdef['Infosupdef']['nom'],
                        'id' => 'infosup_' . $infosupdef['Infosupdef']['code']));
                    if (!$disabled)
                    echo $this->BsForm->select($fieldName, array_merge(array(''=>''), $infosuplistedefs[$infosupdef['Infosupdef']['code']]), 
                            array(
                                'selected' => $selected_values, 
                                'id' => false,
                                'multiple' => true, 
                                'hidden' => false, 
                                'style' => 'display:none;', 
                                'label'=>false, 'div' => false));
            }
        }
        
   echo $this->Bs->tabClose();      
}


if (Configure::read('DELIBERATIONS_MULTIPLES')) {
    echo  $this->Bs->tabPane('multidelib', array('class' => (isset($nameTab) && $nameTab=='multidelib' ? 'active' : ''))); 
    echo $this->Html->tag('br /');  
    
        if (empty($this->data['Deliberation']['parent_id']))
            echo $this->element('multidelib');
        else {
            echo $this->Html->tag('strong', 'Délibération parent : ' . $this->data['Deliberation']['parent_id']);
            echo '<div class="btn-group">';
            echo $this->Html->link('<i class="fa fa-search"></i> Voir', array('action' => 'view', $this->data['Deliberation']['parent_id']), array('escape' => false, 'class' => 'btn btn-mini'));
            echo $this->Html->link('<i class="fa fa-edit"></i> Modifier', array('action' => 'edit', $this->data['Deliberation']['parent_id']), array('escape' => false, 'class' => 'btn btn-mini'));
            echo '</div>';
            echo '<div class="spacer"></div>';
        }
echo  $this->Bs->tabClose();
}

echo $this->Bs->tabPaneClose();

echo $this->Form->hidden('Deliberation.id');
echo $this->Html2->btnSaveCancel('', $previous).
        $this->BsForm->end();
?>

<script type="text/javascript">
    <?php
    echo "var gabarits = ". json_encode($gabarits_acte). ";\n";
    echo "var extensions = ". json_encode($extensions).";\n";
    echo "var extensionsFusion = ". json_encode($extensionsFusion).";\n";
    echo "var extensionsCtrl = ". json_encode($extensionsCtrl).";\n";
    ?>
    var current_gabarit_name;
    $(document).ready(function () {
        // Gestion des gabarits selon le type d'acte
        current_gabarit_name = gabarits[$('#listeTypeactesId').val()];
        $('#listeTypeactesId').change(function () {
            current_gabarit_name = gabarits[$('#listeTypeactesId').val()];
            //Le type d'acte possède un gabarit de texte d'acte
            if (current_gabarit_name != undefined) {
                $('#ajouteMultiDelib .gabarit_name_multidelib').text(current_gabarit_name);
                $('#ajouteMultiDelib .MultidelibGabaritBloc').show();
                $('#ajouteMultiDelib .texte_acte_multidelib').each(function () {
                    //Le formulaire d'upload est vide
                    if ($(this).val() == '') {
                        //Désactivation et masquage du champ d'upload
                        $(this).prop('disabled', true).hide();
                        $(this).closest('.delibRattachee').find('.MultidelibGabaritBloc').show();
                        $(this).closest('.delibRattachee').find('input.gabarit_acte_multidelib').prop('disabled', false);
                    } else { //Formulaire d'upload non
                        //Activationvide
                        $(this).prop('disabled', false).show();
                        $(this).closest('.delibRattachee').find('input.gabarit_acte_multidelib').prop('disabled', true);
                        $(this).closest('.delibRattachee').find('.MultidelibGabaritBloc').hide();
                    }
                });
            } else { // Pas de gabarit associé au type d'acte
                $('#ajouteMultiDelib .MultidelibGabaritBloc').hide();
                $('#ajouteMultiDelib .delibRattachee input.gabarit_acte_multidelib').prop('disabled', true);
                $('#ajouteMultiDelib .texte_acte_multidelib').prop('disabled', false).show();
            }
        });

        // Gestion des formats d'annexe (affichage checkboxes)
        $("#Annex0File").change(function(){
            if ($(this).val() != '') {
                var tmpArray = $(this).val().split('.');
                var extension = tmpArray[tmpArray.length - 1];
                extension = extension.toLowerCase();
                if ($.inArray(extension, extensions) === -1) {
                    $.growl( 
                    {
                        title: "<strong>Erreur :</strong>",
                        message: "Les fichiers " + extension + " ne sont pas autorisés.",
                    },{type:"danger"});
                    $(this).val(null);
                    return false;
                } else {
                    if ($.inArray(extension, extensionsFusion) === -1){
                        $('#Annex0Fusion').prop('checked', false).closest('div').hide();
                    }else{
                        $('#Annex0Fusion').closest('div').show();
                    }
                    if ($.inArray(extension, extensionsCtrl) === -1){
                        $('#Annex0Ctrl').prop('checked', false).closest('div').hide();
                    }else{
                        $('#Annex0Ctrl').closest('div').show();
                    }
                }

            }
        });


    })
</script>