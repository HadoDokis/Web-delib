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
'<b><u>Service émetteur</u></b> : <i>' . $this->Html->value('Service.libelle') . '</i>'.
$this->Bs->close();

echo    $this->BsForm->input('Deliberation.typeacte_id', array(
            'label' => 'Type d\'acte <abbr title="obligatoire">*</abbr>',
            'options' => $this->Session->read('user.Nature'),
            'empty' => true,
            'id' => 'listeTypeactesId',
            'onChange' => "updateTypeseances(this);",
            'escape' => false,
            'required'
        )).
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
$this->BsForm->input('Deliberation.rapporteur_id', array(
            'label' => 'Rapporteur',
            'options' => $rapporteurs,
            'class' => 'select2 selectone',
            'empty' => true)).
 $this->BsForm->input('Deliberation.theme_id', array(
            'label' => 'Thème <abbr title="obligatoire">*</abbr>',
            'empty' => true,
            'class' => 'select2 selectone',
            'escape' => false));

if ($USE_PASTELL) {
        echo $this->BsForm->input('Deliberation.num_pref', array(
            'label' => 'Nomenclature',
            'options' => $nomenclatures,
            'default' => $this->Html->value('Deliberation.num_pref'),
            'disabled' => empty($nomenclatures),
            'empty' => true,
            'class' => 'select2 selectone',
            'escape' => false));
    } else {
        echo $this->BsForm->inputGroup('Deliberation.num_pref_libelle', array(
                            'content'=>'<i class="fa fa-eraser"></i>',
                            'type' => 'button',
                            'id'=>'deselectClassif',
                            'state' => 'primary',
                            'side'=>'right'), array(
                                'label' => 'Classification',
                                'placeholder' => 'Cliquer ici pour choisir la classification',
                                'onclick' => "javascript:window.open('" . Router::url(array('controller' => 'deliberations', 'action' => 'classification')) . "', 'Select_attribut', 'scrollbars=yes,width=570,height=450');",
                                'id' => 'classif1',
                                'title' => 'Selection de la classification',
                                'readonly' => 'readonly',
                                'class'=>'pull-left'));
        echo $this->Form->hidden('Deliberation.num_pref', array('id' => 'num_pref'));

    }
    
    echo $this->BsForm->datetimepicker('Deliberation.date_limite', array('language'=>'fr', 'autoclose'=>'true','format' => 'dd/mm/yyyy',), array(
    'label' => 'Date limite',
    'title' => 'Choisissez une date',
    'style' => 'cursor:pointer',
    'help' => 'Cliquez sur le champs ci-dessus pour choisir la date',
    'readonly' => 'readonly',
    'value'=>isset($date)?$date:''));
    //if ($DELIBERATIONS_MULTIPLES)
   
$this->BsForm->setLeft(0);
echo $this->BsForm->checkbox('Deliberation.is_multidelib', array(
     'autocomplete' => 'off',
     'label' => 'Multi-Délibération',
 ));
$this->BsForm->setLeft(3);
   // $this->BsForm->setLeft(3);
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