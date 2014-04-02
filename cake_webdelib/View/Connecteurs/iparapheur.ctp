<div class='spacer'> </div>
<style type="text/css">
    div.input.radio fieldset label { text-align: left; padding-left: 5px; width:auto;}
    div.input.radio {padding-left: 0px;}
    div.input.radio input[type="radio"]{margin-left: 25px;}
</style>
<?php
    echo $this->Form->create('Connecteur',array('url'=>'/connecteurs/makeconf/iparapheur', 'type'=>'file' )); 
    $notif = array('true' => 'Oui', 'false' => 'Non');
    echo $this->Form->input('use_paraph', array('before'  => '',
                                               'legend'  => "Utilisation du i-parapheur électronique",
                                               'type'    => 'radio',
                                               'options' => $notif,
                                               'value' => Configure::read('USE_PARAPH')?'true':'false',
                                               'div'     => true,
                                               'default' => 'false',
                                               'label'   => true,
                                               'onClick'=>"if(this.value=='true') $('#affiche').show(); else $('#affiche').hide(); " ));
 ?>
    <div class='spacer'> </div>
    <div id='affiche' <?php  echo Configure::read('USE_PARAPH')===false?'style="display: none;"':''; ?>>
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
    echo $this->Html2->boutonsSaveCancel('','/connecteurs/index');
    echo $this->Form->end();
?>
