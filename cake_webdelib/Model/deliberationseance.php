<?php
class Deliberationseance extends AppModel {
    var $name = 'Deliberationseance';
    var $useTable = 'deliberations_seances';
    var $belongsTo = array('Deliberation', 'Seance');
}
?>
