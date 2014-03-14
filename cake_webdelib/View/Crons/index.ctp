<?php
$superadmin = false;
echo $this->Html->css('crons');
echo $this->Html->tag('h1', __('Liste des tâches planifiées', true));

echo $this->Html->tag('table', null, array('cellpadding' => '0', 'cellspacing' => '0', 'class' => "table table-bordered table-hover"));
// initialisation de l'entête du tableau
$listeColonnes = array();
$listeColonnes[] = __('Etat', true);
$listeColonnes[] = __('Nom', true);
$listeColonnes[] = __('Date exécution prévue', true);
$listeColonnes[] = __('Délai entre 2 exécutions', true);
$listeColonnes[] = __('Date dernière exécution', true);
$listeColonnes[] = __('Active', true);
$listeColonnes[] = __('Actions', true);

echo $this->Html->tableHeaders($listeColonnes);
foreach ($this->data as $rownum => $rowElement) {
    if ($rowElement['Cron']['lock'])
        $rowClass = array('class' => 'error');
    elseif ($rowElement['Cron']['last_execution_status'] == Cron::EXECUTION_STATUS_FAILED)
        $rowClass = array('class' => 'error');
    elseif ($rowElement['Cron']['last_execution_status'] == Cron::EXECUTION_STATUS_WARNING)
        $rowClass = array('class' => 'warning');
    elseif ($rowElement['Cron']['last_execution_status'] == Cron::EXECUTION_STATUS_SUCCES)
        $rowClass = array('class' => 'success');
    else
        $rowClass = array('class' => 'info');

    echo $this->Html->tag('tr', null, $rowClass);

    if ($rowElement['Cron']['lock']){
        $verrou = $this->Html->link('<i class="fa fa-lock"></i>', array('action' => 'unlock', $rowElement['Cron']['id']), array('escape' => false, 'title' => 'Devérrouiller la tâche', 'style'=>'color:white'));
        $rowElement['Cron']['statusLibelle'] = $this->Html->tag('span', "$verrou Vérrouillée", array('class'=>'label label-important', 'title'=>"La tâche est vérrouillée, ce qui signifie qu'elle est en cours d'exécution ou dans un état bloqué suite à une erreur"));
    }
    echo $this->Html->tag('td', $rowElement['Cron']['statusLibelle'], array('style' => 'text-align:center;'));
    echo $this->Html->tag('td', $rowElement['Cron']['nom']);
    echo $this->Html->tag('td', $this->Time->format("d-m-Y à H:i:s", $rowElement['Cron']['next_execution_time']));
    echo $this->Html->tag('td', $rowElement['Cron']['durationLibelle']);
    if ($rowElement['Cron']['last_execution_start_time'] != null)
        echo $this->Html->tag('td', $this->Time->format("d-m-Y à H:i:s", $rowElement['Cron']['last_execution_start_time']));
    else
        echo $this->Html->tag('td', 'Jamais');
    echo $this->Html->tag('td', $rowElement['Cron']['activeLibelle']);
    echo $this->Html->tag('td', null, array('class' => 'actions'));

    echo $this->Html->tag('div', null, array('class' => 'btn-toolbar'));

    echo $this->Html->tag('div', null, array("class" => 'btn-group'));
    echo $this->Html->link($this->Html->tag('i', '', array("class" => "fa fa-info-circle fa-lg")), array('action'=>'view', $rowElement['Cron']['id']), array('class' => 'btn', 'title' => 'Voir les détails', 'escape' => false), false, false);
    if ($superadmin){
        echo $this->Html->link($this->Html->tag("i", "", array("class" => "fa fa-edit fa-lg")), '/crons/edit/' . $rowElement['Cron']['id'], array('class' => 'btn', 'title' => 'Modifier', 'escape' => false), false, false);
        echo $this->Html->link($this->Html->tag("i", "", array("class" => "fa fa-trash-o fa-lg")), '/crons/delete/' . $rowElement['Cron']['id'], array('class' => 'btn suppr_cron', 'title' => 'Supprimer', 'escape' => false), false, 'Vous confirmez vouloir supprimer cette tâche ?');
        echo $this->Html->tag('/div', null);
        echo $this->Html->tag('div', null, array("class" => 'btn-group'));
    }
    echo $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-clock-o fa-lg')), array('action'=>'planifier', $rowElement['Cron']['id']), array('class' => 'btn', 'title' => 'Planifier', 'escape' => false), false, false);
    echo $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-cog fa-lg')), array('action'=>'executer', $rowElement['Cron']['id']), array('class' => 'btn waiter', 'title' => 'Exécuter maintenant', 'escape' => false), false, false);
    echo $this->Html->tag('/div', null);

    echo $this->Html->tag('/div', null);

    echo $this->Html->tag('/td');
    echo $this->Html->tag('/tr');
}
echo $this->Html->tag('/table');

echo $this->Html->tag('div', null, array('style' => 'text-align:center; margin-top:10px;'));
if ($superadmin)
    echo $this->Html->link('<i class="fa fa-plus-circle fa-lg"></i> Nouvelle tâche', array('action'=>'add'), array('title'=>'Créer une nouvelle tâche planifiée', 'class'=>'btn', 'escape' => false));
echo $this->Html->link('<i class="fa fa-cogs fa-lg"></i> Exécuter toutes les tâches', array("action" => "runCrons"), array('id' => 'run-crons', 'class' => 'btn btn-primary waiter', 'escape' => false, 'title' => 'Exécuter toutes les tâches planifiées maintenant'));
echo $this->Html->tag('/div');