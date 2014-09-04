<?php
echo $this->Bs->tag('h3', 'Utilisateurs');
?>
<div class="panel panel-default">
    <div class="panel-heading">Fiche utilisateur: <?php echo $user['User']['login'].' '.$user['User']['prenom'] ?></div>
    <div class="panel-body">
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

    </dl></ul>

    <br/>
 <?php
echo $this->Bs->row().
$this->Bs->col('md4 of5');
echo $this->Bs->div('btn-group', null,array('id'=>"actions_fiche" )) .
    $this->Html2->btnCancel(),
    $this->Bs->btn('Modifier', array('controller' => 'users', 'action' => 'edit', $user['User']['id']), array('type' => 'primary', 'icon' => 'glyphicon glyphicon-edit', 'title' => 'Modifier')) .
    $this->Bs->close(6);
