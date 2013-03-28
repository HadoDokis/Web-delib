<style>
    .table{
        font-size: 0.9em;
    }
    .table tr td, .table tr th{
        vertical-align: middle;
        padding: 5px;
    }
    .table tr td.actions{
        min-width: 120px;
        padding: 0px;
        height: 100%;
        vertical-align: middle;
    }
    .table th{
        text-align: center;
        vertical-align: middle;
    }
    tr:hover th, tr th{
        background-color: whitesmoke;
    }
    .info{
        color: inherit;
        font-style: inherit;
    }
/*    .btn-primary{
        margin-bottom: 10px;
    }*/
    .btn-group>.btn{
        float: none;
    }
    .btn-toolbar{
        text-align: center;
    }
</style>
<script type="text/javascript">
    $(document).ready(function() {
        $('.suppr_cron').click(function(e) {
            if (!confirm("Vous confirmez vouloir supprimer cette tâche ?")) {
                e.preventDefault();
                return false;
            }
        });
    });
</script>
<?php
echo $this->Html->css('font-awesome.min');
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
    if ($rowElement['Cron']['last_execution_status'] == Cron::EXECUTION_STATUS_FAILED)
        $rowClass = array('class' => 'error');
    elseif ($rowElement['Cron']['last_execution_status'] == Cron::EXECUTION_STATUS_WARNING)
        $rowClass = array('class' => 'warning');
    elseif ($rowElement['Cron']['last_execution_status'] == Cron::EXECUTION_STATUS_SUCCES)
        $rowClass = array('class' => 'success');
    else
        $rowClass = array('class' => 'info');

    echo $this->Html->tag('tr', null, $rowClass);
    echo $this->Html->tag('td', $rowElement['Cron']['statusLibelle'], array('style' => 'text-align:right;'));
    echo $this->Html->tag('td', $rowElement['Cron']['nom']);
    echo $this->Html->tag('td', $this->Time->format("d-m-Y à H:i:s", $rowElement['Cron']['next_execution_time']));
    echo $this->Html->tag('td', $rowElement['Cron']['durationLibelle']);
    if ($rowElement['Cron']['last_execution_start_time'] != null)
        echo $this->Html->tag('td', $this->Time->format("d-m-Y à H:i:s", $rowElement['Cron']['last_execution_start_time']));
    else
        echo $this->Html->tag('td', "Jamais");
    echo $this->Html->tag('td', $rowElement['Cron']['activeLibelle']);
    echo $this->Html->tag('td', null, array('class' => 'actions'));

    echo $this->Html->tag('div', null, array('class' => 'btn-toolbar'));

    echo $this->Html->tag('div', null, array("class" => 'btn-group'));
    echo $this->Html->link($this->Html->tag("i", "", array("class" => "icon-info-sign icon-large")), '/crons/view/' . $rowElement['Cron']['id'], array('class' => 'btn', 'title' => 'Voir les détails', 'escape' => false), false, false);
//    echo $this->Html->link($this->Html->tag("i", "", array("class" => "icon-edit icon-large")), '/crons/edit/' . $rowElement['Cron']['id'], array('class' => 'btn', 'title' => 'Modifier', 'escape' => false), false, false);
//    echo $this->Html->link($this->Html->tag("i", "", array("class" => "icon-trash icon-large")), '/crons/delete/' . $rowElement['Cron']['id'], array('class' => 'btn suppr_cron', 'title' => 'Supprimer', 'escape' => false), false, false);
//    echo $this->Html->tag('/div', null);
//
//    echo $this->Html->tag('div', null, array("class" => 'btn-group'));
    echo $this->Html->link($this->Html->tag("i", "", array("class" => "icon-time icon-large")), '/crons/planifier/' . $rowElement['Cron']['id'], array('class' => 'btn', 'title' => 'Planifier', 'escape' => false), false, false);
    echo $this->Html->link($this->Html->tag("i", "", array("class" => "icon-cog icon-large")), '/crons/executer/' . $rowElement['Cron']['id'], array("class" => "btn", 'title' => 'Exécuter maintenant', 'escape' => false), false, false);
    echo $this->Html->tag('/div', null);

    echo $this->Html->tag('/div', null);

    echo $this->Html->tag('/td');
    echo $this->Html->tag('/tr');
}
echo $this->Html->tag('/table');
echo $this->Html->tag('div', null, array('id' => 'run_crons', 'style' => 'text-align:center; margin-top:10px;'));
echo $this->Html->link('<i class="icon-cogs icon-large"></i> Exécuter toutes les tâches', array("action" => "runCrons"), array('class' => 'btn btn-primary', 'escape' => false, 'title' => 'Exécuter toutes les tâches planifiées maintenant'));

echo $this->Html->tag('br');
//echo $this->Html->link('<i class=" icon-plus-sign icon-large"></i> Nouvelle tâche', array("action" => "add"), array('class' => 'btn', 'escape' => false, 'title' => 'Créer une nouvelle tâche planifiée'));
echo $this->Html->tag('/div');
//echo $this->element('indexPageNavigation');
?>