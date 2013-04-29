<script>
    $(document).ready(function(){
        var ma_valeur =  <?php  echo Configure::read('USE_PASTELL'); ?>;
        if (ma_valeur == 1) $('#affiche').show(); else $('#affiche').hide(); 
    });
</script>



<div class='spacer'> </div>
<?php  

    echo $this->Form->create('Connecteur',array('url'=>'/connecteurs/makeconf/pastell')); 

    $notif = array(1 => 'Oui', 0=>'Non');
    echo $this->Form->input('use_pastell', array('before'  => '<label>Utilisation du PASTELL</label>',
                                               'legend'  => false,
                                               'type'    => 'radio',
                                               'options' => $notif,
                                               'value' => Configure::read('USE_PASTELL'),
                                               'div'     => false,
                                               'default' => 0,
                                               'label'   => false,
                                               'onClick'=>"if(this.value==1) $('#affiche').show(); else $('#affiche').hide(); " ));
 ?>
    <div class='spacer'> </div>
    <div id='affiche'>
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
