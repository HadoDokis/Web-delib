<?php
if (!empty($seances)) :
    echo $this->Form->input('Seance', array(
        'options' => $seances,
        'label' => 'Date de séances',
        'multiple' => true,
        'id' => 'SeanceSeance',
        'name' => 'data[Seance][Seance]'));
    echo $this->Html->tag('div', '', array('class' => 'spacer'));
?>
<script>
    $("#SeanceSeance").select2({
        width: "80%",
        allowClear: true,
        placeholder: "Selection vide"
    });
</script>
<?php endif; ?>