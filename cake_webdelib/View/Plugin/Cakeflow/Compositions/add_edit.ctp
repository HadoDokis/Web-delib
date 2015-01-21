<?php
echo $this->Html->css("Cakeflow.design.css");
$this->pageTitle = $request . __('d\'une composition à l\'étape du circuit de traitement');
$this->Html->addCrumb(__('Liste des circuits'), array('controller' => 'circuits', 'action' => 'index'));
$this->Html->addCrumb(__('Étapes du circuit'), array('controller' => 'etapes', 'action' => 'index', $etape['Circuit']['id']));
$this->Html->addCrumb(__('Composition de l\'étape'), array('controller' => 'compositions', 'action' => 'index', $circuit_id));
$this->Html->addCrumb($request);
echo $this->Html->tag('h3', $this->pageTitle);
echo $this->Bs->tag('div', "<div class='spacer'></div>");
echo $this->BsForm->create('Composition', array('url' => Router::url(null, true)));
if ($this->action == 'edit')
    echo $this->Html->tag('div', $this->BsForm->input('Composition.id', array('type' => 'hidden')));
echo $this->Html->tag('div', $this->BsForm->input('Composition.etape_id', array('type' => 'hidden')));
echo $this->Bs->row();
echo $this->Bs->col('lg12') .
 $this->Html->tag('div', null, array('style' => "float: left;")) .
// sélection du type de composition
$this->BsForm->select('type_composition', $typeCompositions, array('empty' => $canAddParapheur, 'label' => 'Type de composition')) .
 $this->Html->tag('/div') .
 $this->Html->tag('div', null, array('id' => 'userDiv', 'style' => " float: left;")) .
 $this->Bs->tag('div', "<i class='fa fa-arrow-right'></i>", array('style' => "float: left; position: relative; top: 10px; margin-right:35px; margin-left:15px;")) . //array('style' => "float: left; position: relative; top: 10px; margin-right:35px; margin-left:15px;")
$this->Html->tag('div', null, array('style' => 'float: left;')) .
 $this->BsForm->select('selectUser', $triggers, array('label' => CAKEFLOW_TRIGGER_TITLE, 'name' => 'Composition.trigger_id')) .
 $this->Html->tag('div', $this->BsForm->input('Composition.trigger_id', array('type' => 'hidden'))) .
 $this->Html->tag('/div') .
 $this->Html->tag('/div') .
 $this->Bs->close();

if ($canAddParapheur) {
    echo $this->Html->tag('div', null, array('id' => 'soustype', 'style' => "display:none; float: left;"));
    echo $this->Html->tag('div', "<i class='fa fa-arrow-right'></i>", array('style' => "float: left; position: relative; top: 28px; margin-right:20px; margin-left:20px;"));
    echo $this->Html->tag('div', null, array('style' => 'float: left;'));
    echo $this->BsForm->input('soustype', array('type' => 'select', 'label' => __('Sous-Types de "' . Configure::read('IPARAPHEUR_TYPE') . '"', true), 'empty' => false, 'options' => $soustypes));
    echo $this->Html->tag('/div');
    echo $this->Html->tag('/div');
} //FIN IF USE_IPARAPHEUR

if (CAKEFLOW_GERE_SIGNATURE) {
    echo $this->Html->tag('div', null, array('style' => 'float: left; margin-bottom: 20px; clear: both;', 'id' => 'typeValidation'));
    echo $this->BsForm->input('Composition.type_validation', array('type' => 'radio', 'label' => 'Type de validation'));
    echo $this->Html->tag('/div');
} else {
    echo $this->Html->tag('div', $this->BsForm->input('Composition.type_validation', array('type' => 'hidden', 'value' => 'V')));
}
echo $this->Bs->close();
echo $this->Bs->row();
echo $this->Bs->col('lg12');
echo $this->Html->tag('div', $this->Html2->btnSaveCancel('', $previous), array('class' => 'col-md-offset-2'));
echo $this->Bs->close();
echo $this->Bs->close();
echo $this->BsForm->end();
?>
<script type="text/javascript">
    $(document).ready(function () {

        $('#CompositionTypeComposition').select2({
            allowClear: false,
            placeholder: 'Aucun service',
            width: 'resolve'
        });

        $('#CompositionSelectUser').select2({
            allowClear: false,
            placeholder: 'Aucun service',
            width: 'resolve',
        });
        $("#CompositionSelectUser").on("change", function () {
            $('#CompositionTriggerId').val($('#CompositionSelectUser').val());
        })
        $("#boutonValider").hide();
<?php
if (!$canAddParapheur) {
//            echo "$(\"option[value='PARAPHEUR']\").attr('disabled', true)";
    echo "$(\"option[value='PARAPHEUR']\").hide();";
//            echo "$('#userDiv').show();";
}
if (CAKEFLOW_GERE_SIGNATURE) {
    echo '$("#typeValidation").hide();';
    echo '$("#CompositionTypeValidationD").hide();';
    echo '$("label[for=\'CompositionTypeValidationD\']").hide();';
}
?>

        $('#CompositionTypeComposition').on('change', onChangeAction);
        onChangeAction();
    });

    function onChangeAction() {
        var selectedOption = $('#CompositionTypeComposition').val();
        $('#userDiv').hide();
        $('#tmp_parapheur').remove();
        if (selectedOption == '') {
            $('#boutonValider').hide();
        } else {
            if (selectedOption == 'USER') {
                $('#userDiv').show();
                //$('#tmp_custom').remove();
                $("input[name='data[Composition][type_validation]']").val('V');
<?php
if (CAKEFLOW_GERE_SIGNATURE) {
    echo '$("#typeValidation").show();';
}
?>
            } else if (selectedOption == 'PARAPHEUR') {
                //$('#tmp_custom').remove();
                $('#CompositionSelectUser').append('<option value="-1" id="tmp_parapheur">Parapheur</option>');
                $('#CompositionSelectUser').val("-1");
                $("input[name='data[Composition][type_validation]']").val('D');
                $('#soustype').show();
<?php
if (CAKEFLOW_GERE_SIGNATURE) {
    echo '$("#typeValidation").hide();';
}
?>
                domaine.lastIndexOf(".")
            } else {
                //rajoute un champ ds select user et donc informe du triger_id de la table composition
                //$('#tmp_custom').remove();
                //$('#CompositionSelectUser').append('<option value="' + $('#CompositionTypeComposition').val() + '" id="tmp_custom">custom</option>');
                //selectionne le champ précédement créé
                //$('#CompositionSelectUser').val($('#CompositionTypeComposition').val());
                //$("input[name='data[Composition][type_validation]']").val('C');

                //$('#soustype').show();
<?php
if (CAKEFLOW_GERE_SIGNATURE) {
    echo '$("#typeValidation").hide();';
}
?>
            }
            $('#boutonValider').show();
        }
    }

</script>