<div id="vue_cadre">
<dl>
    <h3><?php echo $titre; ?></h3>
        <div class="imbrique">
            <dt>Nom</dt>
            <dd class="compact"><?php echo $this->data['Infosupdef']['nom']; ?></dd>
        </div>
        <div class="imbrique">
            <div class="gauche">
                    <dt>Date de cr&eacute;ation</dt>
                    <dd>&nbsp;<?php echo $this->data['Infosupdef']['created']?></dd>
            </div>
            <div class="droite">
                    <dt>Date de modification</dt>
                    <dd>&nbsp;<?php echo $this->data['Infosupdef']['modified']?></dd>
            </div>
        </div>
	<dt>Commentaire</dt>
	<dd class="compact"><?php echo $this->data['Infosupdef']['commentaire']; ?></dd>
	<dt>Code</dt>
	<dd class="compact"><?php echo $this->data['Infosupdef']['code']; ?></dd>
	<dt>Num&eacute;ro d'ordre</dt>
	<dd class="compact"><?php echo $this->data['Infosupdef']['ordre']; ?></dd>
	<dt>Type</dt>
	<dd class="compact"><?php echo $this->data['Infosupdef']['libelleType']; ?></dd>
	<dt>Valeur initiale</dt>
	<dd class="compact"><?php echo $this->data['Infosupdef']['val_initiale']; ?></dd>
	<dt>Inclure dans la recherche</dt>
	<dd class="compact"><?php echo $this->data['Infosupdef']['libelleRecherche']; ?></dd>
	<dt>Active</dt>
	<dd class="compact"><?php echo $this->data['Infosupdef']['libelleActif']; ?></dd>

</dl>
<div id="actions_fiche" class="btn-toolbar pagination-centered">
    <div class="btn-group">
<?php echo $this->Html->link('<i class="fa fa-arrow-left"></i> Retour', $lienRetour, array('class'=>'btn', 'name'=>'Retour','escape'=>false))?>
	<?php
        if ($Droits->check($this->Session->read('user.User.id'), 'Infosupdefs:edit'))
         echo $this->Html->link('<i class="fa fa-edit"></i> Modifier',
                           '/infosupdefs/edit/' . $this->data['Infosupdef']['id'], 
                           array('class'=>'btn  btn-primary', 'escape' => false,
                                 'name'=>'Modifier'))
	?>
        </div>
</div>
</div>

