<?php 
$this->Html->addCrumb('Séance à traiter', array('controller'=>'seances', 'action'=>'index'));

$this->Html->addCrumb(__('Vote de la séance'));

echo $this->Bs->tag('h3', __('Vote de la séance du') .' '. $this->Time->i18nFormat($date_seance, '%d %B %Y à %k h %M'));

echo $this->Bs->table(array(
    array('title' => 'Ordre'),
    array('title' => 'Id.'),
    array('title' => 'Etat'),
    array('title' => 'Thème'), 
    array('title' => 'Service émetteur'),
    array('title' => 'Rapporteur'),
    array('title' => 'Président'),
    array('title' => 'Libellé de l\'acte'),
    array('title' => 'Titre'),
    array('title' => 'N° Délibération'),
    array('title' => 'Résultat'),
    array('title' => 'Actions'),
        ), array('hover', 'striped'));

foreach ($deliberations as $deliberation) {
   echo $this->Bs->cell($deliberation['Deliberationseance']['position']);
   echo $this->Bs->cell($deliberation['Deliberation']['id']); 
   
   $image='';
   $resultat='';
   if ($deliberation['Deliberation']['etat'] == 2) {
        $image=$this->Bs->icon('unlock-alt', array('2x'), array('title' => 'Projet validé', 'class'=>'text-success text-center'));
    } elseif ($deliberation['Deliberation']['etat'] < 2) {
        $image=$this->Bs->icon('ban', array('2x'), array('title' => 'Projet en cours d\'élaboration', 'class'=>'text-danger text-center'));
    } elseif ($deliberation['Deliberation']['etat'] > 2) {
        $image=$this->Bs->icon('lock', array('2x'), array('title' => 'Deliberation votée', 'class'=>'text-center'));

        if ($deliberation['Deliberation']['etat'] != 4) {
            $resultat = $this->Bs->icon('thumbs-up', array('2x'), array('title' => 'Adopté', 'class'=>'text-success text-center'));
        } else {
            $resultat = $this->Bs->icon('thumbs-down', array('2x'), array('title' => 'Non adopté', 'class'=>'text-danger text-center'));
        }
    }
                
    echo $this->Bs->cell($image); 
    
    echo $this->Bs->cell($deliberation['Theme']['libelle']);
    echo $this->Bs->cell($deliberation['Service']['libelle']);
    echo $this->Bs->cell($deliberation['Rapporteur']['nom'] . ' ' . $deliberation['Rapporteur']['prenom']);
    echo $this->Bs->cell($deliberation['President']['nom'] . ' ' . $deliberation['President']['prenom']);
    echo $this->Bs->cell($deliberation['Deliberation']['objet_delib']);
    echo $this->Bs->cell($deliberation['Deliberation']['titre']);
    
   
     
    echo $this->Bs->cell(!empty($deliberation['Deliberation']['num_delib'])?$deliberation['Deliberation']['num_delib']:'');
    
     echo $this->Bs->cell($resultat); 
     $actions=$this->Bs->div('btn-group-vertical');
     
     $actions.= $this->Bs->btn($this->Bs->icon('comments-o',array('lg')),array('controller' => 'seances', 'action' => 'saisirDebat', $deliberation['Deliberation']['id'], $seance_id), array(
            'type' => 'default',
            'escape' => false,
            'disabled' => $deliberation['Deliberation']['etat'] > 2 ?null:'disabled',
            'title' => __('Saisir les debats du projet :') .' '. $deliberation['Deliberation']['objet_delib'],
        ));
     $actions.= $this->Bs->btn($this->Bs->icon('gavel',array('lg')),array('controller' => 'seances', 'action' => 'voter', $deliberation['Deliberation']['id'], $seance_id), array(
            'type' => 'primary',
            'escape' => false,
            'disabled' => $seance['Typeseance']['action'] < 2 ?null:'disabled',
            'title' => __('Voter le projet :') .' '. $deliberation['Deliberation']['objet_delib'],
        ));
     $actions.= $this->Bs->btn('<span class="fa-stack fa-1x">'.$this->Bs->icon('gavel',array('stack-1x')).$this->Bs->icon('ban', array('stack-2x'), array('class'=>'text-danger')).'</span>', 
             array('controller' => 'seances', 
                 'action' => 'resetVote', $seance_id, $deliberation['Deliberation']['id']), array(
            'type' => 'default',
            'escape' => false,
            'confirm'=> __('Supprimer le vote pour le projet :') .' '. $deliberation['Deliberation']['objet_delib'].' ?',
            'disabled' => $seance['Typeseance']['action'] < 2 ?null:'disabled',
            'title' => __('Supprimer le vote pour le projet :') .' '. $deliberation['Deliberation']['objet_delib'],
        ));
     /*
        
             $actions.= $this->Html->link(null,
                array('controller' => 'seances', 'action' => 'voter', $deliberation['Deliberation']['id'], $seance_id),
                array(
                    'class' => 'link_voter',
                    'title' => 'Voter les projets',
                    'escape' => false
                )
            );
        */
        $actions.=$this->Bs->btn('Générer', 
                        array('controller' => 'deliberations', 'action' => 'genereFusionToClient', $deliberation['Deliberation']['id']), 
                        array('type' => 'default', 
                            'icon' => 'glyphicon glyphicon-cog', 
                            'title' => 'Générer le document du projet ' . $deliberation['Deliberation']['objet_delib']
        ));
        
         /*$actions.= $this->Html->link(null,
            array('controller' => 'deliberations', 'action' => 'genereFusionToClient', $deliberation['Deliberation']['id']),
            array(
                'class' => 'link_pdf delib_pdf',
                'escape' => false,
                'title' => 'Générer le document PDF'));*/
    $actions.= $this->Bs->close();
     
    echo $this->Bs->cell($actions);
}
echo $this->Bs->endTable();
echo $this->Html2->btnCancel();