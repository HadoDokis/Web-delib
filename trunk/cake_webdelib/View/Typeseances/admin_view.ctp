<?php
$this->Html->addCrumb('Liste des types de séance', array('controller'=>$this->request['controller'],'action'=>'index'));
$this->Html->addCrumb('Types de séance');

echo $this->Bs->tag('h3', 'Types de séance');
?>
<div class="panel panel-default">
    <div class="panel-heading">Fiche type de séance: <?php echo $typeseance['Typeseance']['libelle'] ?></div>
    <div class="panel-body">
    <dl>
        <div class="demi">
            <dt>Libellé</dt>
            <dd>&nbsp;<?php echo $typeseance['Typeseance']['libelle'] ?></dd>
        </div>
        <div class="demi">
            <dt>Nombre de jours avant retard</dt>
            <dd><?php echo $typeseance['Typeseance']['retard']; ?></dd>
        </div>
        <div class="spacer"></div>
        <div class="demi">
            <dt>Action</dt>
            <dd>&nbsp;<?php echo $typeseance['Typeseance']['action'] ? 'Avis' : 'Vote' ?></dd>
        </div>
        <div class="demi">
            <dt>Compteur</dt>
            <dd>&nbsp;<?php echo $typeseance['Compteur']['nom'] ?></dd>
        </div>
        <div class="spacer"></div>

        <div class="demi">
            <dt>Modèle de la convocation</dt>
            <dd>&nbsp;<?php echo $typeseance['Modelconvocation']['name']; ?></dd>
        </div>
        <div class="demi">
            <dt>Modèle de l'ordre du jour</dt>
            <dd>&nbsp;<?php echo $typeseance['Modelordredujour']['name'] ?></dd>
        </div>
        <div class="spacer"></div>

        <div class="demi">
            <dt>Modèle du PV sommaire</dt>
            <dd>&nbsp;<?php echo $typeseance['Modelpvsommaire']['name'] ?></dd>
        </div>
        <div class="demi">
            <dt>Modèle du PV détaillé</dt>
            <dd>&nbsp;<?php echo $typeseance['Modelpvdetaille']['name'] ?></dd>
        </div>
        <div class="spacer"></div>

        <div class="demi">
            <dt>Date de création</dt>
            <dd>&nbsp;<?php echo $typeseance['Typeseance']['created'] ?></dd>
        </div>
        <div class="demi">
            <dt>Date de modification</dt>
            <dd>&nbsp;<?php echo $typeseance['Typeseance']['modified'] ?></dd>
        </div>
        <div class="spacer"></div>
    </dl>     </ul>

    <br/>
<?php
echo $this->Bs->row().
$this->Bs->col('md4 of5');
echo $this->Bs->div('btn-group', null,array('id'=>"actions_fiche" )) .
    $this->Html2->btnCancel(),
    $this->Bs->btn('Modifier', array('controller' => 'typeseances', 'action' => 'edit', $typeseance['Typeseance']['id']), array('type' => 'primary', 'icon' => 'glyphicon glyphicon-edit', 'title' => 'Modifier')) .
    $this->Bs->close(6);
