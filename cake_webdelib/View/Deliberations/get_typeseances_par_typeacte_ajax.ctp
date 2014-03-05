<?php
if (!empty($typeseances)) :
    echo $this->Form->input('Typeseance', array(
        'options' => $typeseances,
        'id' => 'TypeseanceTypeseance',
        'name' => 'data[Typeseance][Typeseance]',
        'label' => 'Types de séance',
        'onchange' => "updateDatesSeances(this);",
        'multiple' => true));
    echo $this->Html->tag('div', '', array('class' => 'spacer'));
?>
<script>
    $("#TypeseanceTypeseance").select2({
        width: "80%",
        allowClear: true,
        placeholder: "Selection vide"
    });
</script>
<?php endif; ?>