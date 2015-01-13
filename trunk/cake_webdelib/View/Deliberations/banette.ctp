<?php
    //echo $this->Html->tag('div', null, array('class' => 'ouvrable', 'id' => $titreVue));
    //echo $this->Html->tag('h2', "$titreVue $nb");

echo $this->Bs->div('panel panel-default');
echo $this->Bs->div('panel-heading', 
        $this->Bs->row().
        $this->Bs->col('xs8').
        $this->Bs->tag('h4', $titreVue . ' (' . $nbProjets .' '. ($nbProjets > 1 ?'projets':'projet').')'
                ).
        $this->Bs->close().
        $this->Bs->col('xs4').
        $this->Bs->tag('p', $this->Html->link(__('Voir le contenu de la banette'), 
                array('controller'=>$this->request['controller'],'action'=>$this->request['action'])
                ), array('class'=>'text-right')).
        $this->Bs->close(2)
        );

echo $this->element('9cases',array('projets'=>$this->data)); 
echo $this->Bs->close();


//                if (in_array('generer', $deliberation['Actions'])) {
//                    if (empty($deliberation['Deliberation']['delib_pdf']))
//                        echo $this->Html->link('',
//                            array('controller' => 'deliberations', 'action' => 'genereFusionToClient', $deliberation['Deliberation']['id']),
//                            array(
//                                'class' => 'link_pdf delib_pdf',
//                                'escape' => false,
//                                'title' => 'Générer le document PDF du projet ' . $deliberation['Deliberation']['objet']));
//                    else
//                        echo $this->Html->link('',
//                            array('controller' => 'deliberations', 'action' => 'downloadDelib', $deliberation['Deliberation']['id']),
//                            array('class' => 'link_pdf delib_pdf',
//                                'title' => 'Visionner le document PDF du projet ' . $deliberation['Deliberation']['objet'],
//                                'escape' => false),
//                            false);
//                }

//if (!empty($listeLiens)) {
//    echo '<div role="toolbar" class="btn-toolbar" style="text-align: center;"><div class="btn-group">';
//    if (in_array('add', $listeLiens)) {
//        echo $this->Html->link('<i class=" fa fa-plus"></i> Ajouter un projet',
//            array("action" => "add"),
//            array('class' => 'btn btn-primary',
//                'escape' => false,
//                'title' => 'Créer un nouveau projet',
//                'style' => 'margin-top: 10px;'));
//    }
//    if (in_array('mesProjetsRecherche', $listeLiens)) {
//        echo '<ul class="actions">';
//        echo '<li>' . $this->Html->link('Nouvelle recherche', '/deliberations/mesProjetsRecherche', array('class' => 'btn', 'escape' => false, 'alt' => 'Nouvelle recherche parmi mes projets', 'title' => 'Nouvelle recherche parmi mes projets')) . '</li>';
//        echo '</ul>';
//    }
//    if (in_array('tousLesProjetsRecherche', $listeLiens)) {
//        echo '<ul class="actions">';
//        echo '<li>' . $this->Html->link('Nouvelle recherche', '/deliberations/tousLesProjetsRecherche', array('class' => 'btn', 'escape' => false, 'alt' => 'Nouvelle recherche parmi tous les projets', 'title' => 'Nouvelle recherche parmi tous les projets')) . '</li>';
//        echo '</ul>';
//    }
//    echo "</div></div>";
//}