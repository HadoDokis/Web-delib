<?php

if (!empty($seances)){
    echo $this->BsForm->select('Deliberation.Deliberationseance', $seances, array(
                        'options' => $seances,
                        'class' => 'select2 selectmultiple',
                        'label' => 'Dates de séance',
                        'selected' => isset($seances_selected) ? $seances_selected : '',
                        'multiple' => true));
    echo $this->Bs->scriptBlock('
                 $("#DeliberationDeliberationseance").select2({
                     width: "100%",
                     allowClear: true,
                 });
    ');
 }