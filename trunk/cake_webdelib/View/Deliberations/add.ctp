<?php 
echo $this->Html->script('utils.js');
echo $this->Html->script('deliberation.js');

if ($DELIBERATIONS_MULTIPLES)
    echo $this->Html->script('multidelib.js');

echo $this->Html->script('ckeditor/ckeditor');
echo $this->Html->script('ckeditor/adapters/jquery');

echo $this->Html->script('/components/smalot-bootstrap-datetimepicker/js/bootstrap-datetimepicker.min');
echo $this->Html->script('/components/smalot-bootstrap-datetimepicker/js/locales/bootstrap-datetimepicker.fr');
echo $this->Html->css('/components/smalot-bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css');

$this->Html->addCrumb('Mes projets', array('controller'=>'deliberations', 'action'=>'mesProjetsRedaction'));
$this->Html->addCrumb('Ajout d\'un projet');

echo $this->Bs->tag('h3', 'Ajout d\'un projet');
echo $this->BsForm->create('Deliberation', array('url' => array('controller' => 'deliberations', 'action' => 'add'), 'type' => 'post'));

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
'<b><u>Rédacteur</u></b> : <i>' . $this->Html->value('Redacteur.prenom') . ' ' . $this->Html->value('Redacteur.nom') . '</i>'.
$this->Html->tag(null, '<br />') .
'<b><u>Service émetteur</u></b> : <i>' . $this->Html->value('Service.name') . '</i>'.
$this->Bs->close();

echo $this->Bs->row().
    $this->Bs->col('xs6');

echo    $this->BsForm->select('Deliberation.typeacte_id', $typeActes, array(
            'label' => 'Type d\'acte <abbr title="obligatoire">*</abbr>',
            'onChange' => "updateTypeseances(this);",
            'escape' => false,
            'class' => 'select2 selectone',
            'placeholder'=> __('Sélection du type d\'acte'),
            'autocomplete' => 'off',
            'required' => true,
            'empty' => true,
        )).
        /*$this->Bs->scriptBlock('
            updateTypeseances($("#DeliberationTypeacteId"));
        ').*/
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
            'empty' => true,
            'placeholder' => 'Cliquer ici pour choisir un thème',
            'class' => 'select2 selectone',
            'autocomplete' => 'off',
            'escape' => false));

    echo $this->BsForm->select('Deliberation.num_pref', $nomenclatures, array(
        'label' => 'Classification',
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
echo $this->Bs->close().
    $this->Bs->col('xs6');
echo $this->Bs->close().
    $this->Bs->col('xs6');
  
echo $this->BsForm->select('Deliberation.rapporteur_id', $rapporteurs, array(
            'label' => 'Rapporteur',
            'class' => 'select2 selectone',
            'empty' => true)).
 $this->BsForm->datetimepicker('Deliberation.date_limite', array('language'=>'fr', 'autoclose'=>'true','format' => 'dd/mm/yyyy',), array(
    'label' => 'Date limite',
    'title' => 'Choisissez une date',
    'style' => 'cursor:pointer',
    'help' => 'Cliquez sur le champs ci-dessus pour choisir la date',
    'readonly' => 'readonly',
    'value'=>isset($date)?$date:''));

echo $this->Bs->div('','',array('id'=>'selectTypeseances'));
echo $this->Bs->div('','',array('id'=>'selectDatesSeances'));
echo $this->Bs->close(2);
echo $this->Bs->tabClose().
$this->Bs->tabPaneClose();


   /* echo $this->Html->tag('div', null, array('class' => 'btn-group'));
    echo $this->Html->link('<i class="fa fa-arrow-left"></i> Annuler', $previous, array('class' => 'btn', 'escape' => false, 'title' => 'Annuler'));
    echo $this->Form->button('<i class="fa fa-save"></i> Sauvegarder', array('type' => 'submit', 'id' => 'boutonValider', 'class' => 'btn btn-primary', 'escape' => false, 'title' => 'Enregistrer le circuit de traitement'));
    echo $this->Html->tag('/div', null);*/
    
echo $this->Html2->btnSaveCancel('', $previous).
        $this->BsForm->end();

echo $this->Bs->scriptBlock('
                    $(\'a[data-toggle="tab"]\').on(\'shown.bs.tab\', function (e) {
                        //e.target // activated tab
                        //e.relatedTarget // previous tab
                        $(\'#DeliberationAddForm\').append(\'<input type=\"hidden\" name=\"nameTab\" value=\"\' + $(e.target).text() + \'\" />\');
                        $(\'#DeliberationAddForm\').submit();
                      })');
if(!empty($typesactemulti)){
?>
<script type="text/javascript">
    <?php echo "var allowedMulti = ". json_encode($typesactemulti). ";\n"; ?>
    $(document).ready(function () {
        $('#listeTypeactesId').change(function () {
            if (jQuery.inArray(parseInt($(this).val()), allowedMulti) === -1) {
                $('#DeliberationIsMultidelib').prop('checked', false).parent().hide();
            } else {
                $('#DeliberationIsMultidelib').parent().show();
            }
        }).change();
    });
</script>
<?php } ?>