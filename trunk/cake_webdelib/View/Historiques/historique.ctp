<?php
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
$affichage .= $this->element('paginator',array('paginator' => $this->Paginator));
$affichage .= $this->Bs->close();
$affichage .= $this->Bs->close();
echo $affichage;
?>
<script>
    $(document).ready(function () {

        $('#CritereDifDate').on('change', function () {
            if ($('#CritereDateDebut').val() != '') {
                $('#CritereDateFin').val(modifierDate($('#CritereDateDebut').val(), 1));
            }
        });
        
        $('#CritereDateDebut').on('change', function () {
            //si la date de fin est null on initialise avec la date de début
            if ($('#CritereDateFin').val() == '') {
                $('#CritereDateFin').val($('#CritereDateDebut').val());
            }
            //si la combobox contient une valeur on rajoute l'écart a la date de sortie
            if ($('#CritereDifDate').val() != '') {
                $('#CritereDateFin').val(modifierDate($('#CritereDateDebut').val(), 1));
            }
        });
        $('#CritereDateFin').on('change', function () {
            //si la date de début est null on initialise avec la date de fin
            if ($('#CritereDateDebut').val() == '') {
                $('#CritereDateDebut').val($('#CritereDateFin').val());
            }
            //si la combobox contient une valeur on rajoute l'écart a la date de début
            if ($('#CritereDifDate').val() != '') {
                $('#CritereDateDebut').val(modifierDate($('#CritereDateFin').val(), -1));
            }
        });
        /**
         * A partir de la date passé on va renvoyer une nouvelle date 
         * en fonction de la combobox et de l'écart passé. L'utilisation 
         * d'une variable date permet de gérer les fin de mois,jour,...
         * 
         * @param {type} dateS date de départ au format yyyy-mm-dd hh:mm:ss
         * @param {type} nb valeur à rajouter 1 => +, -1 => -
         * @returns {String} retourne la date correctement formaté avec l'écarrt voulue
         */
        function modifierDate(dateS, nb) {

            var separators = ['-', ' ', ':'];
            //on récupère tout les champs un a un
            var tab = dateS.split(new RegExp(separators.join('|'), 'g'));
            //on construit la date
            var now = new Date(tab[0], tab[1], tab[2], tab[3], 0, 0, 0);
            //on ajoute la valeur voulue en fonction de la combox
            if ($('#CritereDifDate').val() == 0) {
                now.setHours(now.getHours() + nb);
            } else if ($('#CritereDifDate').val() == 1) {
                now.setDate(now.getDate() + nb);
            } else if ($('#CritereDifDate').val() == 2) {
                now.setMonth(now.getMonth() + nb);
            } else if ($('#CritereDifDate').val() == 3) {
                now.setFullYear(now.getFullYear() + nb);
            }

            return now.getFullYear() + '-' + ajoutZero(now.getMonth().toString()) + '-' + ajoutZero(now.getDate().toString()) + ' ' + ajoutZero(now.getHours().toString()) + ':' + ajoutZero(now.getMinutes().toString()) + ':' + ajoutZero(now.getSeconds().toString());
        }
        /**
         * Ajoute un zero en début de chaine si la taille de data est egale à 1
         * 
         * @param {type} data
         * @returns {String}
         */
        function ajoutZero(data) {
            if (data.length == 1) {
                return '0' + data;
            }
            return data;
        }
    });

</script>