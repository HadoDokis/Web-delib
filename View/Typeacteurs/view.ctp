<div id="vue_cadre">
    <h3>Fiche Type d'acteur</h3>

    <dl>

        <div class="demi">
            <dt>Nom</dt>
            <dd>&nbsp;<?php echo $typeacteur['Typeacteur']['nom'] ?></dd>
        </div>
        <div class="demi">
            <dt>Commentaire</dt>
            <dd>&nbsp;<?php echo $typeacteur['Typeacteur']['commentaire'] ?></dd>
        </div>

        <div class="spacer"></div>

        <div class="demi">
            <dt>Statut</dt>
            <dd>&nbsp;<?php echo $typeacteur['Typeacteur']['elu'] ? 'élu' : 'non élu'; ?></dd>
        </div>

        <div class="spacer"></div>

        <div class="demi">
            <dt>Date de création</dt>
            <dd>&nbsp;<?php echo $typeacteur['Typeacteur']['created'] ?></dd>
        </div>
        <div class="demi">
            <dt>Date de modification</dt>
            <dd>&nbsp;<?php echo $typeacteur['Typeacteur']['modified'] ?></dd>
        </div>

        <div class="spacer"></div>

    </dl>

    <div id="actions_fiche" class="btn-group">
        <?php
        echo $this->Html->link('<i class="fa fa-arrow-left"></i> Retour', array('action' => 'index'), array('class' => 'btn', 'title' => 'Retourner à la liste', 'escape' => false));
        if ($Droits->check($this->Session->read('user.User.id'), 'Typeacteurs:edit'))
            echo $this->Html->link('<i class="fa fa-edit"></i> Modifier', array('action' => 'edit', $typeacteur['Typeacteur']['id']), array('class' => 'btn btn-primary', 'title' => 'Modifier', 'escape' => false));
        ?>
    </div>
</div>
<style>
    #actions_fiche {
        text-align: center;
    }
    .btn-group>.btn{
        float: none;
        text-align: center;
    }
</style>