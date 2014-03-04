<script>
    window.onload = initAffichageInfosElus;

    function afficheInfosElus(typeActeur) {
        divElement = document.getElementById("infoElus");

        if ((typeActeur.value.length == 0) || (typeActeur.value == null)) {
            divElement.style.display = 'none';
        } else {
            if (typeActeur.options[typeActeur.selectedIndex].className == '1')
                divElement.style.display = '';
            else
                divElement.style.display = 'none';
        }
    }

    function initAffichageInfosElus() {
        selectTypeActeur = document.getElementById("ActeurTypeacteurId");
        afficheInfosElus(selectTypeActeur);
    }
    $(document).ready(function () {
        $('#ServiceService').select2({
            allowClear: true,
            placeholder: 'Aucun service',
            width: 'resolve',
            formatSelection: function (object, container) {
                return $.trim(object.text);
            }
        });

        $("#ActeurSuppleantId, #ActeurTypeacteurId").select2({
            width: "resolve",
            allowClear: true,
            placeholder: "Aucune sélection"
        });
    });
</script>

<?php
echo $this->Html->script('calendrier.js');

if ($this->Html->value('Acteur.id')) {
    echo "<h2>Modification d'un acteur</h2>";
    echo $this->Form->create('Acteur', array('url' => array('controller' => 'acteurs', 'action' => 'edit', $this->Html->value('Acteur.id')), 'type' => 'post', 'name' => 'ActeurForm'));
} else {
    echo "<h2>Ajout d'un acteur</h2>";
    echo $this->Form->create('Acteur', array('url' => array('controller' => 'acteurs', 'action' => 'add'), 'type' => 'post', 'name' => 'ActeurForm'));
}
?>
<div class="demi">
    <fieldset>
        <legend>Identité</legend>
        <?php echo $this->Form->input('Acteur.salutation', array('label' => 'Civilité')); ?>
        <br/>
        <?php echo $this->Form->input('Acteur.nom', array('label' => 'Nom <abbr title="obligatoire">*</abbr>')); ?>
        <br/>
        <?php echo $this->Form->input('Acteur.prenom', array('label' => 'Prénom <abbr title="obligatoire">*</abbr>')); ?>
        <br/>
        <?php echo $this->Form->input('Acteur.titre', array('label' => 'Titre')); ?>
    </fieldset>
    <fieldset>
        <legend>Adresse postale</legend>
        <?php echo $this->Form->input('Acteur.adresse1', array('label' => 'Adresse 1')); ?>
        <br/>
        <?php echo $this->Form->input('Acteur.adresse2', array('label' => 'Adresse 2')); ?>
        <br/>
        <?php echo $this->Form->input('Acteur.cp', array('label' => 'Code postal')); ?>
        <br/>
        <?php echo $this->Form->input('Acteur.ville', array('label' => 'Ville')); ?>
        <br/>
    </fieldset>
    <fieldset>
        <legend>Contacts</legend>
        <?php echo $this->Form->input('Acteur.telfixe', array('label' => 'Téléphone fixe')); ?>
        <br/>
        <?php echo $this->Form->input('Acteur.telmobile', array('label' => 'Téléphone mobile')); ?>
        <br/>
        <?php echo $this->Form->input('Acteur.email', array('label' => 'Email')); ?>
    </fieldset>
</div>

<div class="demi">
    <fieldset>
        <legend>Suppléant</legend>
        <?php
        if ($this->Html->value('Acteur.suppleant_id'))
            $suppleant_id = $this->Html->value('Acteur.suppleant_id');
        else
            $suppleant_id = null;
        echo $this->Form->input('Acteur.suppleant_id', array('empty' => true, 'label' => 'Élus', 'selected' => $suppleant_id, 'options' => $acteurs));
        ?>
    </fieldset>
    <fieldset>
        <legend>Type</legend>
        <?php echo $this->Form->label('Acteur.typeacteur_id', 'Type d\'acteur <abbr title="obligatoire">*</abbr>'); ?>
        <?php
        if ($this->Html->value('Acteur.typeacteur_id')) $typeacteur_id = $this->Html->value('Acteur.typeacteur_id');
        else $typeacteur_id = 0;
        ?>

        <select id="ActeurTypeacteurId" onchange="afficheInfosElus(this);" name="data[Acteur][typeacteur_id]">
            <?php if ((count($typeacteurs) > 1) && (!$this->Html->value('Acteur.id'))) echo '<option value></option>' ?>
            <?php
            foreach ($typeacteurs as $typeacteur) {
                echo '<option class="' . $typeacteur['Typeacteur']['elu'] . '" value="' . $typeacteur['Typeacteur']['id'] . '"';
                if ($typeacteur['Typeacteur']['id'] == $typeacteur_id)
                    echo ' selected="selected" ';
                echo '>' . $typeacteur['Typeacteur']['nom'] . '</option>';
            }
            ?>
        </select>

        <div class="spacer"></div>
        <div id='infoElus'>
            <?php echo $this->Form->input('Acteur.position', array('label' => 'Ordre dans le conseil', 'size' => '3')); ?>
            <div class="spacer"></div>
            <?php echo $this->Form->input('Service.Service', array('label' => 'Délégation(s)', 'options' => $services, 'default' => $selectedServices, 'multiple' => 'multiple', 'class' => 'selectMultiple', 'empty' => true, 'escape' => false)); ?>
            <div class="spacer"></div>

            <label>Date de naissance : </label>
            <input name="date" size="9"   <?php if (isset($date)) echo("value =\"$date\""); ?>/>&nbsp;
            <a href="javascript:show_calendar('ActeurForm.date','f');"><?php echo $this->Html->image("calendar.png", array('style' => "border='0'")); ?></a>


        </div>
    </fieldset>
    <div class="spacer"></div>

    <fieldset>
        <legend>Autres informations</legend>
        <?php echo $this->Form->input('Acteur.note', array('type' => 'textarea', 'label' => 'Note', 'cols' => '30')); ?>
    </fieldset>
</div>
<div class="spacer"></div>

<div class="submit" style='text-align: center;'>
    <div class="btn-group">
        <?php
        if ($this->action == 'edit')
            echo $this->Form->hidden('Acteur.id');
        echo $this->Html->link('<i class="fa fa-arrow-left"></i> Retour', $previous, array('escape' => false, 'class' => 'btn'));
        echo $this->Form->button("<i class='fa fa-save'></i> Enregistrer", array('type' => 'submit', 'class' => 'btn btn-primary', 'escape' => false, 'title' => 'Sauvegarder les modifications'));
        ?>
    </div>
</div>

<?php echo $this->Form->end(); ?>
