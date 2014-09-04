<?php
echo $this->Bs->tag('h3', 'Types d\'acte');
?>
<div class="panel panel-default">
    <div class="panel-heading">Fiche type d'acteur: <?php echo $typeacteur['Typeacteur']['nom'] ?></div>
    <div class="panel-body">
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

    </dl>        </ul>

    <br/>
<?php
echo $this->Bs->row().
$this->Bs->col('md4 of5');
echo $this->Bs->div('btn-group', null,array('id'=>"actions_fiche" )) .
    $this->Html2->btnCancel(),
    $this->Bs->btn('Modifier', array('action' => 'edit', $typeacteur['Typeacteur']['id']), array('type' => 'primary', 'icon' => 'glyphicon glyphicon-edit', 'title' => 'Modifier')) .
    $this->Bs->close(6);