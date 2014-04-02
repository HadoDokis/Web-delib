<?php echo $this->Form->create('Seances', array('url' => '/seances/saisirSecretaire/' . $seance_id, 'type' => 'post')); ?>
<fieldset>
    <legend>Choix du Président de séance</legend>
    <?php echo $this->Form->input('Acteur.president_id', array(
        'label' => 'Président de séance',
        'class' => 'select2 selectone',
        'options' => $acteurs,
        'default' => $selectedPresident,
        'empty' => true,
        'div' => false));?>
</fieldset>
<br/>
<fieldset>
    <legend>Choix du secrétaire de séance</legend>
    <?php echo $this->Form->input('Acteur.secretaire_id', array(
        'label' => 'Secrétaire de séance',
        'class' => 'select2 selectone',
        'options' => $acteurs,
        'default' => $selectedActeurs,
        'empty' => true,
        'div' => false));?>
</fieldset>
<br/>
<?php $this->Html2->boutonsAddCancel('', array('action'=>'listerFuturesSeances')); ?>
<?php echo $this->Form->end(); ?>
<script type="text/javascript">
$(".select2.selectone").select2({
        width: "element",
        allowClear: true,
        dropdownCssClass: "selectMaxWidth",
        dropdownAutoWidth: true,
        placeholder: "Selectionnez un élément",
        formatSelection: function (object, container) {
            // trim sur la sélection (affichage en arbre)
            return $.trim(object.text);
        }
    });
</script>

