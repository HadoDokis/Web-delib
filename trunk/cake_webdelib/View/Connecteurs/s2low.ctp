<script>
    $(document).ready(function(){
        var ma_valeur =  <?php  echo Configure::read('USE_S2LOW'); ?>;
        if (ma_valeur == 1) $('#parapheur').show(); else $('#parapheur').hide(); 
    });
</script>



<div class='spacer'> </div>
<?php  
    echo $this->Form->create('Connecteur',array('url'=>'/connecteurs/makeconf/s2low', 'type'=>'file' ));  

    $notif = array(1 => 'Oui', 0=>'Non');
    echo $this->Form->input('use_s2low', array('before'  => '<label for="UseS2low">Utilisation de s2low</label>',
                                               'legend'  => false,
                                               'type'    => 'radio',
                                               'options' => $notif,
                                               'value' => Configure::read('USE_S2LOW'),
                                               'div'     => false,
                                               'default' => 0,
                                               'label'   => false,
                                               'onClick'=>"if(this.value==1) $('#parapheur').show(); else $('#parapheur').hide(); " ));


?>
    <div class='spacer'> </div>
    <div id='parapheur'>
    <fieldset>
        <legend>Choix de la plateforme S2LOW</legend>
<?php  
        echo $this->Form->input('hostname', 
                                array('type' => 'text', 
                                      "placeholder"=>"Exemple : www.s2low.org", 
                                      'label' => false , 
                                      'value' => Configure::read('HOST'),
                                      'before' => 'https://')); ?>
    </fieldset>
    <fieldset>
        <legend>Récupération du certificat électronique</legend>
<?php
    echo $this->Form->input('certificat', array('type' => 'file'));
    echo $this->Form->input('password', array('type' => 'text', 
                                              "placeholder"=>"fourni avec votre certificat", 
                                              'value' => Configure::read('PASSWORD'),
                                              'label' => 'Mot de passe'  ));
?>
    </fieldset>
    <div class='spacer'> </div>
    <fieldset>
        <legend>Paramètrage du proxy</legend>
<?php
    $notif = array(1 => 'Oui', 0=>'Non');

    echo $this->Form->input('use_proxy', array('before'  => '<label for="UseProxy">Utilisation d\'un proxy</label>', 
                                               'legend'  => false,  
                                               'type'    => 'radio', 
                                               'options' => $notif, 
                                               'value' => Configure::read('USE_PROXY'),
                                               'div'     => false, 
                                               'default' => 0,
                                               'label'   => false,  
                                               'onClick'=>"if(this.value==1) $('#proxy_host').show(); else $('#proxy_host').hide(); " ));
?>
    <div class='spacer'> </div>
<?php
    echo ('<div id="proxy_host" style="display: none;" >');
    echo $this->Form->input('proxy_host', array('type'        => 'text', 
                                                'placeholder' => 'Exemple : http://x.x.x.x:8080', 
                                                'value' => Configure::read('HOST_PROXY'),
                                                'label'       => 'Adresse du proxy')).'<br />';
    echo ('</div>');
?>
    </fieldset>
    <fieldset>
        <legend>Utilisation du mail sécurisé</legend>
<?php
    echo $this->Form->input('use_mails', array('before'  => '<label for="UseProxy">Utilisation du mail sécurisé</label>', 
                                               'legend'  => false,  
                                               'type'    => 'radio', 
                                               'options' => $notif, 
                                               'value' => Configure::read('USE_MAIL_SECURISE'),
                                               'div'     => false, 
                                               'default' => 0,
                                               'label'   => false));
?>
    <div class='spacer'> </div>
<?php
    echo $this->Form->input('mails_password', array('type' => 'text', 
                                              "placeholder"=>"fourni avec votre certificat", 
                                              'value' => Configure::read('PASSWORD_MAIL_SECURISE'),
                                              'label' => 'Mot de passe'  ));
?>
    </fieldset>
    </div>
<?php
    echo $this->Form->submit('Configurer', array('class' => "btn btn-primary"));

    echo $this->Form->end();
?>
