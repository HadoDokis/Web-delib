<div id="vue_cadre">
    <h3>Fiche de type d'acte</h3>

    <dl>
        <div class="demi">
            <dt>Libelle</dt>
            <dd>&nbsp;<?php echo $typeacte['Typeacte']['libelle'] ?></dd>
        </div>
        <div class="demi">
            <dt>Nature</dt>
            <dd>&nbsp;<?php echo $typeacte['Nature']['libelle'] ?></dd>
        </div>
        <div class="spacer"></div>

        <div class="demi">
            <dt>Modèle de projet</dt>
            <dd>&nbsp;<?php echo $typeacte['Modelprojet']['name']; ?></dd>
        </div>

        <div class="demi">
            <dt>Modèle de document final</dt>
            <dd>&nbsp;<?php echo $typeacte['Modeldeliberation']['name'] ?></dd>
        </div>
        <div class="spacer"></div>

        <div class="demi">
            <dt>Date de cr&eacute;ation</dt>
            <dd>&nbsp;<?php echo $typeacte['Typeacte']['created'] ?></dd>
        </div>
        <div class="demi">
            <dt>Date de modification</dt>
            <dd>&nbsp;<?php echo $typeacte['Typeacte']['modified'] ?></dd>
        </div>
        <div class="spacer"></div>
        <div>
            <dt>Gabarit : texte de projet</dt>
            <dd>
                <?php
                if (!empty($typeacte['Typeacte']['gabarit_projet']))
                    echo $this->Html->link('gabarit_projet.odt', array('action' => 'downloadgabarit', $typeacte['Typeacte']['id'], 'projet'));
                else
                    echo '-';
                ?>
            </dd>
        </div>
        <div>
            <dt>Gabarit : note de synthèse</dt>
            <dd>
                <?php
                if (!empty($typeacte['Typeacte']['gabarit_synthese']))
                    echo $this->Html->link('gabarit_synthese.odt', array('action' => 'downloadgabarit', $typeacte['Typeacte']['id'], 'synthese'));
                else
                    echo '-';
                ?>
            </dd>
        </div>
        <div>
            <dt>Gabarit : texte d'acte</dt>
            <dd>
                <?php
                if (!empty($typeacte['Typeacte']['gabarit_acte']))
                    echo $this->Html->link('gabarit_acte.odt', array('action' => 'downloadgabarit', $typeacte['Typeacte']['id'], 'acte'));
                else
                    echo '-';
                ?>
            </dd>
        </div>
    </dl>

    <br/>

    <div id="actions_fiche" class="btn-group">
        <?php echo $this->Html->link('<i class="fa fa-arrow-left"></i> Retour', '/typeactes/index', array(
            'class' => 'btn',
            'title' => 'Retourner à la liste',
            'escape' => false)) ?>
        <?php echo $this->Html->link('<i class="fa fa-edit"></i> Modifier', '/typeactes/edit/' . $typeacte['Typeacte']['id'], array(
                'class' => 'btn btn-primary',
                'title' => 'Modifier le type d\'acte',
                'escape' => false)) ?>
        </ul>
    </div>
</div>
<style>
    .btn-group>.btn{
        float: none;
    }
</style>