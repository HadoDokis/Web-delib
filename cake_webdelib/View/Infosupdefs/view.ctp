<?php
echo $this->Bs->tag('h3',  $titre);
?>
<div class="panel panel-default">
    <div class="panel-heading">Fiche : <?php echo $this->data['Infosupdef']['nom'] ?></div>
    <div class="panel-body">
        <dl>
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

    <br/>
<?php
echo $this->Bs->row().
$this->Bs->col('md4 of5');
echo $this->Bs->div('btn-group', null,array('id'=>"actions_fiche" )) .
    $this->Html2->btnCancel($lienRetour),
    $this->Bs->btn('Modifier', array('controller' => 'infosupdefs', 'action' => 'edit', $this->data['Infosupdef']['id']), array('type' => 'primary', 'icon' => 'glyphicon glyphicon-edit', 'title' => 'Modifier')) .
    $this->Bs->close(6);

