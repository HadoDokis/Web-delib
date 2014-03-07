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
    <legend>Nouvelle délibération rattachée</legend>
    <?php
    echo $this->Form->input('Multidelib.Template.objet_delib', array(
        'type' => 'textarea',
        'label' => 'Libellé <abbr title="obligatoire">*</abbr>',
        'value' => '',
        'cols' => '60',
        'rows' => '2',
        'disabled' => true
    ));
    echo $this->Form->input("Multidelib.Template.deliberation", array(
        'label' => 'Texte acte',
        'type' => 'file',
        'disabled' => true
    ));
    echo $this->element('annexe', array('ref' => 'delibRattacheeTemplate', 'annexes' => array()));
    echo $this->Html->tag('div', $this->Html->tag('small', '* Note : les modifications apportées ici ne prendront effet que lors de la sauvegarde du projet.'), array('class' => 'text-right'));
    ?>
</fieldset>