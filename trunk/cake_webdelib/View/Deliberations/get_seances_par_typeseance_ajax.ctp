<?php

if (!empty($seances)){
    echo $this->BsForm->select('Seance', $seances, array(
                        'class' => 'select2 selectmultiple',
                        'label' => 'Dates de séance',
                        'autocomplete' => 'off',
                        'multiple' => true));
    echo $this->Bs->scriptBlock('
                 $("#Seance").select2({
                     width: "100%",
                     allowClear: true,
                 });
    ');
 }