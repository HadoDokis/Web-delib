<?php
$this->Html->addCrumb('Liste des types d\'acte', array('controller'=>$this->request['controller'],'action'=>'index'));
$this->Html->addCrumb('Types d\'acte');

echo $this->Bs->tag('h3', 'Types d\'acte');
?>
<div class="panel panel-default">
    <div class="panel-heading">Fiche type d'acte: <?php echo $typeacte['Typeacte']['libelle'] ?></div>
    <div class="panel-body">
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
                    echo $this->Html->link($typeacte['Typeacte']['gabarit_projet_name'], array('action' => 'downloadgabarit', $typeacte['Typeacte']['id'], 'projet'));
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
                    echo $this->Html->link($typeacte['Typeacte']['gabarit_synthese_name'], array('action' => 'downloadgabarit', $typeacte['Typeacte']['id'], 'synthese'));
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
                    echo $this->Html->link($typeacte['Typeacte']['gabarit_acte_name'], array('action' => 'downloadgabarit', $typeacte['Typeacte']['id'], 'acte'));
                else
                    echo '-';
                ?>
            </dd>
        </div>
    </dl>        </ul>

    <br/>
<?php
echo $this->Bs->row().
$this->Bs->col('md4 of5');
echo $this->Bs->div('btn-group', null,array('id'=>"actions_fiche" )) .
    $this->Html2->btnCancel(),
    $this->Bs->btn('Modifier', array('controller' => 'typeactes', 'action' => 'edit', $typeacte['Typeacte']['id']), array('type' => 'primary', 'icon' => 'glyphicon glyphicon-edit', 'title' => 'Modifier')) .
    $this->Bs->close(6);