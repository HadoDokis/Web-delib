<script>
    $(document).ready(function(){
        var ma_valeur =  <?php  echo Configure::read('USE_PARAPH'); ?>;
        if (ma_valeur == 1) $('#parapheur').show(); else $('#parapheur').hide(); 
    });
</script>
<div class='spacer'> </div>
<?php  

    echo $this->Form->create('Connecteur',array('url'=>'/connecteurs/makeconf/iparapheur', 'type'=>'file' )); 

    $notif = array(1 => 'Oui', 0=>'Non');
    echo $this->Form->input('use_paraph', array('before'  => '<label for="UseParapheur">Utilisation du i-parapheur électronique</label>',
                                               'legend'  => false,
                                               'type'    => 'radio',
                                               'options' => $notif,
                                               'value' => Configure::read('USE_PARAPH'),
                                               'div'     => false,
                                               'default' => 0,
                                               'label'   => false,
                                               'onClick'=>"if(this.value==1) $('#parapheur').show(); else $('#parapheur').hide(); " ));
 ?>
    <div class='spacer'> </div>
    <div id='parapheur'>
    <fieldset>
        <legend>Adresse de la plateforme I-Parapheur</legend>
<?php  
        echo $this->Form->input('wsaction', 
                                array('type' => 'text', 
                                      "placeholder"=>"Exemple : https://parapheur.demonstrations.adullact.org/alfresco", 
                                      'label' => 'WsAction : ' , 
                                      'value' => Configure::read('WSACTION')));
         echo $this->Form->input('wsto',
                                array('type' => 'text',
                                      "placeholder"=>"Exemple : https://parapheur.demonstrations.adullact.org/ws-iparapheur",
                                      'label' => 'WsTo : ' ,
                                      'value' => Configure::read('WSTO'))); 
?>


    </fieldset>
    <fieldset>
        <legend>Récupération des informations pour l'authentification</legend>
<?php
    echo $this->Form->input('certificat', array('type' => 'file'));
    echo $this->Form->input('passphrase', array('type' => 'text', 
                                              "placeholder"=>"fourni avec votre certificat", 
                                              'value' => Configure::read('PASSPHRASE'),
                                              'label' => 'Mot de passe'));
    echo $this->Form->input('login',
                             array('type' => 'text',
                                   "placeholder"=>"Exemple : www.parapheur.org",
                                   'label'  => false,
                                   'before' => '<label>nom d\'utilisateur</label> ' ,
                                   'value' => Configure::read('HTTPAUTH'))); 

    echo $this->Form->input('pass',
                            array('type' => 'text',
                                  "placeholder"=>"mot de passe du certificat",
                                  'label' => false,
                                      'value' => Configure::read('HTTPPASSWD'),
                                      'before' => '<label>Mot de passe</label>')); 

?>
    </fieldset>
    <div class='spacer'> </div>
<?php
    echo $this->Form->input('typetech', array('before'  => '<label for="UseProxy">Type technique : </label>', 
                                               'legend'  => false,  
                                               'type'    => 'text', 
                                               'label' => 'Type technique : ' ,
                                               'value' => Configure::read('TYPETECH'),
                                               'div'     => false, 
                                               'label'   => false));
?>
</div>
    <div class='spacer'> </div>
<?php
    echo $this->Form->submit('Configurer', array('class' => "btn btn-primary"));
    echo $this->Form->end();
?>
