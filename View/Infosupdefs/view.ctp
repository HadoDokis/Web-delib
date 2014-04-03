<div id="vue_cadre">
    <dl>
        <h3><?php echo $titre; ?></h3>
        <div class="imbrique">
            <dt>Nom</dt>
            <dd><?php echo $this->data['Infosupdef']['nom']; ?></dd>
        </div>
        <div class="imbrique">
            <div class="gauche">
                <dt>Date de création</dt>
                <dd><?php echo $this->data['Infosupdef']['created'] ?></dd>
            </div>
            <div class="droite">
                <dt>Date de modification</dt>
                <dd><?php echo $this->data['Infosupdef']['modified'] ?></dd>
            </div>
        </div>
        <dt>Commentaire</dt>
        <dd><?php echo $this->data['Infosupdef']['commentaire']; ?></dd>
        <dt>Code</dt>
        <dd><?php echo $this->data['Infosupdef']['code']; ?></dd>
        <dt>Numéro d'ordre</dt>
        <dd><?php echo $this->data['Infosupdef']['ordre']; ?></dd>
        <dt>Type</dt>
        <dd><?php echo $this->data['Infosupdef']['libelleType']; ?></dd>
        <dt>Valeur initiale</dt>
        <dd><?php echo $this->data['Infosupdef']['val_initiale']; ?></dd>
        <dt>Inclure dans la recherche</dt>
        <dd><?php echo $this->data['Infosupdef']['libelleRecherche']; ?></dd>
        <dt>Active</dt>
        <dd><?php echo $this->data['Infosupdef']['libelleActif']; ?></dd>

    </dl>
    <div class="text-center">
        <div class="btn-group">
            <?php echo $this->Html->link('<i class="fa fa-arrow-left"></i> Retour', $lienRetour, array('class' => 'btn', 'escape' => false)) ?>
            <?php
            if ($Droits->check($this->Session->read('user.User.id'), 'Infosupdefs:edit'))
                echo $this->Html->link('<i class="fa fa-edit"></i> Modifier',
                    array('controller' => 'infosupdefs', 'action' => 'edit', $this->data['Infosupdef']['id']),
                    array('class' => 'btn  btn-primary', 'escape' => false)
                )
            ?>
        </div>
    </div>
</div>

