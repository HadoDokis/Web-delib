<?php
$this->Html->scriptStart(array('inline' => false));

if(empty($settings))
    $settings=array();

echo '$.growl({';
if (!empty($type)) {
    switch ($type) {
        case 'info' :
        case 'important' :
            //FIX
            $type = 'info';
            $options = array(
                'icon' => 'glyphicon glyphicon-warning-sign',
                'glue' => 'before',
                'title' =>'<strong>'.__('Important').'</strong>'
            );
            break;
        case 'success' :
            //FIX
            $title = __('Important');
            $options = array(
                'icon' => 'glyphicon glyphicon-warning-sign',
                'glue' => 'before',
                'title' => '<strong>'.__('Erreur').'</strong>'
            );
            break;
        case 'error' :
        case 'danger' :
            $type = 'danger';
            $options = array(
                'sticky' => 'true',
                'icon' => 'glyphicon glyphicon-warning-sign',
                'title' => '<strong>'.__('Erreur').'</strong>'
            );
            break;
        default:
            $type = 'info';
            $options = array(
                'icon' => 'glyphicon glyphicon-warning-sign',
                'title' => '<strong>'.__('Information').'</strong>'
            );
    }
} else {
    $type = 'info';
    $options = array(
        'icon' => 'glyphicon glyphicon-warning-sign',
        'title' => '<strong>'.__('Information').'</strong>'
    );
}

foreach ($options as $key => $option) {
    echo $key . ': \'' . $option . '\',';
}
echo 'message: \'' . h($message) . '\'';
echo '},{';
$settings += array('offset'=> array('x'=>80,'y'=>100));
foreach ($settings as $key => $setting) {
    if (is_array($setting)) {
        echo $key . ': {';
        foreach ($setting as $key_param => $param) {
            echo $key_param.': \'' . $param . '\',';
        }
        echo '},';
    } else {
        echo $key . ': \'' . $setting . '\',';
    }
}
echo 'type: \'' . $type . '\' });';

$this->Html->scriptEnd();