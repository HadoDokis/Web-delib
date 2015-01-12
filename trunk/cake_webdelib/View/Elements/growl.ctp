<?php
/** Bootstrap Growl
 * @link https://github.com/mouse0270/bootstrap-growl This is a simple pluging that turns standard Bootstrap alerts into "Growl-like" notifications.
 * 
 * 
 */
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
        case 'warning' :
            $type = 'warning';
            $options = array(
                'sticky' => 'true',
                'icon' => 'glyphicon glyphicon-warning-sign',
                'title' => '<strong>'.__('Attention').'</strong>'
            );
            break;
        case 'warning' :
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
        $params=$key . ': {';
        foreach ($setting as $key_param => $param) {
            $params.= $key_param.': \'' . $param . '\',';
        }
        echo substr($params,0,-1).'},';
    } else {
        echo $key . ': \'' . $setting . '\', ';
    }
}
echo 'type: \'' . $type . '\', ';
echo 'template: \'<div data-growl="container" class="alert" role="alert">'
. '<button type="button" class="close" data-growl="dismiss">'
        . '<span aria-hidden="true">×</span><span class="sr-only">Close</span></button>'
        . '<span data-growl="icon"></span>&nbsp;<span data-growl="title"></span><br />'
        . '<span data-growl="message"></span></div>\'';
echo '});';
$this->Html->scriptEnd();
