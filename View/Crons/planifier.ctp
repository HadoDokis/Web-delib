<style>
    div.date select, div.time select{
        width: auto;
    }
    label{
        text-align: left;
        width: auto;
        min-width: 200px;
    }
    select, input{
        margin: 5px;
    }
</style>
<?php
echo $this->Html->tag('h2', __('Planification de la tâche', true) . ' : ' . $this->data['Cron']['nom']);
echo $this->Form->create(null, array('action' => "planifier/"));
echo $this->Form->input('next_execution_date', array('label' => __('Date de la prochaine exécution', true),
    'type' => 'date',
    'dateFormat' => 'DMY',
    'minYear' => date('Y') - 0,
    'maxYear' => date('Y') + 2,
    'monthNames' => false,
    'empty' => true
));

echo $this->Form->input('next_execution_heure', array('label' => __('Heure de la prochaine exécution', true), 'type' => 'time', 'timeFormat' => '24', 'interval' => 15));
echo $this->DurationPicker->picker('Cron.execution_duration', array('label' => 'Délais entre deux exécutions', 'empty' => true, 'value' => $this->data['Cron']['execution_duration']));
echo $this->Form->input('active', array('label' => __('Activation', true)));
echo $this->Form->hidden('id');
echo $this->Html->tag("div", null, array("class" => "btn-group", 'style'=>'clear: both; left: 210px;'));
echo $this->Html->link('<i class="fa fa-arrow-left"></i> Annuler', array('action' => 'index'), array('class' => 'btn', 'escape' => false));
echo $this->Form->button('<i class="fa fa-check"></i> Valider', array('type' => 'submit', 'id' => 'boutonValider', 'class' => 'btn btn-primary', 'escape' => false));
echo $this->Html->tag('/div', null);
echo $this->Form->end();
?>