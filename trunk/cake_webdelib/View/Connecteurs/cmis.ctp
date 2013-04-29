<script>
    $(document).ready(function(){
        var ma_valeur =  <?php  echo Configure::read('USE_GED'); ?>;
        if (ma_valeur == 1) $('#affiche').show(); else $('#affiche').hide(); 
    });
</script>

<div class='spacer'> </div>
<?php  

    echo $this->Form->create('Connecteur',array('url'=>'/connecteurs/makeconf/cmis')); 

    $notif = array(1 => 'Oui', 0=>'Non');
    echo $this->Form->input('use_ged', array('before'  => '<label>Utilisation d\'une GED</label>',
                                               'legend'  => false,
                                               'type'    => 'radio',
                                               'options' => $notif,
                                               'value' => Configure::read('USE_GED'),
                                               'div'     => false,
                                               'default' => 0,
                                               'label'   => false,
                                               'onClick'=>"if(this.value==1) $('#affiche').show(); else $('#affiche').hide(); " ));
 ?>
    <div class='spacer'> </div>
    <div id='affiche'>
    <fieldset>
        <legend>Paramètrage de la GED</legend>
<?php  
    echo $this->Form->input('ged_url', 
                             array('type' => 'text', 
                                   "placeholder"=>"Exemple : pastell.maville.fr", 
                                   'label' => 'Serveur de la GED : ' , 
                                   'value' => Configure::read('GED_URL'))).'<br>';
    echo $this->Form->input('ged_login',
                            array('type' => 'text',
                                  "placeholder"=>"nom d'utilisateur",
                                  'label' => false,
                                  'value' => Configure::read('GED_LOGIN'),
                                  'before' => '<label>Nom d\'utilisateur</label>')).'<br>'; 
    echo $this->Form->input('ged_passwd',
                            array('type' => 'text',
                                  "placeholder"=>"mot de passe",
                                  'label' => false,
                                  'value' => Configure::read('GED_PASSWD'),
                                  'before' => '<label>Mot de passe</label>')).'<br>'; 
    echo $this->Form->input('ged_repo',
                            array('type' => 'text',
                                  "placeholder"=>"exemple : /Sites/Web-delib",
                                  'label' => false,
                                  'value' => Configure::read('GED_REPO'),
                                  'before' => '<label>Répertoire de stockage</label>')); 
?>
    </fieldset>
</div>
    <div class='spacer'> </div>
<?php
    echo $this->Html2->boutonsSaveCancel('','/connecteurs/index');
    echo $this->Form->end();
?>
