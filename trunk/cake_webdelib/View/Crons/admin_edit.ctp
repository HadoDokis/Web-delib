<?php
$plugins = array();
$this->Html->addCrumb(__('Planification de la tâche'), array('controller' => 'crons', 'action' => 'index'));

foreach ($plugin_model_method as $plugin => $models) {
    if ($plugin !== '')
        $plugins[$plugin] = $plugin;
    else {
        foreach ($models as $model => $method)
            $modelList[$model] = $model;
    }
}

echo $this->Html->tag('h3', __('Edition de la tâche planifiée', true) . ' : ' . $this->data['Cron']['nom']) .
$this->Form->create(null, array(
    'plugin' => 'Crons', 
    'controller' => 'crons', 
    'action' => 'edit'));

$panel['left'][] ='<b>'.__('Nom de la tâche planifiée', true) .' : </b>';
$panel['left'][] ='<b>'.__('Description', true).' : </b>';
$panel['left'][] ='<b>'.__('Plugin', true).' : </b>';
$panel['left'][] ='<b>'.__('Model', true) .' : </b>';
$panel['left'][] ='<b>'.__('Action', true).' : </b>';
$panel['left'][] ='<b>'.__('Paramètre(s) (séparateur: ",")', true).' : </b>';
$panel['left'][] ='<b>'.__('Date de la prochaine exécution', true).' : </b>';
$panel['left'][] ='<b>'.__('Heure de la prochaine exécution', true).' : </b>';
$panel['left'][] ='<b>'.__('Délais entre deux exécutions', true).' : </b>';
$panel['left'][] ='<b>'.__('Activation', true).' : </b>';

$panel['right'][] = $this->Form->input('nom', array('label' => false ));
$panel['right'][] = $this->Form->input('description', array('label' => false));
$panel['right'][] = $this->Form->input('plugin', array('label' => false,'type' => 'select', 'empty' => true, 'options' => $plugins));
$panel['right'][] = $this->Form->input('model', array('label' => false, 'type' => 'select', 'empty' => true, 'options' => $modelList));
$panel['right'][] = $this->Form->input('action', array('label' => false, 'type' => 'select', 'empty' => true));
$panel['right'][] = $this->Form->input('params', array('label' => false));
$panel['right'][] = $this->Form->input('next_execution_date', array('label' => false,
                    'type' => 'date',
                    'dateFormat' => 'DMY',
                    'minYear' => date('Y') - 0,
                    'maxYear' => date('Y') + 2,
                    'monthNames' => false,
                    'empty' => true
                ));
$panel['right'][] = $this->Form->input('next_execution_heure', array('label' => false, 'type' => 'time', 'timeFormat' => '24', 'interval' => 15));
$panel['right'][] = $this->DurationPicker->picker('Cron.execution_duration', array('label' => false, 'empty' => true, 'value' => $this->data['Cron']['execution_duration']));
$panel['right'][] = $this->Form->input('active', array('label' => false));



echo $this->Bs->panel('Edition de la tâche');
foreach($panel['left'] as $i => $temp)
{
   echo $this->Bs->row() .
        $this->Bs->col('xs3') .
        $this->Bs->close() .
        $this->Bs->col('xs3').$panel['left'][$i] .
        $this->Bs->close() .
        $this->Bs->col('xs3').$panel['right'][$i] .
        $this->Bs->close(2) .
        $this->Bs->div('spacer').$this->Bs->close();
}
unset($panel);
echo $this->Bs->endPanel() .
$this->Html2->btnSaveCancel('', $previous, 'Valider', 'Valider') .
$this->Form->hidden('id') .
$this->Form->hidden('has_params', array('value' => '0')) .
$this->Form->end();
?>




<script type="text/javascript">
    $(document).ready(function() {
        var plugin_ctrl_method = <?php echo json_encode($plugin_model_method); ?>;

        function pluginChange() {
            $.each(plugin_ctrl_method, function(plugin, ctrls) {
                if (plugin == $("#CronPlugin option:selected").text()) {
                    $("#CronModel").empty();
                    $.each(ctrls, function(ctrl, action) {
                        $("#CronModel").append('<option value="' + ctrl + '">' + ctrl + '</option>');
                    });
                    return;
                }
            });
        }
        function modelChange() {
            $.each(plugin_ctrl_method, function(plugin, ctrls) {
                if (plugin == $("#CronPlugin option:selected").text()) {
                    $.each(ctrls, function(ctrl, action) {
                        if (ctrl == $("#CronModel option:selected").text()) {
                            $("#CronAction").empty();
                            $.each(action, function(i, method) {
                                $("#CronAction").append('<option value="' + method + '">' + method + '</option>');
                            });
                            return;
                        }
                    });
                }
            });
        }

        $("#CronParams").change(function(e) {
            if ($(this).val() !== '')
                $("#CronHasParams").val('1');
            else
                $("#CronHasParams").val('0');
        });

        $("#CronPlugin").change(pluginChange);
        $("#CronModel").change(modelChange);

        $("#CronPlugin").val('<?php echo ucfirst($actual_plugin); ?>');
        pluginChange();

        $("#CronModel").val('<?php echo ucfirst($actual_model); ?>');
        modelChange();

        $("#CronAction").val('<?php echo $actual_action; ?>');

    });
</script>