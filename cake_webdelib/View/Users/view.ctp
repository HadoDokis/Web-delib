<div id="vue_cadre">
    <h3>Fiche utilisateur</h3>
    <dl>
        <div class="tiers">
            <dt>Login</dt>
            <dd><?php echo $user['User']['login'] ?></dd>
        </div>
        <div class="tiers">
            <dt>Nom</dt>
            <dd><?php echo $user['User']['nom'] ?></dd>
        </div>
        <div class="tiers">
            <dt>Prénom</dt>
            <dd><?php echo $user['User']['prenom'] ?></dd>
        </div>
        <div class="spacer"></div>

        <div class="tiers">
            <dt>Telephone fixe</dt>
            <dd><?php echo $user['User']['telfixe'] ?></dd>
        </div>
        <div class="tiers">
            <dt>Telephone mobile</dt>
            <dd><?php echo $user['User']['telmobile'] ?></dd>
        </div>
        <div class="tiers">
            <dt>E-mail</dt>
            <dd><a href="mailto:<?php echo $user['User']['email'] ?>"><?php echo $user['User']['email'] ?></a></dd>
        </div>
        <div class="spacer"></div>

        <div class="tiers">
            <dt>Notification mail</dt>
            <dd><?php echo $user['User']['accept_notif'] ? 'Oui' : 'Non'; ?></dd>
        </div>
        <div class="tiers">
            <dt>Profil</dt>
            <dd><?php echo $user['Profil']['libelle'] ?></dd>
        </div>
        <div class="tiers">
            <dt>Service(s)</dt>
            <?php
            foreach ($user['Service'] as $service) {
                echo '<dd>';
                echo $service['libelle'] . '<br/>';
                echo '</dd>';
            };
            ?>
        </div>
        <div class="spacer"></div>

        <div class="tiers">
            <dt>Circuit par défaut</dt>
            <dd><?php echo $circuitDefautLibelle ?></dd>
        </div>
        <div class="tiers">
            <dt>Note</dt>
            <dd><?php echo $user['User']['note'] ?></dd>
        </div>
        <div class="spacer"></div>

        <div class="tiers">
            <dt>Date de création</dt>
            <dd><?php echo $user['User']['created'] ?></dd>
        </div>
        <div class="tiers">
            <dt>Date de modification</dt>
            <dd><?php echo $user['User']['modified'] ?></dd>
        </div>
        <div class="spacer"></div>

    </dl>
    <div style='text-align: center;'>
        <div class='btn-group'>
            <?php
            echo $this->Html->link('<i class="fa fa-arrow-left"></i> Retour', $previous, array('escape' => false, 'class' => 'btn'));
            if ($canEdit)
                echo $this->Html->link('<i class="fa fa-edit"></i> Modifier', array('action' => 'edit', $user['User']['id']), array('escape' => false, 'class' => 'btn'));
            ?>
        </div>
    </div>
    <div class="spacer"></div>
</div>
