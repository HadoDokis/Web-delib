<?php

echo $this->Bs->tag('h3', 'Post-séances') .
 $this->Bs->table(array(array('title' => 'Type de séance'),
    array('title' => 'Date de la séance'),
    array('title' => 'Débats'),
    array('title' => 'Transmitions Tdt'),
    array('title' => 'Finalisation'),
    array('title' => 'Actions')
        ), array('hover', 'striped'));
foreach ($seances as $seance) {
    echo $this->Bs->tableCells(array(
        $seance['Typeseance']['libelle'],
        $seance['Seance']['date'],
        $this->Bs->div('btn-group') .
        $this->Bs->btn(null, array('controller' => 'postseances', 'action' => 'afficherProjets', $seance['Seance']['id']), 
                array('type' => 'default', 
                    'icon' => 'glyphicon glyphicon-pencil', 
                    'title' => 'Editer les débats')) .
        $this->Bs->close()
        ,
        $this->Bs->div('btn-group') .
        (($use_tdt) ?
                $this->Bs->btn(null, array('controller' => 'deliberations', 'action' => 'toSend', $seance['Seance']['id']), 
                        array(
                            'type' => 'primary', 
                            'icon' => 'glyphicon glyphicon-send', 
                            'title' => 'Envoie au TdT')) .
                $this->Bs->btn(null, array('controller' => 'deliberations', 'action' => 'transmit', $seance['Seance']['id']), 
                        array(
                            'type' => 'info', 
                            'icon' => 'glyphicon glyphicon-transfer', 
                            'title' => 'délibérations envoyees au TdT')) 
        :'')
        ,
        $this->Bs->close().
        $this->Bs->div('btn-group') .
        ((in_array('ged', $seance['Seance']['Actions'])) ?
                $this->Bs->btn(__('Export Ged','btn'), array('controller' => 'postseances', 'action' => 'sendToGed', $seance['Seance']['id']), 
                        array(
                            'type' => 'primary', 
                            'icon' => 'glyphicon glyphicon-export', 
                            'title' => 'Envoie la seance a la GED'), 'Envoyer les documents à la GED ?') 
        :'').$this->Bs->close()
        ,
         ((($seance['Seance']['pv_figes'] == 1) && ($format == 0)) ?
                //////////////////////
                $this->Bs->div('btn-group') .
                $this->Bs->btn(__('Télécharger','btn').' <span class="caret"></span>', 
                        array('controller' => 'postseances', 'action' => 'afficherProjets'), 
                        array('type' => 'default', 
                            'icon' => 'glyphicon glyphicon-download', 
                            'escape'=>false,'class'=>'dropdown-toggle', 
                            'data-toggle'=>'dropdown')).
                $this->Bs->nestedList(array(
                $this->Bs->link('PV sommaire', array('controller' => 'postseances', 'action' => 'downloadPV', $seance['Seance']['id'] , 'sommaire'), 
                    array(
                            'title' => 'Télécharger le pv sommaire pour la séance du ' . $seance['Seance']['date'],
                          )),
                $this->Bs->link('PV complet', array('controller' => 'postseances', 'action' => 'downloadPV', $seance['Seance']['id'] , 'complet'), 
                    array(
                    'title' => 'Télécharger le pv complet pour la séance du ' . $seance['Seance']['date']
                    ))
                )
                , array('class'=>'dropdown-menu','role'=>'menu')).
                $this->Bs->close():
                //////////////////////
                $this->Bs->div('btn-group') .
                $this->Bs->btn(__('Générer','btn').' <span class="caret"></span>', 
                        array('controller' => 'postseances', 'action' => 'afficherProjets'), 
                        array('type' => 'default', 
                            'icon' => 'glyphicon glyphicon-cog', 
                            'escape'=>false,'class'=>'dropdown-toggle', 
                            'data-toggle'=>'dropdown')).
                $this->Bs->nestedList(array(
                $this->Bs->link('PV sommaire', array('controller' => 'postseances', 'action' => 'genereFusionToClient', $seance['Seance']['id'] , 'pvsommaire'), 
                    array(
                            'title' => 'Génération du pv sommaire pour la séance du ' . $seance['Seance']['date'],
                            'class' => 'waiter',
                            'data-modal' => 'Génération du PV sommaire en cours')),
                $this->Bs->link('PV complet', array('controller' => 'postseances', 'action' => 'genereFusionToClient', $seance['Seance']['id'] , 'pvdetaille'), 
                    array(
                    'title' => 'Génération du pv complet pour la séance du ' . $seance['Seance']['date'],
                    'class' => 'waiter',
                    'data-modal' => 'Génération du PV complet en cours'))
                )
                , array('class'=>'dropdown-menu','role'=>'menu')).
                $this->Bs->close()
                //////////////////////////////
        )
    ));
}
echo $this->Bs->endTable();
