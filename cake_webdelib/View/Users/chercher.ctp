<?php
/**
 * Application: webdelib / Adullact.
 * Date: 16/12/13
 * @author: Florian Ajir <florian.ajir@adullact.org>
 * @license CeCiLL V2 <http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html>
 */
echo $this->Html->tag('h1', 'Rechercher un utilisateur');
echo $this->Form->create('User');

echo $this->Html->tag('fieldset', null);
echo $this->Html->tag('legend', 'Par son nom');
echo $this->Form->input('id', array('type'=>'select', 'label'=>"Nom de l'utilisateur", 'class'=>'select2', 'options'=>$options));
echo $this->Html->tag('br');
echo $this->Html->tag('div', null, array('class'=>'btn-group'));
echo $this->Form->button('<i class="fa fa-search"></i> Voir', array('name'=>'action', 'value'=>'view', 'class'=>'btn'));
echo $this->Form->button('<i class="fa fa-edit"></i> Editer', array('name'=>'action', 'value'=>'edit', 'class'=>'btn'));
echo $this->Html->tag('/div', null);
echo $this->Html->tag('/fieldset', null);
echo $this->Form->end();
echo $this->Html->tag('br');
echo $this->Html->tag('div', null, array('style'=>'text-align:center'));
echo $this->Html->link('<i class="fa fa-list"></i> Tout voir', array('action'=>'index'), array('class'=>'btn btn-inverse', 'escape'=>false, 'style'=>'text-align:center'));
echo $this->Html->tag('/div', null);
?>
<script type="application/javascript">
    $(document).ready(function(){
       $('#UserId').select2({
           'width': '50%'
       });
    });
</script>
<style>
    label{
        float: none;
        text-align: left;
        width: auto;
    }
    form#UserChercherForm{
        padding: 25px;
        border: 2px dotted #ccc;
    }
</style>