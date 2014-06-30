<?php echo $this->Html->css('webdelib'); ?>
<?php echo $this->Html->script('utils'); ?>
<br/><br/><br/><br/><br/>
<div id="centre">
<?php
echo $this->Session->flash();
echo $content_for_layout;
?> 
</div>