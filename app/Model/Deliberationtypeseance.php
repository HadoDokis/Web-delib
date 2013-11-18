<?php
class Deliberationtypeseance extends AppModel {
    var $name = 'Deliberationtypeseance';
    var $useTable = 'deliberations_typeseances';
    var $belongsTo = array('Deliberation', 'Typeseance');
}
?>
