<?php
$this->Html->addCrumb('Mes projets', array('controller' => $this->params['controller'], 'action' => ''));

$titre=$titreVue . ' (' . $nbProjets .' '. ($nbProjets > 1 ?'projets':'projet').')';

if (($this->params['filtre'] != 'hide')
    && ($this->params['action'] != 'mesProjetsRecherche')
    && ($this->params['action'] != 'tousLesProjetsRecherche')
) {
    echo $this->element('filtre').
    $this->Bs->tag('h3', $titre);
    $this->Html->addCrumb($titre);

} else {
    echo $this->Html->tag('div', null, array('class' => 'ouvrable', 'id' => $titreVue));
}

if (isset($traitement_lot) && ($traitement_lot == true))
    echo $this->Form->create('Deliberation', array('url' => array('controller' => 'deliberations', 'action' => 'traitementLot'), 'type' => 'post', 'class' => 'waiter'));

echo $this->element('9cases',array('projets'=>$this->data,
    'traitement_lot'=> isset($traitement_lot)?$traitement_lot:null)
    ); 

if (isset($traitement_lot) && ($traitement_lot == true)) {
    echo $this->html->tag('div', '', array('class' => 'spacer'));
    $actions_possibles['generation'] = 'Génération';
    echo $this->html->tag('fieldset', null, array('id' => 'generation-multiseance'));
    echo $this->Form->input('Deliberation.action', array(
        'options' => $actions_possibles,
        'empty' => true,
        'div' => array('class' => 'pull-left'),
        'label' => false,
        'after' => '<i class="fa fa-arrow-right" style="margin-left: 10px"></i>'
    ));
    echo $this->Form->input('Deliberation.modele', array(
        'options' => $modeles,
        'empty' => true,
        'div' => array('id'=>'divmodeles', 'class' => 'pull-left', 'style' => 'display:none;margin-left: 10px'),
        'label' => false,
        'after' => '<i class="fa fa-arrow-right" style="margin-left: 10px"></i>'
    ));
    echo $this->Form->button('<i class="fa fa-cogs"></i> Executer<span id="nbDeliberationsChecked"></span>', array(
        'type' => 'submit',
        'class' => 'btn btn-primary pull-left',
        'title' => "Executer",
        'id' => 'generer_multi_delib',
        'style' => 'margin-left: 10px'
    ));
    echo $this->html->tag('div', '', array('class' => 'spacer'));
    echo $this->html->tag('/fieldset', null);
}

if (!empty($listeLiens)) {
    echo '<div role="toolbar" class="btn-toolbar" style="text-align: center;"><div class="btn-group">';
    if (in_array('add', $listeLiens)) {
        echo $this->Html->link('<i class=" fa fa-plus"></i> Ajouter un projet',
            array("action" => "add"),
            array('class' => 'btn btn-primary',
                'escape' => false,
                'title' => 'Créer un nouveau projet',
                'style' => 'margin-top: 10px;'));
    }
    if (in_array('mesProjetsRecherche', $listeLiens)) {
        echo '<ul class="actions">';
        echo '<li>' . $this->Html->link('Nouvelle recherche', '/deliberations/mesProjetsRecherche', array('class' => 'btn', 'escape' => false, 'alt' => 'Nouvelle recherche parmi mes projets', 'title' => 'Nouvelle recherche parmi mes projets')) . '</li>';
        echo '</ul>';
    }
    if (in_array('tousLesProjetsRecherche', $listeLiens)) {
        echo '<ul class="actions">';
        echo '<li>' . $this->Html->link('Nouvelle recherche', '/deliberations/tousLesProjetsRecherche', array('class' => 'btn', 'escape' => false, 'alt' => 'Nouvelle recherche parmi tous les projets', 'title' => 'Nouvelle recherche parmi tous les projets')) . '</li>';
        echo '</ul>';
    }
    echo "</div></div>";
}
?>
<script type="text/javascript">
    $(document).ready(function () {
        //Lors d'action sur une checkbox :
        $('input[type=checkbox]').change(selectionChange);
        
        $('#DeliberationAction').select2({
            width: 'resolve',
            placeholder: 'Selectionner une action'
        }).change(selectionChange).trigger('change');
        
        $('#DeliberationModele').select2({width: 'resolve', placeholder: 'Selectionner un modèle'});
    });
    function selectionChange() {
        var nbChecked = $('input[type=checkbox].checkbox_deliberation_generer:checked').length;
        //Apposer ou non la class disabled au bouton selon si des checkbox sont cochées (style)
        if (nbChecked > 0 && $('#DeliberationAction').val() != '') {
            $('#generer_multi_delib').removeClass('disabled');
            $("#generer_multi_delib").prop("disabled", false);
        } else {
            $('#generer_multi_delib').addClass('disabled');
            $("#generer_multi_delib").prop("disabled", true);
        }
        if($('#DeliberationAction').val() == 'generation'){
            $('#divmodeles').show();
        }else {
            $('#divmodeles').hide();
        }
        $('#nbDeliberationsChecked').text('(' + nbChecked + ')');
    }
</script>