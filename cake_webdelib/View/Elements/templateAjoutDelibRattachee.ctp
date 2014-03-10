<?php
/**
 * Application: webdelib / Adullact.
 * Date: 07/03/14
 * @author: Florian Ajir <florian.ajir@adullact.org>
 * @license CeCiLL V2 <http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html>
 *
 * Description : Template (invisible) pour l'ajout de déliberations rattachées (clone)
 */

?>
<fieldset id="delibRattacheeTemplate" style="display: none">
    <legend><span class="label label-success">Création</span> Nouvelle délibération rattachée</legend>
    <?php
    //Libelle objet_delib
    echo $this->Form->input('Multidelib.Template.objet_delib', array(
        'type' => 'textarea',
        'label' => 'Libellé <abbr title="obligatoire">*</abbr>',
        'value' => '',
        'cols' => '60',
        'rows' => '2',
        'disabled' => true
    ));
    echo $this->Html->tag('div', null);
    //Upload texte acte
    echo $this->Form->input("Multidelib.Template.deliberation", array(
        'label' => 'Texte acte',
        'class' => 'texte_acte_multidelib',
        'type' => 'file',
        'disabled' => true,
        'div' => false
    ));
    //Gabarit acte
    echo $this->Html->tag('span', null, array('id' => 'MultidelibTemplateGabaritBloc', 'class' => 'MultidelibGabaritBloc', 'title' => "Gabarit par défaut pour ce type d'acte, pour le modifier en WebDAV, veuillez valider le formulaire puis revenir en édition."));
    echo $this->Html->tag('i', '', array('class' => 'fa fa-magic'));
    echo '&nbsp;';
    echo $this->Html->link('', 'javascript:void(0)', array('class' => 'gabarit_name_multidelib', 'style' => 'cursor:help'));
    echo '&nbsp;&nbsp;';
    echo $this->Html->link('<i class="fa fa-eraser"></i> Supprimer', 'javascript:void(0)', array('id' => 'supprimerMultidelibTemplateGabarit', 'class' => 'supprimerMultidelibTemplateGabarit btn btn-mini btn-danger', 'onclick' => 'supprimerGabaritMultidelib(Template)', 'title'=>'Cliquez ici pour ne pas utiliser le gabarit proposé par défaut', 'escape' => false));
    echo $this->Form->hidden("Multidelib.Template.gabarit", array(
        'value' => '0',
        'class' => 'gabarit_acte_multidelib',
        'disabled' => true
    ));
    echo $this->Html->tag('/span');
    echo $this->Html->tag('/div');

    echo $this->Html->tag('div', '', array('class' => 'spacer'));
    //Annexes
    echo $this->element('annexe', array('ref' => 'delibRattacheeTemplate', 'annexes' => array()));
    //Note
    echo $this->Html->tag('div', $this->Html->tag('small', '* Note : les modifications apportées ici ne prendront effet que lors de la sauvegarde du projet.'), array('class' => 'text-right'));
    ?>
</fieldset>