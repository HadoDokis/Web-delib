<h2>Nouveau modèle : </h2>

<?php echo $this->Form->create('Model',array('url'=>'/models/add','type'=>'post')); ?>
<?php  echo $this->Form->input('Model.modele', array('label'=>'Libellé'));
/**
 * TODO : Import du fichier du modèle dans la même vue
 */
?>

<div class="submit">
    <?php $this->Html2->boutonsAddCancel(); ?>
</div>
<?php echo $this->Form->end(); ?>

<style>
    label{
        padding-top:6px;
        text-align: left;
        width: auto; 
   }
</style>