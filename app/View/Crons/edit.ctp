<style>
    h2 {
        margin-bottom: 30px;
    }
    div.date select, div.time select{
        width: auto;
    }
    label{
        text-align: left;
        width: auto;
        min-width: 200px;
        padding: 4px;
    }
    .checkbox input[type="checkbox"]{
        margin-top: 7px;
        margin-left: -12px;
    }
</style>
<script type="text/javascript">
    $(document).ready(function() {
        var plugin_ctrl_method = <?php echo json_encode($plugin_ctrl_method); ?>;

        function pluginChange() {
            $.each(plugin_ctrl_method, function(plugin, ctrls) {
                if (plugin == $("#CronPlugin option:selected").text()) {
                    $("#CronController").empty();
                    $.each(ctrls, function(ctrl, action) {
                        $("#CronController").append('<option value="' + ctrl + '">' + ctrl + '</option>');
                    });
                    return;
                }
            });
        }
        function controllerChange() {
            $.each(plugin_ctrl_method, function(plugin, ctrls) {
                if (plugin == $("#CronPlugin option:selected").text()) {
                    $.each(ctrls, function(ctrl, action) {
                        if (ctrl == $("#CronController option:selected").text()) {
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
        $("#CronController").change(controllerChange);

        $("#CronPlugin").val('<?php echo ucfirst($actual_plugin); ?>');
        pluginChange();

        $("#CronController").val('<?php echo ucfirst($actual_controller) . 'Controller'; ?>');
        controllerChange();

        $("#CronAction").val('<?php echo $actual_action; ?>');

    });
</script>
<?php
foreach ($plugin_ctrl_method as $plugin => $ctrls) {
    if ($plugin !== '')
        $plugins[$plugin] = $plugin;
    else {
        foreach ($ctrls as $controller => $method)
            $controllers[$controller] = $controller;
    }
}
echo $this->Html->tag('h2', __('Edition de la tâche planifiée', true) . ' : ' . $this->data['Cron']['nom']);
echo $this->Form->create(null, array('action' => "edit/"));
echo $this->Form->input('nom', array('label' => __('Nom de la tâche planifiée', true)));
echo $this->Form->input('description', array('label' => __('Description', true), 'type' => 'textarea'));
echo $this->Form->input('plugin', array('type' => 'select', 'empty' => true, 'options' => $plugins));
echo $this->Form->input('controller', array('type' => 'select', 'empty' => true, 'options' => $controllers));
echo $this->Form->input('action', array('type' => 'select', 'empty' => true));
echo $this->Form->input('params', array('label' => __('Paramètre(s) (séparateur: ",")', true)));
echo $this->Form->hidden('has_params', array('value' => '0'));

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
echo $this->Html->tag("div", null, array("class" => "btn-group btn-group-vertical", 'style' => 'clear: both; left: 210px;'));
echo $this->Html->link('<i class="icon-circle-arrow-left"></i> Annuler', array('action' => 'index'), array('class' => 'btn', 'escape' => false));
echo $this->Form->button('<i class="icon-ok"></i> Valider', array('type' => 'submit', 'id' => 'boutonValider', 'class' => 'btn btn-primary', 'escape' => false));
echo $this->Html->tag('/div', null);
echo $this->Form->end();
?>