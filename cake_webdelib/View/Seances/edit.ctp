<?php
echo $this->Html->script('/libs/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js');
echo $this->Html->script('/libs/bootstrap-datetimepicker/js/locales/bootstrap-datetimepicker.fr.js');
echo $this->Html->css('/libs/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css');

echo $this->Html->script('ckeditor/ckeditor');
echo $this->Html->script('ckeditor/adapters/jquery');

$this->Html->addCrumb('Séance à traiter', array($this->request['controller'], 'action'=>'listerFuturesSeances'));

if ($this->Html->value('Seance.id')) {
    echo $this->Bs->tag('h3', 'Modification d\'une séance');
    $this->Html->addCrumb('Modification d\'une séance');
    echo $this->BsForm->create('Seance', array('url' => array('controller' => 'seances', 'action' => 'edit', $this->Html->value('Seance.id')), 'type' => 'file', 'name' => 'SeanceForm'));
} else {
    echo $this->Bs->tag('h3', 'Ajout d\'une séance');
    $this->Html->addCrumb('Ajout d\'une séance');
    echo $this->BsForm->create('Seance', array('url' => array('controller' => 'seances', 'action' => 'add'), 'type' => 'file', 'name' => 'SeanceForm'));
}

$onglets=array(
    'seance' => 'Date de séance',
    );
if(!empty($infosupdefs)){
    $onglets['infosup']='Informations supplémentaires';
}
    
echo $this->Bs->tab($onglets, 
    array('active' =>'seance', 'class' => '-justified')) .
 $this->Bs->tabContent();

echo $this->Bs->tabPane('seance', array('class' => 'active')) .
 $this->Html->tag(null, '<br />') .
// $this->Html->tag('div', null, array('class' => 'panel panel-default')) .
//$this->Html->tag('div', 'Date', array('class' => 'panel-heading')) .
//$this->Html->tag('div', null, array('class' => 'panel-body')) .
        $this->BsForm->select('Seance.type_id',$typeseances, array('label' => 'Type de s&eacute;ance',
            'default' => $this->Html->value('Seance.type_id'),
            'empty' => true)).
        $this->BsForm->dateTimepicker('Seance.date', //TODO
                array('language'=>'fr', 
                    'autoclose'=>'true',
                    'format' => 'dd/mm/yyyy hh:ii',
                    'pickerPosition'=>'bottom-right'), 
                array(
                        'label' => 'Date',
                        'title' => 'Choisissez la date et l\'heure de séance',
                        'style' => 'cursor:pointer',
                        'help' => 'Cliquez sur le champs ci-dessus pour choisir la date et l\'heure de la séance',
                        'readonly' => 'readonly',
                        'value'=>isset($date)?$date:'')
                ).
$this->Bs->tabClose();
     
if (!empty($infosupdefs))
{
    echo $this->Bs->tabPane('infosup') . $this->Html->tag(null, '<br />');
        foreach ($infosupdefs as $infosupdef) {
            $disabled = $infosupdef['Infosupdef']['actif'] == false;
            $fieldName = 'Infosup.' . $infosupdef['Infosupdef']['code'];
            $fieldId = 'Infosup' . Inflector::camelize($infosupdef['Infosupdef']['code']);
            echo "<div class='required'>";
            
            if ($infosupdef['Infosupdef']['type'] == 'text') {
                echo $this->BsForm->input($fieldName, array('label' => $infosupdef['Infosupdef']['nom'], 'type' => 'textarea', 'title' => $infosupdef['Infosupdef']['commentaire']));
            } elseif ($infosupdef['Infosupdef']['type'] == 'boolean') {
                echo $this->BsForm->input($fieldName, array('label' => $infosupdef['Infosupdef']['nom'], 'type' => 'checkbox', 'title' => $infosupdef['Infosupdef']['commentaire']));
            } elseif ($infosupdef['Infosupdef']['type'] == 'date') {
                $fieldSelector = preg_replace("#[^a-zA-Z]#", "", $fieldId);
                echo $this->BsForm->input($fieldName, array('type' => 'text', 'id' => $fieldSelector, 'div' => false, 'label' => false, 'size' => '9', 'title' => $infosupdef['Infosupdef']['commentaire']));
                echo '&nbsp;';
                echo $this->Html->link($this->Html->image("calendar.png", array('style' => "border:0")), "javascript:show_calendar('seanceForm.$fieldSelector', 'f');", array('escape' => false), false);
            } elseif ($infosupdef['Infosupdef']['type'] == 'richText') {
                echo '<div class="annexesGauche"></div>';
                echo '<div class="fckEditorProjet">';
                echo $this->BsForm->input($fieldName, array('label' => $infosupdef['Infosupdef']['nom'], 'type' => 'textarea'));
                echo $this->Fck->load($fieldId);
                echo '</div>';
                echo '<div class="spacer"></div>';
            } elseif ($infosupdef['Infosupdef']['type'] == 'file') {
                if (empty($this->data['Infosup'][$infosupdef['Infosupdef']['code']]))
                    echo $this->BsForm->input($fieldName, array('label' => $infosupdef['Infosupdef']['nom'], 'type' => 'file', 'size' => '60', 'title' => $infosupdef['Infosupdef']['commentaire']));
                else {
                    echo '<span id="' . $infosupdef['Infosupdef']['code'] . 'InputFichier" style="display: none;"></span>';
                    echo '<span id="' . $infosupdef['Infosupdef']['code'] . 'AfficheFichier">';
                    echo '[' . $this->Html->link($this->data['Infosup'][$infosupdef['Infosupdef']['code']], '/infosups/download/' . $this->data['Seance']['id'] . '/' . $infosupdef['Infosupdef']['id'], array('title' => $infosupdef['Infosupdef']['commentaire'])) . ']';
                    echo '&nbsp;&nbsp;';
                    echo $this->Html->link('Supprimer', "javascript:infoSupSupprimerFichier('" . $infosupdef['Infosupdef']['code'] . "', '" . $infosupdef['Infosupdef']['commentaire'] . "')", null, 'Voulez-vous vraiment supprimer le fichier joint ?\n\nAttention : ne prendra effet que lors de la sauvegarde\n');
                    echo '</span>';
                }
            } elseif ($infosupdef['Infosupdef']['type'] == 'odtFile') {
                if (empty($this->data['Infosup'][$infosupdef['Infosupdef']['code']])
                    || empty($this->data['Infosup'][$infosupdef['Infosupdef']['code']]['tmp_name'])
                    || isset($errors_Infosup[$infosupdef['Infosupdef']['code']])
                )
                    echo $this->BsForm->input($fieldName, array('label' => $infosupdef['Infosupdef']['nom'], 'type' => 'file', 'size' => '60', 'title' => $infosupdef['Infosupdef']['commentaire']));
                else {
                    echo '<span id="' . $infosupdef['Infosupdef']['code'] . 'InputFichier" style="display: none;"></span>';
                    echo '<span id="' . $infosupdef['Infosupdef']['code'] . 'AfficheFichier">';
                    $name = $this->data['Infosup'][$infosupdef['Infosupdef']['code']];
                    $url = Configure::read('PROTOCOLE_DL') . "://" . $_SERVER['SERVER_NAME'] . "/files/generee/seance/" . $this->data['Seance']['id'] . "/$name";
                    echo "[<a href='$url'>$name</a>] ";
                    echo $this->BsForm->hidden($fieldName);
                    echo '&nbsp;&nbsp;';
                    echo $this->Html->link('Supprimer', "javascript:infoSupSupprimerFichier('" . $infosupdef['Infosupdef']['code'] . "', '" . $infosupdef['Infosupdef']['commentaire'] . "')", null, 'Voulez-vous vraiment supprimer le fichier joint ?\n\nAttention : ne prendra effet que lors de la sauvegarde\n');
                    echo '</span>';
                }
            } elseif ($infosupdef['Infosupdef']['type'] == 'list') {
                echo $this->BsForm->s($fieldName, $infosuplistedefs[$infosupdef['Infosupdef']['code']], array('label' => false, 'empty' => true, 'title' => $infosupdef['Infosupdef']['commentaire'], 'class' => 'select2 selectone'));
            } elseif ($infosupdef['Infosupdef']['type'] == 'listmulti') {
                if (!$disabled) {
                    echo $this->BsForm->select($fieldName, $infosuplistedefs[$infosupdef['Infosupdef']['code']], 
                            array('selected' => !empty($this->request->data['Infosup'][$infosupdef['Infosupdef']['code']]) ? $this->request->data['Infosup'][$infosupdef['Infosupdef']['code']] : '', 
                                'label' => $infosupdef['Infosupdef']['nom'], 
                                'empty' => true, 
                                'title' => $infosupdef['Infosupdef']['commentaire'], 
                                'multiple' => true, 
                                'class' => 'select2 selectmultiple'));
                } else {
                    echo $this->BsForm->input($fieldName, array('selected' => $this->request->data['Infosup'][$infosupdef['Infosupdef']['code']], 'label' => $infosupdef['Infosupdef']['nom'], 'options' => $infosuplistedefs[$infosupdef['Infosupdef']['code']], 'empty' => true, 'title' => $infosupdef['Infosupdef']['commentaire'], 'disabled' => $disabled));
                    echo $this->BsForm->input($fieldName, array('value' => implode(',', $selected_values), 'id' => false, 'type' => 'hidden'));
                }

            }
            echo $this->Bs->tabClose();
        };
 }
echo $this->Bs->tabPaneClose();


    echo $this->BsForm->hidden('Seance.id');
    //$this->BsForm->setLeft(0);
echo $this->Html2->btnSaveCancel('', 'listerFuturesSeances', 'Ajouter la séance').
$this->BsForm->end();
?>
<script type="application/javascript">
    $(document).ready(function () {
        $(".select2.selectmultiple").select2({
            width: "resolve",
            allowClear: true,
            placeholder: "Liste à choix multiples"
        });
        $(".select2.selectone").select2({
            width: "resolve",
            allowClear: true,
            placeholder: "Selectionnez un élément"
        });
        $('#SeanceTypeId').select2({
            width: "resolve"
        });
    });
</script>