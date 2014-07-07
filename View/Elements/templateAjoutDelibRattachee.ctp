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
<fieldset id="delibRattacheeTemplate" class="delibRattachee" style="display: none">
    <legend>
        <span class="label label-success">Création</span>&nbsp;
        Nouvelle délibération rattachée
        <span class="pull-right cancel-actions-multidelib">
            <a href="javascript:void(0)" class="btn btn-warning btn-mini annulerAjouterDelibRattachee" onclick="annulerAjouterDelibRattachee(this)">
                <i class="fa fa-undo"></i> Annuler
            </a>
        </span>
    </legend>
    <?php
    //Libelle objet_delib
    echo $this->Form->input('Multidelib.Template.objet_delib', array(
        'type' => 'textarea',
        'label' => 'Libellé <abbr title="obligatoire">*</abbr>',
        'value' => '',
        'class' => 'libelle-multidelib',
        'cols' => '60',
        'rows' => '2',
        'disabled' => true,
        'required'
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
    echo $this->Html->link($this->Html->tag('i', '', array('class' => 'fa fa-magic')).'&nbsp;<span class="gabarit_name_multidelib"></span>', 'javascript:void(0)', array('style' => 'cursor:help', 'escape' => false));
    echo $this->Form->hidden("Multidelib.Template.gabarit", array(
        'value' => '1',
        'class' => 'gabarit_acte_multidelib',
        'disabled' => true
    ));
    echo $this->Html->tag('/span');
    echo "&nbsp;&nbsp;";
    echo $this->Html->link('<i class="fa fa-eraser"></i> Effacer', 'javascript:void(0)', array('id' => 'supprimerMultidelibTemplateGabarit', 'class' => 'supprimerMultidelibTemplateGabarit btn btn-mini btn-danger', 'onclick' => 'supprimerGabaritMultidelib(Template)', 'title'=>'Cliquez ici pour ne pas utiliser le gabarit proposé par défaut', 'escape' => false));
    echo $this->Html->tag('/div');

    echo $this->Html->tag('div', '', array('class' => 'spacer'));
    //Annexes
    echo $this->element('annexe', array('ref' => 'delibRattacheeTemplate', 'annexes' => array()));
    ?>
</fieldset>