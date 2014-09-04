<?php if (isset($message)) echo $message; 
echo $this->element('filtre');

$projet_etat_libelle=array(
    0=>"<i class='fa fa-pencil'></i> En cours de rédaction",
    1=>"<i class='fa fa-clock-o'></i> Dans un circuit",
    2=>"<i class='fa fa-check'></i> Validé",
    3=>"<i class='fa fa-thumbs-up'></i> Adopté",
    4=>"<i class='fa fa-thumbs-down'></i> Refusé",
    5=>"<i class='fa fa-certificate'></i> Envoyé au TDT"
        );

echo $this->Bs->tag('h3', $titreVue) .

$this->BsForm->create('Deliberation', array('url' => array('controller'=>'deliberations', 'action'=>'sendActesToSignature'), 'type' => 'file')).
$this->Bs->table(array(
    array('title' => $this->action != "autresActesAValider" && !empty($actes)?$this->Bs->checkbox( array('id'=>'masterCheckbox')):''),
    array('title' => 'Identifiant'),
    array('title' => 'Type d\'acte'),
    array('title' => 'Libellé de l\'acte'),
    array('title' => 'Titre'),
    array('title' => 'Classification'),
    array('title' => 'Circuit'),
    array('title' => 'État'),
    $this->action == 'autreActesValides' ? array('title' => 'État parapheur') : null,
    array('title' => 'Actions')
        ), array('hover', 'striped'));
foreach ($actes as $acte) {
    
    if($this->action == 'autreActesValides'){
        $signature_etat_libelle='';
        switch ($acte['Deliberation']['parapheur_etat']) {
                    case -1 :
                        $signature_etat_libelle='<i class="fa fa-exclamation-triangle" title="' . $acte['Deliberation']['parapheur_commentaire'] . '"></i>&nbsp;Refusé par le parapheur';
                        break;
                    case 1 :
                        $signature_etat_libelle='<i class="fa fa-clock-o"></i> En cours de signature';
                        break;
                    case 2 :
                        $signature_etat_libelle='<i class="fa fa-check"></i> Approuvé dans le parapheur&nbsp;';
                        if (!empty($acte['Deliberation']['signature']))
                            $signature_etat_libelle.= '(Signé&nbsp;<a href="/deliberations/downloadSignature/' . $acte['Deliberation']['id'] . '" title="Télécharger la signature" style="text-decoration: none;"><i class="fa fa-download"></i></a>)';
                        else
                            $signature_etat_libelle.= '(Visa)';
                        break;
                    default : //0 ou null
                        if (!empty($acte['Deliberation']['signee']))
                            $signature_etat_libelle= '<i class="fa fa-check"></i> Signature manuscrite';
                        else
                            $signature_etat_libelle= 'Non envoyé';
        }
    }
    
       $actions=$this->Bs->div('btn-group') .
        $this->Bs->btn(null, array('controller' => 'deliberations', 'action' => 'view', $acte['Deliberation']['id']), 
                array('type' => 'default', 
                    'icon' => ' glyphicon glyphicon-eye-open', 
                    'title' => 'Vue détaillée du projet')) .
        $this->Bs->close();
        
        //if ($acte['Deliberation']['etat'] >= 2 && !empty($acte['Deliberation']['signee']))
        //   $model_id = $acte['Modeltemplate']['modelefinal_id'];
//        else
//            $model_id = $acte['Modeltemplate']['modeleprojet_id'];
        //
        $actions.= $this->Bs->div('btn-group') .
                $this->Bs->btn('Choisir un modèle <span class="caret"></span>', 
                        array(), 
                        array('type' => 'default', 
                            'icon' => 'glyphicon glyphicon-cog', 
                            'escape'=>false,'class'=>'dropdown-toggle', 
                            'data-toggle'=>'dropdown')).
                $this->Bs->nestedList(array(
                $this->Bs->link('PV sommaire', array('controller' => 'deliberations', 'action' => 'genereFusionToClient', $acte['Deliberation']['id'] , 'pvsommaire'), 
                    array(
                            'title' => 'Génération du pv sommaire pour la séance du ',
                            'class' => 'waiter',
                            'data-modal' => 'Génération du PV sommaire en cours')),
                $this->Bs->link('PV complet', array('controller' => 'deliberations', 'action' => 'genereFusionToClient', $acte['Deliberation']['id'] , 'pvdetaille'), 
                    array(
                    'title' => 'Génération du pv complet pour la séance du ',
                    'class' => 'waiter',
                    'data-modal' => 'Génération du PV complet en cours'))
                )
                , array('class'=>'dropdown-menu','role'=>'menu')).
        $this->Bs->close();

        
        if($this->action == 'autresActesAValider' && $canGoNext && !empty($acte['Deliberation']['circuit_id']) && $acte['Deliberation']['etat'] == 1) {
            $actions.=$this->Bs->btn(null, array('controller' => 'deliberations', 'action' => 'goNext', $acte['Deliberation']['id']), 
                array('type' => 'default', 
                    'icon' => ' glyphicon glyphicon-eye-open', 
                    'title' => 'Sauter une ou plusieurs étapes du circuit'));
        }
        if ($this->action == 'autresActesAValider' && $peuxValiderEnUrgence && !empty($acte['Deliberation']['circuit_id']) && $acte['Deliberation']['etat'] == 1) {
            $actions.=$this->Bs->btn(null, array('controller' => 'deliberations', 'action' => 'validerEnUrgence', $acte['Deliberation']['id']), 
                array('type' => 'default', 
                    'icon' => ' glyphicon glyphicon-eye-open', 
                    'title' => 'Valider en urgence le projet',
                    'confirm'=>'Confirmer la validation en urgence du projet ' . $acte['Deliberation']['id'] . ' ?'));
        }

        if ($this->action == 'autreActesValides' && $canEdit && !$acte['Deliberation']['signature_encours']) {
            $actions.=$this->Bs->btn(null, array('controller' => 'deliberations', 'action' => 'edit', $acte['Deliberation']['id']), 
                array('type' => 'primary', 
                    'icon' => ' glyphicon glyphicon-edit', 
                    'title' => 'Modifier le projet'));
        }
        if ($this->action == 'autreActesValides' && !$acte['Deliberation']['signature_encours']) {
            debug($acte['Deliberation']['signature_encours']);
            $actions.=$this->Bs->btn(null, array('controller' => 'deliberations', 'action' => 'attribuercircuit', $acte['Deliberation']['id']), 
                array('type' => 'default', 
                    'icon' => 'glyphicon glyphicon-road', 
                    'title' => 'Attribuer un circuit pour le projet ' . $acte['Deliberation']['objet']));
        }
    
    echo $this->Bs->tableCells(array(
        ($this->action != "autresActesAValider")?
        $this->BsForm->checkbox('Deliberation.id_' . $acte['Deliberation']['id'], 
                ((empty($acte['Deliberation']['signee'])
                && in_array($acte['Deliberation']['parapheur_etat'], array(null, 0, -1))
                && $acte['Deliberation']['etat'] >= 2
            )?
                $options['checked'] = true
            :
                $options['disabled'] = true)
                ):''
        ,
        (!empty($acte['Deliberation']['num_delib'])?
        $this->Html->link($acte['Deliberation']['num_delib'], array('action' => 'view', $acte['Deliberation']['id']))
        :
        $this->Html->link('Acte : ' . $acte['Deliberation']['id'], array('action' => 'view', $acte['Deliberation']['id']))
        ),
        $acte['Typeacte']['libelle'],
        $acte['Deliberation']['objet'],
        $acte['Deliberation']['titre'],
        
        ////////////////////////////////
        ////classification
        (($this->action != 'autreActesValides')?
            !empty($acte['Deliberation']['num_pref']) ? $acte['Deliberation']['num_pref'] . ' - ' . $acte['Deliberation']['num_pref_libelle'] : '<em>-- Manquante --</em>'
        :
            //$id_num_pref = $acte['Deliberation']['id'] . '_num_pref';
            ((Configure::read('TDT') == 'PASTELL') ?
                $this->BsForm->input('Deliberation.' . $acte['Deliberation']['id'] . '_num_pref', array(
                    'name' => $acte['Deliberation']['id'] . 'classif2',
                    'label' => false,
                    'options' => (empty($nomenclatures)? null :$nomenclatures),
                    'default' => $acte['Deliberation']['num_pref'],
                    'readonly' => empty($nomenclatures),
                    'empty' => true,
                    'class' => 'select2 selectone',
                    'style' => 'width:auto; max-width:400px;',
                    'div' => array('style' => 'text-align:center;font-size: 1.1em;'),
                    'escape' => false
                ))
            :
//                $this->BsForm->input('Deliberation.' . $acte['Deliberation']['id'] . '_num_pref_libelle', array(
//                    'label' => false,
//                    'div' => false,
//                    'id' => $acte['Deliberation']['id'] . 'classif1',
//                    'style' => 'width: 25em;',
//                    'disabled' => true,
//                    'value' => $acte['Deliberation']['num_pref'] . ' - ' . $acte['Deliberation']['num_pref_libelle']));
//                <a class="list_form" href="#add"
//                   onclick="javascript:window.open('// echo $this->base; /deliberations/classification?id= echo $acte['Deliberation']['id']; ', 'Classification', 'scrollbars=yes,,width=570,height=450');
//                   id='.$acte['Deliberation']['id'].'_classification_text">[Choisir la classification]</a>
//                $this->BsForm->hidden('Deliberation.' . $acte['Deliberation']['id'] . '_num_pref', array(
//                    'id' => $acte['Deliberation']['id'] . 'classif2',
//                    'name' => $acte['Deliberation']['id'] . 'classif2',
//                    'value' => $acte['Deliberation']['num_pref']
//                ));
            '')
        ),
        //////////////////////////////////
        $acte['Circuit']['nom'],
        $projet_etat_libelle[$acte['Deliberation']['etat']], 
        (!empty($signature_etat_libelle)?$signature_etat_libelle : null),
        $actions
    ));
}
echo $this->Bs->endTable();

if ($this->action == "autreActesValides" && !empty($actes)) {
    echo '<div id="select-circuit">';
    echo($this->BsForm->input('Parapheur.circuit_id', array('class' => 'select-circuit select2', 'options' => $circuits, 'label' => array('text' => 'Circuits disponibles', 'class' => 'circuits_label'), 'div' => false)));
    echo $this->BsForm->button('<i class="fa fa-mail-forward"></i> Envoyer', array('class' => 'btn btn-inverse sans-arrondi', 'escape' => false));
    echo '</div>';
}
echo $this->BsForm->end();
?>
<script type="application/javascript">
    /**
     * Actions au chargement de la page
     */
    $(document).ready(function () {
        $('#ParapheurCircuitId').select2({ width: 'resolve' });
        $('.selectone').select2({
            allowClear: true,
            placeholder: 'Aucune classification',
            width: 'resolve'
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
    .select2-container .select2-choice {
        border-radius: 0;
    }
</style>
