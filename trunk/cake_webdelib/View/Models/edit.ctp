<?php
if ($this->action == 'add'){
    echo $this->Html->tag('h2', 'Nouveau modèle');
    $url = array('action'=>$this->action);
}elseif($this->action == 'edit'){
    echo $this->Html->tag('h2', 'Modification du modèle '.$this->data['Model']['modele']);
    $url = array('action'=>$this->action, $this->data['Model']['id']);
}

echo $this->Form->create('Model',array('url'=>$url,'type'=>'file'));

echo $this->Form->input('Model.modele', array('label'=>'Libellé', 'placeholder'=>'Nom du modèle'));
//echo $this->Form->input('Modeltype.id', array('label'=>'Type de modèle', 'type'=>'select'));
if ($this->action == 'edit' && !empty($this->data['Model']['name'])){
echo $this->Html->tag('a' ,'Changer de document odt', array('id'=>'replaceDocument'));
?>

<script type="application/javascript">
    $(document).ready(function(){
        $('#fileUpload').hide();
        $('#replaceDocument').click(function(){
            $('#fileUpload').show();
            $('#replaceDocument').remove();
            return false;
        });
    })
</script>
<?php } ?>
<span id="fileUpload">
<?php
echo $this->Form->input('template', array('label'=>'Fichier', 'type'=>'file'));
?>
</span>

<hr>
<div class="submit">
<?php $this->Html2->boutonsAddCancel('', array('action'=>'index')); ?>
</div>
<?php echo $this->Form->end(); ?>

<style>
    #replaceDocument{
        cursor: pointer;
    }
    label{
        padding-top:6px;
        text-align: left;
        width: 150px;
    }
    div.input{
        margin: 10px;
    }
    input[type="text"]{
        margin-bottom: 0;
    }
</style>