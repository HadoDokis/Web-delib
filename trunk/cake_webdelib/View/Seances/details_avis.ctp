<?php
$this->Html->addCrumb('Séance à traiter', array($this->request['controller'], 'action' => 'afficherProjets', $seance_id));
$this->Html->addCrumb( __('Séance du ') . $this->Time->i18nFormat($date_seance, '%d/%m/%Y à %k:%M'));

$attribute = array();
$attribute['attributes']['id'] = 'tableDetailAvis';
$attribute['attributes']['name'] = 'tableDetailAvis';

echo $this->Bs->tag('h3',  __('Détails des projets de la séance du ') .$this->Time->i18nFormat($date_seance, '%d/%m/%Y à %k:%M')) .
     $this->Bs->div('deliberations') .
     $this->Bs->table(
        array(
            array('title' => __('Résultat')),
            array('title' => __('Theme')),
            array('title' => __('Service emetteur')),
            array('title' => __('Rapporteur')),
            array('title' => __('Libellé de l\'acte')),
            array('title' => __('Titre')),
            array('title' => __('ID')),
            array('title' => __('Actions')),
        ), 
        array('striped'),
        $attribute
     );

     foreach ($deliberations as $deliberation){
          //cellule avis
          if ($deliberation['Deliberation']['avis'] === true)
              $cellDelibAvis = $this->Bs->icon('thumbs-up', array('2x'), array('title' => 'Avis favorable', 'class'=>'text-success text-center'));
          elseif ($deliberation['Deliberation']['avis'] === false)
              $cellDelibAvis = $this->Bs->icon('thumbs-down', array('2x'), array('title' => 'Avis défavorable', 'class'=>'text-danger text-center'));

          //cellule actions
          //@Deprecated : $this->Bs->lineAttributes(array('class'=>'actions'));
          $cellAction = $this->Html->link(null,
              array('controller' => 'seances', 'action' => 'saisirDebat', $deliberation['Deliberation']['id'], $seance_id),
              array(
                  'class' => 'link_debat',
                  'escape' => false,
                  'title' => 'Saisir les debats')) .
              $this->Html->link(null,
              array('controller' => 'seances', 'action' => 'donnerAvis', $deliberation['Deliberation']['id'], $seance_id),
              array(
                  'class' => 'link_donnerAvis',
                  'escape' => false,
                  'title' => 'Donner un avis')) .
              $this->Html->link(null,
              array('controller' => 'deliberations', 'action' => 'genereFusionToClient', $deliberation['Deliberation']['id']),
              array(
                  'class' => 'link_pdf delib_pdf',
                  'escape' => false,
                  'title' => 'Générer le PDF du projet ' . $deliberation['Deliberation']['objet']));
          
          echo $this->Bs->cell($cellDelibAvis) .
               $this->Bs->cell($deliberation['Theme']['libelle']) .
               $this->Bs->cell($deliberation['Service']['libelle']) .
               $this->Bs->cell($deliberation['Rapporteur']['nom'] . ' ' . $deliberation['Rapporteur']['prenom']) .
               $this->Bs->cell($deliberation['Deliberation']['objet_delib']) .
               $this->Bs->cell($deliberation['Deliberation']['titre']) .
               $this->Bs->cell($deliberation['Deliberation']['id']) .
               $this->Bs->cell($cellAction);
     }
     echo $this->Bs->endTable() . $this->Bs->close();

     //SPACER
     echo $this->Bs->div('spacer').$this->Bs->close().

     $this->Html2->btnCancel();
