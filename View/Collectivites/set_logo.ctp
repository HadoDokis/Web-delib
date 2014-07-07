<h2>Edition du logo de la Collectivité</h2>
<?php echo $this->Form->create('Collectivites', array('url' => $this->webroot . 'collectivites/setLogo', 'type' => 'file')); ?>
<?php echo $this->Form->input('Collectivite.logo', array('label' => 'Fichier image (format png, jpg ou jpeg)', 'type' => 'file')) ?>
<div class="submit">
    <?php $this->Html2->boutonsSaveCancel('', "index", "Ajouter le logo", 'Sauvegarder logo'); ?>
</div>

<?php echo $this->Form->end(); ?>

<style>
    label {
        text-align: left;
        width: auto;
        float: none;
    }
</style>