<?php
if ($this->Html->value('Typeacte.id')) {
    echo "<h2>Modification d'un type d'acte</h2>";
    echo $this->Form->create('Typeacte', array('url' => '/typeactes/edit/' . $this->Html->value('Typeacte.id'), 'type' => 'file'));
} else {
    echo "<h2>Ajout d'un type d'acte</h2>";
    echo $this->Form->create('Typeacte', array('url' => '/typeactes/add/', 'type' => 'file'));
}
?>

<div class="demi">
    <fieldset>
        <legend>Informations générales</legend>
        <?php echo $this->Form->input('Typeacte.libelle', array('label' => 'Libellé <abbr title="obligatoire">*</abbr>', 'size' => '40', 'type' => 'text')); ?>
        <br/>
        <?php echo $this->Form->input('Typeacte.compteur_id', array('label' => 'Compteur <abbr title="obligatoire">*</abbr>', 'options' => $compteurs, 'default' => $this->Html->value('Typeacte.compteur_id'), 'empty' => (count($compteurs) > 1) && (!$this->Html->value('Typeacte.id')))); ?>
        <br/>
        <?php
        if (empty($selectedNatures) && !empty($this->request->data))
            $selectedNatures = $this->request->data['Typeacte']['nature_id'];
        echo $this->Form->input('Typeacte.nature_id', array('label' => 'Nature <abbr title="obligatoire">*</abbr>', 'options' => $natures, 'default' => $selectedNatures, 'empty' => false)); ?>
        <br/>
        <?php
        if ($this->action == 'add')
            echo $this->Form->input('Typeacte.teletransmettre', array('label' => 'Télétransmettre', 'div'=>false, 'checked'=>true));
        else
            echo $this->Form->input('Typeacte.teletransmettre', array('label' => 'Télétransmettre', 'div'=>false));
        ?>
    </fieldset>
</div>

<div class="demi">
    <fieldset>
        <legend>Modèles pour les éditions</legend>
        <?php echo $this->Form->input('Typeacte.modeleprojet_id', array('label' => 'projet <abbr title="obligatoire">*</abbr>', 'options' => $models_projet, 'default' => $this->Html->value('Typeacte.modelprojet_id'), 'empty' => false)); ?>
        <br/>
        <?php echo $this->Form->input('Typeacte.modelefinal_id', array('label' => 'document final <abbr title="obligatoire">*</abbr>', 'options' => $models_docfinal, 'default' => $this->Html->value('Typeacte.modeldeliberation_id'), 'empty' => false)); ?>
    </fieldset>
</div>
<div class="spacer"></div>

<div class="demi">
    <fieldset>
        <legend>Gabarits / Textes par défaut</legend>
        <?php
        echo $this->Form->input('Typeacte.gabarit_projet_upload', array(
            'label' => 'Texte projet',
            'type' => 'file',
            'div' => array('id' => 'div_gabarit_projet')
        ));
        echo $this->Form->hidden('Typeacte.gabarit_projet_upload_erase', array('value' => '0'));
        ?>
        <div class="spacer"></div>
        <?php
        echo $this->Form->input('Typeacte.gabarit_synthese_upload', array(
            'label' => 'Note synthese',
            'type' => 'file',
            'div' => array('id' => 'div_gabarit_synthese')
        ));
        echo $this->Form->hidden('Typeacte.gabarit_synthese_upload_erase', array('value' => '0'));
        ?>
        <div class="spacer"></div>
        <?php
        echo $this->Form->input('Typeacte.gabarit_acte_upload', array(
            'label' => 'Texte acte',
            'type' => 'file',
            'div' => array('id' => 'div_gabarit_acte')
        ));
        echo $this->Form->hidden('Typeacte.gabarit_acte_upload_erase', array('value' => '0'));
        ?>
    </fieldset>
</div>
<div class="spacer"></div>

<div class="submit">
    <?php
    echo $this->Form->hidden('Typeacte.id');
    $this->Html2->boutonsSaveCancel('', array('action' => 'index'));
    ?>
</div>
<?php $this->Form->end(); ?>
<script type="application/javascript">
    $(document).ready(function () {
        <?php if ($this->action == 'edit' && !empty($this->request->data['Typeacte']['gabarit_projet'])) : ?>
        var gabaritProjet = $('#TypeacteGabaritProjetUpload');
        var btnEraseProjet = '<?php echo $this->Form->button('Effacer', array('class'=>'btn btn-mini btn-danger pull-right', 'id'=>'erase_gabarit_projet', 'type'=>'button', 'title'=>'Effacer le gabarit')); ?>';
        gabaritProjet.hide();
        gabaritProjet.prop('disabled', true);
        gabaritProjet.before('<?php echo $this->Html->link($this->request->data['Typeacte']['gabarit_projet_name'], array('action'=>'downloadgabarit', $this->request->data['Typeacte']['id'], 'projet'), array('id'=>'link_gabarit_projet', 'download'=>$this->request->data['Typeacte']['gabarit_projet_name'])) ?>');
        gabaritProjet.after(btnEraseProjet);
        $('#erase_gabarit_projet').click(function () {
            gabaritProjet.prop('disabled', false);
            $('#link_gabarit_projet').remove();
            gabaritProjet.val(null);
            gabaritProjet.show();
            $('#erase_gabarit_projet').remove();
            $('#TypeacteGabaritProjetUploadErase').val('1');
        });
        <?php endif; ?>

        <?php if ($this->action == 'edit' && !empty($this->request->data['Typeacte']['gabarit_synthese'])) : ?>
        var gabaritSynthese = $('#TypeacteGabaritSyntheseUpload');
        var btnEraseSynthese = '<?php echo $this->Form->button('Effacer', array('class'=>'btn btn-mini btn-danger pull-right', 'id'=>'erase_gabarit_synthese', 'type'=>'button', 'title'=>'Effacer le gabarit')); ?>';
        gabaritSynthese.hide();
        gabaritSynthese.prop('disabled', true);
        gabaritSynthese.before('<?php echo $this->Html->link($this->request->data['Typeacte']['gabarit_synthese_name'], array('action'=>'downloadgabarit', $this->request->data['Typeacte']['id'], 'synthese'), array('id'=>'link_gabarit_synthese', 'download'=>$this->request->data['Typeacte']['gabarit_synthese_name'])) ?>');
        gabaritSynthese.after(btnEraseSynthese);
        $('#erase_gabarit_synthese').click(function () {
            gabaritSynthese.prop('disabled', false);
            $('#link_gabarit_synthese').remove();
            gabaritSynthese.val(null);
            gabaritSynthese.show();
            $('#erase_gabarit_synthese').remove();
            $('#TypeacteGabaritSyntheseUploadErase').val('1');
        });
        <?php endif; ?>

        <?php if ($this->action == 'edit' && !empty($this->request->data['Typeacte']['gabarit_acte'])) : ?>
        var gabaritActe = $('#TypeacteGabaritActeUpload');
        var btnEraseActe = '<?php echo $this->Form->button('Effacer', array('class'=>'btn btn-mini btn-danger pull-right', 'id'=>'erase_gabarit_acte', 'type'=>'button', 'title'=>'Effacer le gabarit')); ?>';
        gabaritActe.hide();
        gabaritActe.prop('disabled', true);
        gabaritActe.before('<?php echo $this->Html->link($this->request->data['Typeacte']['gabarit_acte_name'], array('action'=>'downloadgabarit', $this->request->data['Typeacte']['id'], 'acte'), array('id'=>'link_gabarit_acte', 'download'=>$this->request->data['Typeacte']['gabarit_acte_name'])) ?>');
        gabaritActe.after(btnEraseActe);
        $('#erase_gabarit_acte').click(function () {
            gabaritActe.prop('disabled', false);
            $('#link_gabarit_acte').remove();
            gabaritActe.val(null);
            gabaritActe.show();
            $('#erase_gabarit_acte').remove();
            $('#TypeacteGabaritActeUploadErase').val('1');
        });
        <?php endif; ?>
    });
</script>
