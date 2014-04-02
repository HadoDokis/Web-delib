<div class='spacer'> </div>
<style type="text/css">
    div.input.radio fieldset label { text-align: left; padding-left: 5px; width:auto;}
    div.input.radio {padding-left: 0px;}
    div.input.radio input[type="radio"]{margin-left: 25px;}
</style>
<?php  
    echo $this->Form->create('Connecteur',array('url'=>'/connecteurs/makeconf/s2low', 'type'=>'file' ));  
    $notif = array('true' => 'Oui', 'false' => 'Non');
    echo $this->Form->input('use_s2low', array('before'  => '',
                                               'legend'  => 'Utilisation de s2low',
                                               'type'    => 'radio',
                                               'options' => $notif,
                                               'value' => Configure::read('USE_S2LOW')?'true':'false',
                                               'div'     => true,
                                               'default' => 'false',
                                               'label'   => true,
                                               'onClick'=>"if(this.value=='true') $('#affiche').show(); else $('#affiche').hide(); " ));
    ?>
    <div class='spacer'> </div>
    <div id='affiche' <?php  echo Configure::read('USE_S2LOW')===false?'style="display: none;"':''; ?>>
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
    $notif = array('true' => 'Oui', 'false' =>'Non');
    echo $this->Form->input('use_proxy', array('before'  => '<label for="UseProxy">Utilisation d\'un proxy</label>', 
                                               'legend'  => false,  
                                               'type'    => 'radio', 
                                               'options' => $notif, 
                                               'value' => Configure::read('USE_PROXY')?'true':'false',
                                               'div'     => false, 
                                               'default' => 'false',
                                               'label'   => false,  
                                               'onClick'=>"if(this.value=='true') $('#proxy_host').show(); else $('#proxy_host').hide(); " ));
?>
    <div class='spacer'> </div>
    <div id="proxy_host" style="display: none;">
<?php
    
    echo $this->Form->input('proxy_host', array('type'        => 'text', 
                                                'placeholder' => 'Exemple : http://x.x.x.x:8080', 
                                                'value' => Configure::read('HOST_PROXY'),
                                                'label'       => 'Adresse du proxy')).'<br />';
   
?> </div>
    </fieldset>
    <fieldset>
        <legend>Utilisation du mail sécurisé</legend>
<?php
    $notif = array('true' => 'Oui', 'false' =>'Non');
    echo $this->Form->input('use_mails', array('before'  => '<label for="UseProxy">Utilisation du mail sécurisé</label>', 
                                               'legend'  => false,  
                                               'type'    => 'radio', 
                                               'options' => $notif, 
                                               'value' => Configure::read('USE_MAIL_SECURISE')?'true':'false',
                                               'div'     => false, 
                                               'default' => 'false',
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
    echo $this->Html2->boutonsSaveCancel('','/connecteurs/index');
    echo $this->Form->end();
?>
