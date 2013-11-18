<?php
    if (!empty($seances))
        echo $this->Form->input('Seance', array(
            'options' => $seances,
            'label'   => 'Date de séances',
            'size'     => 10,
            'multiple' => true,
            'id' => 'SeanceSeance',
            'name' => 'data[Seance][Seance]'));
?>
