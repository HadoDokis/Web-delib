<script>
    window.onload = initAffichage;

    /*
     * Affiche ou masque les options en fonction du type d'info sup
     */
    function afficheOptions(typeInfoSup) {
        var val = $(typeInfoSup).val();

        /* On masque toutes les options */
        $("#val_initiale").hide();
        $("#val_initiale_boolean").hide();
        $("#val_initiale_date").hide();
        if ($("#recherche")) $("#recherche").hide();
        $("#gestionListe").hide();

        /* si le choix est vide : on sort */
        if ((val.length == 0) || (val == null)) return;

        /* on affiche en fonction du type d'info sup */
        switch (val) {
            case "text":
                $("#val_initiale").show();
                if ($("#recherche")) $("#recherche").show();
                break;
            case "richText":
                $("#val_initiale").show();
                if ($("#recherche")) $("#recherche").show();
                break;
            case "date":
                $("#val_initiale_date").show();
                if ($("#recherche")) $("#recherche").show();
                break;
            case "boolean":
                $("#val_initiale_boolean").show();
                if ($("#recherche")) $("#recherche").show();
                break;
            case "list":
                if ($("#recherche")) $("#recherche").show();
                $("#gestionListe").show();
                break;
            case "listmulti":
                if ($("#recherche")) $("#recherche").show();
                $("#gestionListe").show();
                break;
            case "odtFile":
                break;
            case "file":
                break;
        }
    }

    function initAffichage() {
        afficheOptions($("#selectTypeInfoSup"));
    }
</script>

<?php
echo $this->Html->script('calendrier.js');

echo $this->Bs->tag('h3', $titre);

echo $this->BsForm->create('Infosupdef', array('url' => array('action' => $this->request->action), 'type' => 'post', 'name' => 'infoSupForm'));
?>
<div class="required">
    <?php echo $this->BsForm->input('Infosupdef.nom', array('label' => 'Nom <abbr title="obligatoire">*</abbr>', 'size' => '40', 'title' => 'Nom affiché dans le formulaire d\'édition des projets')); ?>
</div>
<div class="required">
    <?php echo $this->BsForm->input('Infosupdef.commentaire', array('label' => 'Commentaire', 'size' => '80', 'title' => 'Bulle d\'information affiché dans le formulaire d\'édition des projets')); ?>
</div>
<div class="required">
    <?php echo $this->BsForm->input('Infosupdef.code', array('label' => 'Code <abbr title="obligatoire">*</abbr>', 'size' => '40', 'title' => 'Code unique utilisé pour les éditions (pas d\'espace ni de caractère spécial)'), false, false); ?>
</div>
<div class="required">
    <?php
    $htmlAttributes['disabled'] = false;
    $empty = false;
    if (($this->action == 'edit') && !$Infosupdef->isDeletable($this->request->data['Infosupdef']['id'])) {
        $htmlAttributes['disabled'] = true;
        echo $this->BsForm->hidden('Infosupdef.type');
        $empty = true;
    }
    echo $this->BsForm->input('Infosupdef.type', array('label' => 'Type <abbr title="obligatoire">*</abbr>', 'options' => $types, 'id' => 'selectTypeInfoSup', 'onChange' => "afficheOptions(this);", 'disabled' => $htmlAttributes['disabled'], 'showEmpty' => $empty,'help'=>'Note : la gestion des éléments de la liste est accessible &agrave; partir de la liste des informations suppl&eacute;mentaires.'));
    ?>
</div>
<div class="required" id="val_initiale">
    <?php echo $this->BsForm->input('Infosupdef.val_initiale', array('label' => 'Valeur initiale', 'size' => '80', 'title' => 'Valeur initiale lors de la création d\'un projet')); ?>
</div>
<div class="required" id="val_initiale_boolean">
    <?php echo $this->BsForm->input('Infosupdef.val_initiale_boolean', array('label' => 'Valeur initiale', 'options' => $listEditBoolean)); ?>
</div>
<div class="required" id="val_initiale_date">
    <?php 
    echo $this->BsForm->input('Infosupdef.val_initiale_date', array('div' => false, 'label' => 'Valeur initiale', 'id' => 'InfosupdefValInitialeDate', 'size' => '9', 'title' => 'Valeur initiale lors de la création d\'un projet')).
    $this->Html->link($this->Html->image("calendar.png", array('style' => "border:0;")), "javascript:show_calendar('infoSupForm.InfosupdefValInitialeDate', 'f');", array('escape' => false), false); ?>
</div>
<?php
if ($this->request->data['Infosupdef']['model'] == 'Deliberation')
    echo $this->BsForm->checkbox('Infosupdef.recherche', array( 'label' => 'Inclure dans la recherche'));
else
    echo $this->BsForm->hidden('Infosupdef.recherche', array('value' => false));

echo $this->BsForm->checkbox('Infosupdef.actif', array('label' => 'information active'));
echo $this->Html->tag('div', '', array('class' => 'spacer'));
?>

<?php echo $this->BsForm->input('Profil', array('options' => $profils, 'multiple' => true,
    'label' => 'Profils autorisés', 'title' => 'l\'information supplémentaire ne sera utilisable que pour les profils sélectionnés dans cette liste')); ?>

<div class="spacer"></div>
<div class="submit">
    <?php
    echo $this->BsForm->hidden('Infosupdef.id');
    echo $this->BsForm->hidden('Infosupdef.model');
    echo $this->BsForm->hidden('Typeacte.id');
    echo $this->Html2->btnSaveCancel('', $lienRetour);
    ?>
</div>
<?php echo $this->BsForm->end(); ?>
<script type="text/javascript">
    $(document).ready(function(){
       $('#ProfilProfil').select2({
           width: 'element',
           placeholder : 'Aucun profil'
       });
    });
</script>