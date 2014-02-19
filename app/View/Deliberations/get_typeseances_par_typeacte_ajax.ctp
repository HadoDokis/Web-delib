<?php
    if (!empty($typeseances))
        echo $this->Form->input('Typeseance', array('options'  => $typeseances, 
                                                    'id'       => 'TypeseanceTypeseance',
                                                    'name'     => 'data[Typeseance][Typeseance]',
                                                    'size'     => 10,
                                                    'label'    => 'Types de séance',
                                                    'onchange' => "updateDatesSeances(this);",
                                                    'multiple' => true));
?>
