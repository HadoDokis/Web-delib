<?php
$superadmin = false;
echo $this->Html->css('crons') .
$this->Bs->tag('h2', __('Liste des tâches automatiques', true)) .
$this->Bs->table(
array(
   array('title' => __('Etat', true)),
   array('title' => __('Nom', true)),
   array('title' => __('Date exécution prévue', true)),
   array('title' => __('Délai entre 2 exécutions', true)),
   array('title' => __('Date dernière exécution', true)),
   array('title' => __('Active', true)),
   array('title' => __('Actions', true))
), array('hover', 'striped'));

foreach ($this->data as $rownum => $rowElement) {

    if ($rowElement['Cron']['lock']) {
        $verrou = $this->Html->link('<i class="fa fa-lock"></i>', array(
            'action' => 'unlock', $rowElement['Cron']['id']), array(
                'escape' => false, 'title' => 'Devérrouiller la tâche', 
                'style' => 'color:white'));
        $rowElement['Cron']['statusLibelle'] = $this->Html->tag(
                'span', "$verrou Vérrouillée", array(
                    'class' => 'label label-warning', 
                    'title' => "La tâche est vérrouillée, ce qui signifie qu'elle est en cours d'exécution ou dans un état bloqué suite à une erreur"));
    }
    else
    {
        switch ($rowElement['Cron']['last_execution_status']) {
            case 'LOCKED':
                $rowElement['Cron']['statusLibelle'] = '<span class="label label-info" title="La tâche est vérrouillée, ce qui signifie qu\'elle est en cours d\'exécution ou dans un état bloqué suite à une erreur"><i class="fa fa-lock"></i> ' . __('Vérrouillée', true) . '</span>';
                break;
            case 'SUCCES':
                $rowElement['Cron']['statusLibelle'] = '<span class="label label-success" title="Opération exécutée avec succès"><i class="fa fa-check"></i> ' . __('Exécutée avec succès', true) . '</span>';
                break;
            case 'WARNING':
                $rowElement['Cron']['statusLibelle'] = '<span class="label label-warning" title="Avertissement(s) détecté(s) lors de l\'exécution, voir les détails de la tâche"><i class="fa fa-info"></i> ' . __('Exécutée, en alerte', true) . '</span>';
                break;
            case 'FAILED':
                $rowElement['Cron']['statusLibelle'] = '<span class="label label-danger" title="Erreur(s) détectée(s) lors de l\'exécution, voir les détails de la tâche"><i class="fa fa-warning"></i> ' . __('Non exécutée : erreur', true) . '</span>';
                break;
            default:
                $rowElement['Cron']['statusLibelle'] = '<span class="label label-default" title="La tâche n\'a jamais été exécutée">' . __('En attente', true) . '</span>';
        }
    }
   
    //cell date derniere execution
    if ($rowElement['Cron']['last_execution_start_time'] != null)
        $date_last_exec = $this->Time->format("d-m-Y à H:i:s", $rowElement['Cron']['last_execution_start_time']);
    else
        $date_last_exec = 'Jamais';
    
    //liste des actions
    $liste_bouton = $this->Bs->btn(null, array(
                        'controller' => 'crons',
                        'action' => 'view', 
                        $rowElement['Cron']['id']), 
                            array(
                                'type' => 'default', 
                                'icon' => 'fa fa-info-circle fa-lg', 
                                'title' => 'Voir les détails'));

    if ($superadmin) {

        $liste_bouton .= $this->Bs->btn(null, array(
                        'controller' => 'crons',
                        'action' => 'edit', 
                        $rowElement['Cron']['id']), 
                            array(
                                'type' => 'default', 
                                'icon' => 'fa fa-edit fa-lg', 
                                'title' => 'Modifier'));

        $liste_bouton .= $this->Bs->btn(null, array(
                        'controller' => 'crons',
                        'action' => 'delete', 
                        $rowElement['Cron']['id']), 
                            array(
                                'type' => 'default', 
                                'icon' => 'fa fa-trash-o fa-lg', 
                                'title' => 'Supprimer'),'Vous confirmez vouloir supprimer cette tâche automatique ?' );
    }

    $liste_bouton .= $this->Bs->btn(null, array(
                    'controller' => 'crons',
                    'action' => 'planifier', 
                    $rowElement['Cron']['id']), 
                        array(
                            'type' => 'primary', 
                            'icon' => 'fa fa-clock-o fa-lg', 
                            'title' => 'Planifier'));

    $liste_bouton .= $this->Bs->btn(null, array(
                'controller' => 'crons',
                'action' => 'executer', 
                $rowElement['Cron']['id']), 
                    array(
                        'type' => 'success', 
                        'icon' => 'fa fa-cog fa-lg', 
                        'title' => 'Exécuter maintenant'));
   
    $liste_bouton = $this->Bs->div('btn-group') . $liste_bouton . $this->Bs->close();
    
    echo $this->Bs->tableCells(array(
        $rowElement['Cron']['statusLibelle'],
        $rowElement['Cron']['nom'],
        $this->Time->format("d-m-Y à H:i", $rowElement['Cron']['next_execution_time']),
        $rowElement['Cron']['durationLibelle'],
        $date_last_exec,
        $rowElement['Cron']['activeLibelle'],
        $liste_bouton
    ));
}

echo $this->Bs->endTable();


echo $this->Bs->div('text-center');
if ($superadmin)
echo $this->Bs->btn('Nouvelle tâche', array('controller' => 'crons', 'action' => 'add'), array('type' => 'default', 'icon' => 'fa fa-plus-circle fa-lg', 'title' => 'Créer une nouvelle tâche planifiée'));
        
echo $this->Bs->btn('Exécuter toutes les tâches', array('controller' => 'crons', 'action' => 'runCrons'), array('type' => 'default', 'icon' => 'fa fa-cogs fa-lg', 'title' => 'Exécuter toutes les tâches planifiées maintenant'));
echo $this->Bs->close();