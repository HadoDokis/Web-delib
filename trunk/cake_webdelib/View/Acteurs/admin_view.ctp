<?php
$this->Html->addCrumb('Fiche acteur');

foreach ($acteur['Service'] as $service){
   $services[] = $service['name'];
}

$panel_left = '<b>Identité : </b>'.$acteur['Acteur']['salutation'] . ' ' . $acteur['Acteur']['prenom'] . ' ' . $acteur['Acteur']['nom'] . 
              ($acteur['Acteur']['titre'] ? ', ' : '') . $acteur['Acteur']['titre'].'<br>' .
              '<b>Adresse postale : </b>' . $acteur['Acteur']['adresse1']. '<br>' .
              ($acteur['Acteur']['adresse2'] ? $acteur['Acteur']['adresse2'].'<br>' : '' ) .
              ($acteur['Acteur']['cp'] ? $acteur['Acteur']['cp'].'<br>' : '' ) .
              ($acteur['Acteur']['ville'] ? $acteur['Acteur']['ville'].'<br>' : '') .
              '<b>Contacts : </b><br>' .
              'Téléphone fixe : ' . $acteur['Acteur']['telfixe'].'<br>' .
              'Téléphone mobile : ' . $acteur['Acteur']['telmobile'].'<br>' .
              'Adresse email : ' . $acteur['Acteur']['email'].'<br>';

$panel_right = '<b>Type : </b>' . $acteur['Typeacteur']['nom'] . '<br>' .
               ($acteur['Typeacteur']['elu'] ? '<b>Numéro d\'ordre dans le conseil : </b>'. 
               $acteur['Acteur']['position'] . '<br>' . 
               '<b>Délégations : </b>' . (!empty($service) ? implode(',', $services) : '').'<br>' . 
               '<b>Date Naissance : </b>' . $acteur['Acteur']['date_naissance'] . '<br>' :  '');

$panel_bottom = $this->Bs->row() .
                $this->Bs->col('xs4').'<b>Note : </b>' . $acteur['Acteur']['note'] . '<br>' . $this->Bs->close() .
                $this->Bs->col('xs4').'<b>Date de création : </b>' . $acteur['Acteur']['created'] .'<br>' . $this->Bs->close() .
                $this->Bs->col('xs4').'<b>Date de modification : </b>' . $acteur['Acteur']['modified'] . '<br>' . $this->Bs->close(2);

echo $this->Bs->panel('Fiche acteur') .
        $this->Bs->row() .
        $this->Bs->col('xs6').$panel_left .
        $this->Bs->close() .
        $this->Bs->col('xs6').$panel_right .
        $this->Bs->close(2) .
        $this->Bs->div('spacer').$this->Bs->close() .
        $this->Bs->div('spacer').$this->Bs->close() .
        $panel_bottom .
        $this->Bs->div('spacer').$this->Bs->close() .
     $this->Bs->endPanel() .
     $this->Bs->row().
     $this->Bs->col('md4 of5') .
     $this->Bs->div('btn-group', null,array('id'=>"actions_fiche" )) .
     $this->Html2->btnCancel() .
     $this->Bs->btn('Modifier', array('controller' => 'acteurs', 'action' => 'edit', $acteur['Acteur']['id']), array('type' => 'primary', 'icon' => 'glyphicon glyphicon-edit', 'title' => 'Modifier')) .
     $this->Bs->close(3);
?>






