<script>
    $(document).ready(function(){
        var ma_valeur =  <?php  echo Configure::read('USE_ASALAE'); ?>;
        if (ma_valeur == 1) $('#affiche').show(); else $('#affiche').hide(); 
    });
</script>

<div class='spacer'> </div>
<?php  
    echo $this->Form->create('Connecteur',array('url'=>'/connecteurs/makeconf/asalae')); 

    $notif = array(1 => 'Oui', 0=>'Non');
    echo $this->Form->input('use_asalae', array('before'  => '<label for="UseASALAE">Utilisation de ASALAE</label>',
                                               'legend'  => false,
                                               'type'    => 'radio',
                                               'options' => $notif,
                                               'value' => Configure::read('USE_ASALAE'),
                                               'div'     => false,
                                               'default' => 0,
                                               'label'   => false,
                                               'onClick'=>"if(this.value==1) $('#affiche').show(); else $('#affiche').hide(); " ));
?>
    <div class='spacer'> </div>
    <div id='affiche'>
    <fieldset>
        <legend>Paramètrage de AS@LAE</legend>
<?php  
    echo $this->Form->input('asalae_wsdl', 
                             array('type' => 'text', 
                                   "placeholder"=>"WSDL de as@lae", 
                                   'label' => 'WSDL de AS@LAE : ' , 
                                   'value' => Configure::read('ASALAE_WSDL'))).'<br>';
    echo $this->Form->input('siren_archive',
                            array('type' => 'text',
                                  "placeholder"=>"SIREN Archivage",
                                  'label' => false,
                                  'value' => Configure::read('SIREN_ARCHIVE'),
                                  'before' => '<label>SIREN Archivage</label>')).'<br>'; 
    echo $this->Form->input('numero_agrement',
                            array('type' => 'text',
                                  "placeholder"=>"Numéro agrément",
                                  'label' => false,
                                  'value' => Configure::read('NUMERO_AGREMENT'),
                                  'before' => '<label>Numéro de l\'agrément</label>')).'<br>'; 
    echo $this->Form->input('identifiant_versant',
                            array('type' => 'text',
                                  "placeholder"=>"",
                                  'label' => false,
                                  'value' => Configure::read('IDENTIFIANT_VERSANT'),
                                  'before' => '<label>Identifiant du versant</label>')).'<br>'; 
    echo $this->Form->input('mot_de_passe',
                            array('type' => 'text',
                                  "placeholder"=>"exemple : actes",
                                  'label' => false,
                                  'value' => Configure::read('MOT_DE_PASSE'),
                                  'before' => '<label>Mot de passe du versant</label>')).'<br>'; 


?>
    </fieldset>
</div>
    <div class='spacer'> </div>
<?php
    echo $this->Form->submit('Configurer', array('class' => "btn btn-primary"));
    echo $this->Form->end();
?>
