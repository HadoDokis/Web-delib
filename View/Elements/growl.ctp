<?php
$pos = @strripos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6');
if ($pos === false) {
    echo '<script type="text/javascript">';
    if (!empty($type)) {
        switch ($type) {
            case 'important' :
                echo 'jQuery.jGrowl("<div class=\'important\'>' . $message . '</div>", { header: "<strong>Important :</strong>", glue: "before" })';
                break;
            case 'erreur' :
            case 'error' :
                echo 'jQuery.jGrowl("<div class=\'error_message\'>' . $message . '</div>", { header: "<strong>Erreur :</strong>", sticky: true })';
                break;
            default:
                echo 'jQuery.jGrowl("<div class=\'' . $type . '\'>' . $message . '</div>", { header: "' . $type . ' :" })';
        }
    } else {
        echo 'jQuery.jGrowl("<div class=\'info\'>' . $message . '</div>", {})';
    }
    echo '</script>';
} else {
    echo $this->Html->tag('div', null, array('class' => 'alert alert-'.$type));
    echo $this->Html->tag('button', '&times;', array('class' => 'close', 'type' => 'button', 'data-dismiss' => 'alert'));
    echo $this->Html->tag('strong', "$type!");
    echo $message;
    echo $this->Html->tag('/div');
}