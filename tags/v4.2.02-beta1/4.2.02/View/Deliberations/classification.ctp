<h2>Choisir la classification</h2>
<div id="attribute_list">
<?php
    if (!isset($_GET['id'])) {
        if(isset($classification))
        foreach ($classification as $key=>$value) {
	        $val=addslashes($value);
                if(is_int($key) && !is_string($key))
                echo $key.' - '.$value;
                    else
	        echo $this->Html->link($key.' - '.$value,'#add',array('onclick'=>"javascript:returnChoice('$key - $val','$key');", 'id'=>$key, 'name'=>$key, 'value'=>$key));
	        echo '<br/>';
        }
    }
    else {
    	$id = $_GET['id'];
        if(isset($classification))
    	foreach ($classification as $key=>$value) {
	        $val=addslashes($value);
                 if(is_int($key) && !is_string($key))
                echo $key.' - '.$value;
                    else
	       echo $this->Html->link($key.' - '.$value,'#add',array('onclick'=>"javascript:return_choice_lot('$key - $val','$key',$id);", 'id'=>$key, 'name'=>$key, 'value'=>$key));
	        echo '<br/>';
    	}
    }
    if(!isset($classification))echo "Pas de classification disponible.</div><br />"
?>
<br/>
<?php echo $this->Html->link('Fermer la fenêtre','#add',array('onclick'=>"javascript:window.close();")); ?>
</div>