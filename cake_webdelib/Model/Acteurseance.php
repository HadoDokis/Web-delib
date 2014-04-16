<?php

class Acteurseance extends AppModel {

    var $name = 'Acteurseance';
    var $useTable = 'acteurs_seances';
    var $belongsTo = array('Acteur', 'Seance');

}
