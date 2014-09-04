<?php

echo $this->Bs->tag('h3', 'Projets de la séance du ' . $date_seance) .
 $this->Bs->table(array(
    array('title' => 'Num Delib'),
    array('title' => 'Libellé de l\'acte'),
    array('title' => 'Titre'),
    array('title' => 'Thème'),
    array('title' => 'Rapporteur'),
    array('title' => 'Actions')
        ), array('hover', 'striped'));
foreach ($projets as $projet) {
    echo $this->Bs->tableCells(array(
        $projet['Deliberation']['num_delib'],
        $projet['Deliberation']['objet_delib'],
        $projet['Deliberation']['titre'],
        $projet['Theme']['libelle'],
        $projet['Rapporteur']['nom'] . ' ' . $projet['Rapporteur']['prenom'],
        $this->Bs->div('btn-group') .
        ($pv_figes != 1 ?
                $this->Bs->btn(null, array('controller' => 'seances', 'action' => 'saisirDebat', $projet['Deliberation']['id'], $seance_id), array('type' => 'primary',
                    'icon' => 'glyphicon glyphicon-pencil',
                    'title' => 'Saisir les debats')) : '') .
        $this->Bs->btn(null, array('controller' => 'deliberations', 'action' => 'downloadDelib', $projet['Deliberation']['id']), array('type' => 'default',
            'icon' => 'glyphicon glyphicon-download',
            'title' => 'Télécharger la délibération au format PDF')) .
        $this->Bs->close()
    ));
}
echo $this->Bs->endTable() .
 $this->Bs->div('btn-group') .
 $this->Bs->btn('Figer les débats', array(
    'controller' => 'postseances',
    'action' => 'changeStatus',
    $seance_id), array('type' => 'primary',
    'class' => 'waiter',
    'disabled' => ($pv_figes ? 'disabled' : null),
    'icon' => 'glyphicon glyphicon glyphicon-ok',
    'escape' => false,
    'confirm' => 'Etes-vous sur de vouloir figer les débats ?',
    'name' => 'Clore',
    'title' => 'Figer les débats')) .
 $this->Bs->close() .
 $this->Bs->div('btn-group') .
 $this->Html2->btnCancel() .
 $this->Bs->close();
