<?php echo $this->Html->script('utils.js'); 
$this->Html->addCrumb('Séances à traiter', array('controller'=>'seances','action'=>'listerFuturesSeances'));
$this->Html->addCrumb('Signature des délibérations');

echo $this->element('filtre');
$titles=array();
if (!empty($seance_id)){
    echo $this->Bs->tag('h3', 'Signature des délibérations');
    echo $this->BsForm->create('Deliberation', array(
        'url' => array('controller' => 'deliberations', 'action' => 'sendToParapheur', $seance_id),
        'type' => 'file'
    ));
    $titles[]=array('title' => $this->BsForm->checkbox('masterCheckbox', array(
        'label' => '',
        'inline'=>true
     )));
} 
else { 
    echo $this->Bs->tag('h3', 'Délibérations signées');
}
    
$titles[]=array('title' => 'id');
$titles[]=array('title' => 'Numéro Délibération');
$titles[]=array('title' => 'Libellé de l\'acte');
$titles[]=array('title' => 'Classification');
$titles[]=array('title' => 'Bordereau');
$titles[]=array('title' => 'Statut');

echo $this->Bs->table($titles, array('hover', 'striped'));

foreach ($deliberations as $delib) {

        $options = array();
        if ($seance_id != null) {
            if (empty($delib['Deliberation']['signee'])
                && in_array($delib['Deliberation']['parapheur_etat'], array(null, 0, -1))
                && in_array($delib['Deliberation']['etat'], array(3, 4))
            )
                $options['checked'] = true;
            else
                $options['disabled'] = true;

            echo $this->Bs->cell($this->Form->checkbox('Deliberation.id_' . $delib['Deliberation']['id'], $options));
        }
        echo $this->Bs->cell($this->Html->link($delib['Deliberation']['id'], array('action' => 'view', $delib['Deliberation']['id'])));
        if (!empty($delib['Deliberation']['num_delib'])) {
            echo $this->Bs->cell($this->Html->link($delib['Deliberation']['num_delib'], array('controller' => 'deliberations', 'action' => 'genereFusionToClient', $delib['Deliberation']['id']), array('class' => 'waiter')));
        } else {
            echo $this->Bs->cell($this->Html->link('Acte : ' . $delib['Deliberation']['id'], array('controller' => 'deliberations', 'action' => 'genereFusionToClient', $delib['Deliberation']['id']), array('class' => 'waiter')));
        }

        echo $this->Bs->cell($delib['Deliberation']['objet_delib']);

            if ($seance_id == null)
               echo $this->Bs->cell(!empty($delib['Deliberation']['num_pref']) ? $delib['Deliberation']['num_pref'] . ' - ' . $delib['Deliberation']['num_pref_libelle'] : '<em>-- Manquante --</em>');
            else {
                $id_num_pref = $delib['Deliberation']['id'] . '_num_pref';
                    if (empty($nomenclatures)) $nomenclatures = array();
                    echo $this->Bs->cell( $this->Form->input('Deliberation.' . $delib['Deliberation']['id'] . '_num_pref', array(
                        'name' => $delib['Deliberation']['id'] . 'classif2',
                        'label' => false,
                        'options' => $nomenclatures,
                        'default' => $delib['Deliberation']['num_pref'],
                        'readonly' => empty($nomenclatures),
                        'empty' => true,
                        'class' => 'select2 selectone',
                        'data-placeholder'=>'Sélectionnez une classification',
                        'style' => 'width:auto; max-width:400px;',
                        'div' => array('style' => 'text-align:center;font-size: 1.1em;'),
                        'escape' => false
                    )));
            }
        echo $this->Bs->cell(!empty($delib['Deliberation']['parapheur_bordereau']) ? $this->Html->link('<i class="fa fa-file-o"></i> Bordereau de signature', array('action' => 'downloadBordereau', $delib['Deliberation']['id']), array('escape' => false, 'title' => 'Télécharger le bordereau de signature', 'style' => 'text-decoration: none')):'');
            
            switch ($delib['Deliberation']['parapheur_etat']) {
                    case -1 :
                        $status=  '<i class="fa fa-exclamation-triangle" title="' . $delib['Deliberation']['parapheur_commentaire'] . '"></i>&nbsp;Retour parapheur : refusée';
                        break;
                    case 1 :
                        $status=  '<i class="fa fa-clock-o"></i> En cours de signature';
                        break;
                    case 2 :
                        $status=  '<i class="fa fa-check"></i> Approuvé dans le parapheur&nbsp;';
                        if (!empty($delib['Deliberation']['signee'])) {
                            if (!empty($delib['Deliberation']['signature']))
                                $status.=  '(<a href="/deliberations/downloadSignature/' . $delib['Deliberation']['id'] . '" title="Télécharger la signature" style="text-decoration: none;">Signature</a>)';
                            else
                                $status.=  '(Visa)';
                        }
                        break;
                    default : //0 ou null
                        if (!empty($delib['Deliberation']['signee'])) {
                            if (!empty($delib['Deliberation']['signature']))
                                $status=  '<i class="fa fa-check"></i> Signée&nbsp;<a href="/deliberations/downloadSignature/' . $delib['Deliberation']['id'] . '" title="Télécharger la signature" style="text-decoration: none;"><i class="fa fa-download"></i></a>';
                            else
                                $status=  '<i class="fa fa-check"></i> Signée manuellement';
                        } else {
                            switch ($delib['Deliberation']['etat']) {
                                case -1 :
                                    $status=  '<i class="fa fa-times"></i> Projet refusé';
                                    break;
                                case 2 :
                                    $status=  '<i class="fa fa-clock-o"></i> A faire voter';
                                    break;
                                case 3 :
                                    $status=  '<i class="fa fa-thumbs-up"></i> Projet voté';
                                    break;
                                case 4 :
                                    $status=  '<i class="fa fa-thumbs-down"></i> Projet non adopté';
                                    break;
                                case 5 :
                                    $status=  '<i class="fa fa-certificate"></i> Projet envoyé au tdt';
                                    break;
                                default :
                                    $status= '<i class="fa fa-pencil"></i> En cours d&apos;élaboration';
                            }
                            
                        }
                    }
                    echo $this->Bs->cell($status);
    }
echo $this->Bs->endTable();
echo $this->Html->tag(null, '<br />') ;
$this->BsForm->setLeft(0);
$this->BsForm->setRight(12);
    if (!empty($seance_id) && !empty($deliberations)) {
        echo $this->Bs->row().
        $this->Bs->col('xs4').
        $this->BsForm->selectGroup('Parapheur.circuit_id', array_merge(array(''=>''), $circuits), array(
                                'content'=>$this->Bs->icon('mail-forward').' Envoyer <span id="nbProjetChecked"></span>',
                                'type' => 'submit',
                                'state' => 'success',
                                'side'=>'right'), array(
                                'class'=>'select2 selectone',
                                'data-placeholder'=>'Sélectionnez une action',
                                'label' => 'Actions disponibles')).
        $this->Bs->close(2).
        $this->BsForm->end();
    }
echo $this->Html2->btnCancel($previous);
?>
<script type="application/javascript">
    /**
     * Actions au chargement de la page
     */
    $(document).ready(function () {
        $('.selectone').select2({
            allowClear: true,
        });
        $('input[type="checkbox"]').change(changeSelection);
        changeSelection();
    });

    /**
     * Afficher/Masquer la sélection de circuit selon si la selection est vide ou non
     */
    function changeSelection() {
        if ($('input[type="checkbox"]:checked').length > 0) {
            $('#select-circuit').show();
        } else {
            $('#select-circuit').hide();
        }
    }
</script>

<style>
/*    .select2-container .select2-choice {
        border-radius: 0;
    }*/
</style>