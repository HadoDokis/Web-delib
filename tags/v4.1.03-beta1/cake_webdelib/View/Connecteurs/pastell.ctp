<div class='spacer'> </div>
<style type="text/css">
    div.input.radio fieldset label { text-align: left; padding-left: 5px; width:auto;}
    div.input.radio {padding-left: 0px;}
    div.input.radio input[type="radio"]{margin-left: 25px;}
</style>
<?php  

    echo $this->Form->create('Connecteur',array('url'=>'/connecteurs/makeconf/pastell')); 

    $notif = array('true' => 'Oui','false'=>'Non');
    echo $this->Form->input('use_pastell', array('before'  => '',
                                               'legend'  => 'Utilisation du PASTELL',
                                               'type'    => 'radio',
                                               'options' => $notif,
                                               'value' => Configure::read('USE_PASTELL')?'true':'false',
                                               'div'     => true,
                                               'default' => 'false',
                                               'label'   => true,
                                               'onClick'=>"if(this.value=='true') $('#affiche').show(); else $('#affiche').hide(); " ));
 ?>
    <div class='spacer'> </div>
    <div id='affiche' <?php  echo Configure::read('USE_PASTELL')===false?'style="display: none;"':''; ?>>
    <fieldset>
        <legend>Paramètrage de PASTELL</legend>
<?php  
    echo $this->Form->input('pastell_host', 
                             array('type' => 'text', 
                                   "placeholder"=>"Exemple : pastell.maville.fr", 
                                   'label' => 'Serveur PASTELL : ' , 
                                   'value' => Configure::read('PASTELL_HOST'))).'<br>';
    echo $this->Form->input('pastell_login',
                            array('type' => 'text',
                                  "placeholder"=>"nom d'utilisateur",
                                  'label' => false,
                                  'value' => Configure::read('PASTELL_LOGIN'),
                                  'before' => '<label>Nom d\'utilisateur</label>')).'<br>'; 
    echo $this->Form->input('pastell_pwd',
                            array('type' => 'text',
                                  "placeholder"=>"mot de passe",
                                  'label' => false,
                                  'value' => Configure::read('PASTELL_PWD'),
                                  'before' => '<label>Mot de passe</label>')).'<br>'; 
    echo $this->Form->input('pastell_type',
                            array('type' => 'text',
                                  "placeholder"=>"exemple : actes",
                                  'label' => false,
                                  'value' => Configure::read('PASTELL_TYPE'),
                                  'before' => '<label>type technique</label>')); 


?>
    </fieldset>
</div>
    <div class='spacer'> </div>
<?php
    echo $this->Html2->boutonsSaveCancel('','/connecteurs/index');
    echo $this->Form->end();
?>
