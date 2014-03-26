<?php
if (!empty($this->data['Multidelib'])) {
    foreach ($this->data['Multidelib'] as $delib) {
        echo $this->Html->tag('fieldset', null, array('id' => 'delibRattachee' . $delib['id']));

        $actionsDelib = $this->Html->tag('span', null, array('class' => 'pull-right btn-group actions-multidelib'));
        $actionsDelib .= $this->Html->link('<i class="fa fa-edit"></i> Modifier', 'javascript:void(0)', array('escape' => false, 'class' => 'btn btn-mini', 'onclick' => 'modifierDelibRattachee(' . $delib['id'] . ')'));
        $actionsDelib .= $this->Html->link('<i class="fa fa-trash-o"></i> Supprimer', 'javascript:void(0)', array('escape' => false, 'class' => 'btn btn-danger btn-mini', 'onclick' => 'supprimerDelibRattachee(' . $delib['id'] . ')'));
        $actionsDelib .= $this->Html->tag('/span');
        $actionsDelib .= $this->Html->tag('span', null, array('class' => 'pull-right cancel-actions-multidelib'));
        $actionsDelib .= $this->Html->link('<i class="fa fa-undo"></i> Annuler', 'javascript:void(0)', array('escape' => false, 'id' => 'annulerModifierDelibRattachee' . $delib['id'], 'class' => 'btn btn-warning btn-mini', 'onclick' => 'annulerModifierDelibRattachee(' . $delib['id'] . ')', 'style' => 'display:none'));
        $actionsDelib .= $this->Html->link('<i class="fa fa-undo"></i> Annuler', 'javascript:void(0)', array('escape' => false, 'id' => 'annulerSupprimerDelibRattachee' . $delib['id'], 'class' => 'btn btn-warning btn-mini', 'onclick' => 'annulerSupprimerDelibRattachee(' . $delib['id'] . ')', 'style' => 'display:none'));
        $actionsDelib .= $this->Html->tag('/span');

        echo $this->Html->tag('legend', '<span class="label">Visualisation</span> Délibération rattachée : ' . $delib['id'] . $actionsDelib);
        //Pour la modification
        echo $this->Form->hidden('Multidelib.' . $delib['id'] . '.id', array('value' => $delib['id'], 'disabled' => false));
        // info pour la suppression
        echo $this->Form->hidden('MultidelibASupprimer.' . $delib['id'], array('value' => $delib['id'], 'disabled' => true));
        // affichage de la délibération rattachée
        echo $this->Html->tag('div', null, array('id' => 'delibRattacheeDisplay' . $delib['id']));
        // affichage libellé
        echo $this->Html->tag('label', 'Libellé : ');
        echo $this->Html->tag('span', $delib['objet_delib'], array('id' => 'Multidelib' . $delib['id'] . 'libelle'));
        echo $this->Html->tag('div', '', array('class' => 'spacer'));
        // affichage texte de délibération
        echo $this->Html->tag('label', 'Texte acte : ');
        echo 'deliberation.odt';

        echo $this->Html->tag('div', '', array('class' => 'spacer'));
        // affichage des annexes
        $annexeOptions = array('mode' => 'display');
        if (isset($delib['Annex'])) $annexeOptions['annexes'] = $delib['Annex'];

        echo $this->element('annexe', array('mode' => 'display', 'annexes' => $delib['Annexes'], 'ref' => 'delibRattachee' . $delib['id']));

        echo $this->Html->tag('div', '', array('class' => 'spacer'));
        echo $this->Html->tag('/div');
        // modification de la délibération rattachée
        echo $this->Html->tag('div', null, array('id' => 'delibRattacheeForm' . $delib['id'], 'style' => 'display:none'));
        // saisie libellé
        echo $this->Form->input('Multidelib.' . $delib['id'] . '.objet_delib', array(
            'type' => 'textarea',
            'label' => 'Libellé <abbr title="obligatoire">*</abbr>',
            'cols' => '60',
            'rows' => '2',
            'value' => $delib['objet_delib'],
            'disabled' => false
        ));
        // saisie texte de délibération
        echo $this->Html->tag('label', 'Texte acte');
        if (empty($delib['deliberation_name']))
            echo $this->Form->input("Multidelib." . $delib['id'] . ".deliberation", array('label' => false, 'type' => 'file', 'title' => 'Texte délibération', 'disabled' => true));
        else {
            $url = Configure::read('PROTOCOLE_DL') . "://" . $_SERVER['SERVER_NAME'] . "/files/generee/projet/" . $delib['id'] . "/deliberation.odt";
            echo $this->Html->tag('span', '', array('id' => 'MultidelibDeliberationAdd' . $delib['id'], 'style' => 'display: none;'));
            echo $this->Html->tag('span', null, array('id' => 'MultidelibDeliberationDisplay' . $delib['id']));
            echo "<a href='$url'><i class='fa fa-pencil'></i> " . $delib['deliberation_name'] . "</a>";
            echo '&nbsp;&nbsp;';
            echo $this->Html->link(
                '<i class="fa fa-trash-o"></i> Supprimer',
                'javascript:supprimerTextDelibDelibRattachee(' . $delib['id'] . ')',
                array('escape' => false, 'class' => 'btn btn-danger btn-mini'),
                'Voulez-vous vraiment supprimer le fichier ?');
            echo $this->Html->tag('/span');
        }
        echo $this->Html->tag('div', '', array('class' => 'spacer'));
        // saisie des annexes
        echo $this->element('annexe', array('annexes' => $delib['Annexes'], 'ref' => 'delibRattachee' . $delib['id'], 'affichage' => 'partiel'));
        echo $this->Html->tag('div', $this->Html->tag('small', '* Note : les modifications apportées ici ne prendront effet que lors de la sauvegarde du projet.'), array('class' => 'text-right'));
        echo $this->Html->tag('/div');
        echo $this->Html->tag('/fieldset');
        echo $this->Html->tag('div', '', array('class' => 'spacer'));
    }
}