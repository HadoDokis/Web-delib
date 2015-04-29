<?php
//préparation du tableau
$panel_left = array();
$panel_left[] = '<b>'.__('Date de la prochaine exécution', true) .' : </b>';
$panel_left[] = '<b>'.__('Heure de la prochaine exécution', true).' : </b>';
$panel_left[] = '<b>'.__('Délais entre deux exécutions', true).' : </b>';
$panel_left[] = '<b>'.__('Activation', true).' : </b>';
$panel_right = array();
$panel_right[] = $this->Form->input('Cron.next_execution_date', array(
    'label' => false,
    'type' => 'date',
    'dateFormat' => 'DMY',
    'minYear' => date('Y') - 0,
    'maxYear' => date('Y') + 2,
    'monthNames' => false,
    'empty' => true
));
$panel_right[] = $this->Form->input('Cron.next_execution_heure', array(
    'label' => false, 
    'type' => 'time', 
    'timeFormat' => '24', 
    'interval' => 15));
$panel_right[] = $this->DurationPicker->picker('Cron.execution_duration', array(
    'label' => false, 
    'empty' => true, 
    'value' => $this->data['Cron']['execution_duration']));
$panel_right[] = $this->Form->input('Cron.active', array('label' => false));

$content = '';
foreach ($panel_left as $key=>$val){
    $content .= $this->Bs->row() .
    $this->Bs->col('xs3') .
    $this->Bs->close() .
    $this->Bs->col('xs3').$panel_left[$key] .
    $this->Bs->close() .
    $this->Bs->col('xs3').$panel_right[$key] .
    $this->Bs->close(2) .
    $this->Bs->div('spacer').$this->Bs->close();
}

//affichage
$this->Html->addCrumb(__('Planification de la tâche'), array('controller' => 'crons', 'action' => 'index'));
$this->Html->addCrumb($this->data['Cron']['nom']);
echo $this->Html->tag('h3', $this->data['Cron']['nom']) .
$this->BsForm->create('Crons',array('type'=>'post', 'url' => array(
    'controller' => 'crons', 
    'action' => 'planifier'))) .
$this->Bs->panel('Détails').
$content .
$this->Bs->endPanel() .
        
$this->Html2->btnSaveCancel('', $previous, 'Valider', 'Valider') .
$this->Form->hidden('Cron.id') .
$this->BsForm->end();