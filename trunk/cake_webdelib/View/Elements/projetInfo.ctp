<?php
echo    $this->Bs->div('well').
            $this->Bs->row().
                $this->Bs->col('xs6')
                    .'<b>Service émetteur :</b> ' . $projet['Service']['libelle'] . $this->Bs->tag('br /')
                    .'<b>Rédacteur :</b> ' . $this->Html->link($projet['Redacteur']['prenom'] . ' ' . $projet['Redacteur']['nom'], array('controller'=> 'users', 'action'=>'view', $projet['Redacteur']['id']))
                .$this->Bs->close().
                $this->Bs->col('xs6')
                    .'<b>Date création :</b> ' . $this->Time->i18nFormat($projet['Deliberation']['created'], '%d/%m/%Y à %k:%M') . $this->Bs->tag('br /')
                    .'<b>Date modification :</b> ' . $this->Time->i18nFormat($projet['Deliberation']['modified'], '%d/%m/%Y à %k:%M') . $this->Bs->tag('br /')
                    
                .$this->Bs->close().
                $this->Bs->close().
        $this->Bs->close();

echo $this->Bs->row().  
        $this->Bs->col('xs6').'<b>Libellé :</b> ' . $projet['Deliberation']['objet'].$this->Bs->tag('br /')
        .'<b>Titre :</b> ' . $projet['Deliberation']['objet'].$this->Bs->tag('br /')
        .'<b>Rapporteur :</b> ' . $this->Html->link($projet['Rapporteur']['prenom'] . ' ' . $projet['Rapporteur']['nom'], array('controller'=>'acteurs', 'action'=>'view' , $projet['Rapporteur']['id']))
        .$this->Bs->close().
        $this->Bs->col('xs6')
        .'<b>Thème :</b> ' . $projet['Theme']['libelle'].$this->Bs->tag('br /')
        .'<b>Num Pref :</b> ' . $projet['Deliberation']['num_pref']
.$this->Bs->close(2);

echo $this->Bs->row().
        $this->Bs->col('xs2').'<b>Date Séance :</b> '
        .$this->Bs->close().
        $this->Bs->col('xs10');
if (isset($projet['Seance'][0])) {
    foreach ($projet['Seance'] as $seance) {
        echo $seance['Typeseance']['libelle'] . " : ";
        echo $this->Time->i18nFormat($seance['date'], '%d/%m/%Y à %k:%M') . $this->Bs->tag('br /');
    }
}
echo $this->Bs->close(2);

echo $this->Html->tag('br /');

$sLis='';
if (!empty($projet['Deliberation']['texte_projet_name']))
$sLis=$this->Bs->tag('li',
    $this->element('viewTexte', array('type' => 'projet', 'delib' => $projet['Deliberation']))
    , array('class'=>'list-group-item'));

if (!empty($projet['Deliberation']['texte_synthese_name']))
$sLis.=$this->Bs->tag('li', 
    $this->element('viewTexte', array('type' => 'synthese', 'delib' => $projet['Deliberation']))
    , array('class'=>'list-group-item'));

if (empty($projet['Multidelib'])){
if (!empty($projet['Deliberation']['deliberation_name']))
$sLis.=$this->Bs->tag('li', 
    $this->element('viewTexte', array('type' => 'deliberation', 'delib' => $projet['Deliberation']))
    , array('class'=>'list-group-item'));
}       
if(!empty($sLis)){
    echo $this->Bs->row()
    .$this->Bs->col('xs12')
    .$this->Bs->tag('ul',  $sLis, array('class'=>'list-group'))
    .$this->Bs->close(2);
}

if(!empty($tab_anterieure)){
    $sLis='';
    $i=1;
    foreach ($tab_anterieure as $key=>$anterieure) {
        $sLis.=$this->Bs->tag('li', $this->Bs->tag('span', $i, array('class' => 'badge'))
                . $this->Html->link(__('Version antérieur du '). $anterieure['date_version'], $anterieure['lien'])
                , array('class' => 'list-group-item list-group-item-danger'));
        $i++;
    }

    echo $this->Bs->row()
    .$this->Bs->col('xs12')
    .$this->Bs->tag('ul',  $sLis, array('class'=>'list-group'))
    .$this->Bs->close(2);
}
