<?php echo $html->css('webdelib'); ?>
<?php echo $javascript->link('utils'); ?>
<br/><br/><br/><br/><br/>
<div id="centre">
<?php
echo $session->flash();
echo $content_for_layout;
?> 
</div>