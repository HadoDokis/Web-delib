<?php        
$panel_left ='<b>'.__('Date de la prochaine exécution', true) .' : </b><br><br>' .
             '<b>'.__('Heure de la prochaine exécution', true).' : </b><br><br>' .
             '<b>'.__('Délais entre deux exécutions', true).' : </b><br><br>' .
             '<b>'.__('Activation', true).' : </b>';
        
$panel_right = $this->Form->input('next_execution_date', array(
    'label' => false,
    'type' => 'date',
    'dateFormat' => 'DMY',
    'minYear' => date('Y') - 0,
    'maxYear' => date('Y') + 2,
    'monthNames' => false,
    'empty' => true
)).'<br>' .
$this->Form->input('next_execution_heure', array(
    'label' => false, 
    'type' => 'time', 
    'timeFormat' => '24', 
    'interval' => 15)).'<br>' .
$this->DurationPicker->picker('Cron.execution_duration', array(
    'label' => false, 
    'empty' => true, 
    'value' => $this->data['Cron']['execution_duration'])) .'<br>' .
$this->Form->input('active', array('label' => false));

echo $this->Html->tag('h3', __('Planification de la tâche', true) . ' : ' . $this->data['Cron']['nom']) .
$this->BsForm->create('Crons',array('type'=>'post', 'url' => array(
    'controller' => 'crons', 
    'action' => 'planifier'))) .
$this->Bs->panel('Planification de la tâche') .
    $this->Bs->row() .
    $this->Bs->col('xs2') .
    $this->Bs->close() .
    $this->Bs->col('xs3').$panel_left .
    $this->Bs->close() .
    $this->Bs->col('xs7').$panel_right .
    $this->Bs->close(2) .
    $this->Bs->div('spacer').$this->Bs->close() .
$this->Bs->endPanel() .
$this->Html2->btnSaveCancel('', $previous, 'Valider', 'Valider') .
$this->Form->hidden('id') .
$this->BsForm->end();