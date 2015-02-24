<div id="vue_cadre">
    <h3>Fiche acteur</h3>

    <dl>
        <div class="demi">
            <dt>Identité</dt>
            <dd><?php echo $acteur['Acteur']['salutation'] . ' ' . $acteur['Acteur']['prenom'] . ' ' . $acteur['Acteur']['nom'] . ($acteur['Acteur']['titre'] ? ', ' : '') . $acteur['Acteur']['titre'] ?></dd>
            <dt>Adresse postale</dt>
            <dd class="compact"><?php echo $acteur['Acteur']['adresse1'] ?></dd>
            <dd class="compact"><?php echo $acteur['Acteur']['adresse2'] ?></dd>
            <dd class="compact"><?php echo $acteur['Acteur']['cp'] ?></dd>
            <dd class="compact"><?php echo $acteur['Acteur']['ville'] ?></dd>
            <dt>Contacts</dt>
            <dd class="compact">Téléphone fixe : <?php echo $acteur['Acteur']['telfixe'] ?></dd>
            <dd class="compact">Téléphone mobile : <?php echo $acteur['Acteur']['telmobile'] ?></dd>
            <dd class="compact">Adresse email : <?php echo $acteur['Acteur']['email'] ?></dd>
        </div>
        <div class="demi">
            <dt>Type</dt>
            <dd><?php echo $acteur['Typeacteur']['nom'] ?></dd>
            <?php if ($acteur['Typeacteur']['elu']) {
                echo "<dt>Numéro d'ordre dans le conseil</dt>";
                echo "<dd>" . $acteur['Acteur']['position'] . "</dd>";
                echo "<dt>Délégations</dt>";
                foreach ($acteur['Service'] as $service) {
                    echo '<dd class="compact">' . $service['libelle'] . '</dd>';
                };
                echo "<dt>Date Naissance</dt>";
                echo "<dd>" . $acteur['Acteur']['date_naissance'] . "</dd>";
            } ?>
        </div>
        <div class="spacer"></div>

        <div class="tiers">
            <dt>Note</dt>
            <dd><?php echo $acteur['Acteur']['note'] ?></dd>
        </div>
        <div class="tiers">
            <dt>Date de création</dt>
            <dd><?php echo $acteur['Acteur']['created'] ?></dd>
        </div>
        <div class="tiers">
            <dt>Date de modification</dt>
            <dd><?php echo $acteur['Acteur']['modified'] ?></dd>
        </div>
        <div class="spacer"></div>

    </dl>

    <div style='text-align: center;'>
        <div class='btn-group'>
            <?php
            echo $this->Html->link('<i class="fa fa-arrow-left"></i> Retour', $previous, array('escape' => false, 'class' => 'btn'));
            if ($canEdit)
                echo $this->Html->link('<i class="fa fa-edit"></i> Modifier', array('action' => 'edit', $acteur['Acteur']['id']), array('escape' => false, 'class' => 'btn'));
            ?>
        </div>
    </div>
    <div class="spacer"></div>

</div>