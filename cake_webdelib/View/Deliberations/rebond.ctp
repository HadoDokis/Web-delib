<?php
echo $this->Html->tag('h2', __('Envoyer le projet à un ou plusieurs utilisateurs', true));
$this->Html->addCrumb('Délibération', array('action' => 'traiter', $delib_id));
$this->Html->addCrumb('Envoyer le projet à un utilisateur');
$options = array(
    'detour' => 'Envoyer (sans retour) <i class="fa fa-mail-forward"></i>',
    'retour' => 'Aller-retour <i class="fa fa-retweet"></i>',
    'validation' => 'Validation finale <i class="fa fa-legal"></i>');
$attributes = array('legend' => false, 'value' => 2);
echo $this->BsForm->create('Insert', array('url' => array('controller' => 'deliberations', 'action' => 'rebond', $delib_id), 'type' => 'post'));
$affiche = $this->BsForm->radio('etape_choisie', array(3 => 'Collaboratif [ET]', 2 => 'Concurrent [OU]', 1 => 'Simple'), $attributes);
$affiche .= $this->BsForm->select('users_id', $users, array('multiple' => true,'placeholder' => __('Utilisateurs')));

$affiche = $this->Html2->div('panel-heading', __('Sélection du ou des destinataires'))
        . $this->Html2->div('panel-body', $affiche);
echo $this->Html2->div('panel panel-default', $affiche);

/* echo  $this->Html->tag('div', 'Titre', array('class' => 'panel-heading'));
  echo $this->Html->tag('div', $affiche, array('class' => 'panel-body')); */
$attributes = array('legend' => false, 'value' => 'retour');
$radio = '';
if ($typeEtape == CAKEFLOW_COLLABORATIF) {
    $radio .= $this->BsForm->hidden('option', array('value' => 'retour'));
    $radio .= $this->BsForm->radio('option_disabled', $options, array_merge($attributes, array('disabled' => true)));
    $radio .= '<div class="spacer"></div>';
    $radio .= $this->Html->para('profil', 'Note : pour les étapes collaboratives (ET), l\'aller-retour est la seule possibilité.', array('style' => 'float: left;text-align: left;'));
} else {
    $radio .= $this->BsForm->radio('option', $options, $attributes);
}
$radio = $this->Html2->div('panel-heading', __('Sélection du type d\'envoie'))
        .$this->Html2->div('panel-body', $radio);
echo $this->Html2->div('panel panel-default', $radio);

echo $this->Html2->btnSaveCancel('', array('action' => 'traiter', $delib_id));

echo $this->BsForm->end();
?>
<script type="application/javascript">
    $(document).ready(function(){
    $("#InsertEtapeChoisie3").on('change', function(){
    $('#InsertUsersId').select2({ width: "100%"});
    });
    $("#InsertEtapeChoisie2").on('change', function(){
    $('#InsertUsersId').select2({ width: "100%"});
    });
    $("#InsertEtapeChoisie1").on('change', function(){
    $("#InsertUsersId").select2("val", "");
    $('#InsertUsersId').select2({ width: "100%",maximumSelectionSize: 1 });
    });
    $('#InsertUsersId').select2({
    width: "100%",
    });    
    });
</script>
