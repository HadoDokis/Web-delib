<?php
echo $this->Html->script('main.js');
$this->Html->addCrumb('Historiques');
echo $this->element('filtre');
echo $this->Bs->tag('h3', 'Historiques');
$affichage = $this->Bs->row();
$affichage .= $this->Bs->col('lg12');
$affichage .= $this->Bs->table(
        array(
    array('title' => $this->Paginator->sort('Historique.created', __('Date'))),
    array('title' => __('User')),
    array('title' => $this->Paginator->sort('Deliberation.id', __('id délibération'))),
    array('title' => __('Commentaire')),
        ), array('striped')
);
foreach ($historique as $data) {
    if (!empty($data['Historique']['commentaire'])) {
        $cell = '';
        $cell .= $this->Bs->cell($data['Historique']['created']);
        $cell .= $this->Bs->cell($data['User']['nom'] . ' ' . $data['User']['prenom']);
        $cell .= $this->Bs->cell($this->Html->link($data['Deliberation']['id'], array('controller' => 'deliberations', 'action' => 'view', $data['Deliberation']['id']), array('class' => 'btn', 'escape' => false, 'alt' => 'Nouvelle recherche parmi tous les projets', 'title' => 'Nouvelle recherche parmi tous les projets')));
        $cell .= $this->Bs->cell($data['Historique']['commentaire'], 'text-justified');
        $affichage .= $cell;
    }
}
$affichage .= $this->Bs->endTable();
//affichage des pages numéroté
$affichage .= $this->element('paginator', array('paginator' => $this->Paginator));
$affichage .= $this->Bs->close();
$affichage .= $this->Bs->close();
echo $affichage;
?>
<script>
    $(document).ready(function () {

        $('#CritereDifDate').on('change', function () {
            var date = new Date(Date.now());
            if ($('#CritereDateDebut').val() == '' && $('#CritereDateFin').val() == '') {
                $('#CritereDateDebut').val(date.getFullYear() + '-' + ajoutZero((date.getMonth() + 1).toString()) + '-' + ajoutZero(date.getDate().toString()) + ' ' + ajoutZero(date.getHours().toString()) + ':00:00');
                $('#CritereDateFin').val(modifierDate('#CritereDifDate', $('#CritereDateDebut').val(), 1));
            } else if ($('#CritereDateDebut').val() == '' && $('#CritereDateFin').val() != '') {
                $('#CritereDateDebut').val(modifierDate('#CritereDifDate', $('#CritereDateFin').val(), -1));
            } else {
                $('#CritereDateFin').val(modifierDate('#CritereDifDate', $('#CritereDateDebut').val(), 1));
            }
        });

        $('#CritereDifDate').on('change', function () {
            if ($('#CritereDateDebut').val() != '') {
                $('#CritereDateFin').val(modifierDate('#CritereDifDate', $('#CritereDateDebut').val(), 1));
            }
        });

        $('#CritereDateDebut').on('change', function () {
            //si la date de fin est null on initialise avec la date de début
            if ($('#CritereDateFin').val() == '') {
                $('#CritereDateFin').val($('#CritereDateDebut').val());
            }
            //si la combobox contient une valeur on rajoute l'écart a la date de sortie
            if ($('#CritereDifDate').val() != '') {
                $('#CritereDateFin').val(modifierDate('#CritereDifDate', $('#CritereDateDebut').val(), 1));
            }
        });
        $('#CritereDateFin').on('change', function () {
            //si la date de début est null on initialise avec la date de fin
            if ($('#CritereDateDebut').val() == '') {
                $('#CritereDateDebut').val($('#CritereDateFin').val());
            }
            //si la combobox contient une valeur on rajoute l'écart a la date de début
            if ($('#CritereDifDate').val() != '') {
                $('#CritereDateDebut').val(modifierDate('#CritereDifDate', $('#CritereDateFin').val(), -1));
            }
        });

    });

</script>