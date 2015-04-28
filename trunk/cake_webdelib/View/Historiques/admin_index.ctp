<?php
//CONSTRUCTION DE L'AFFICHAGE
$affichage = $this->Bs->row() .
             $this->Bs->col('lg12') .
             $this->Bs->table(
                array(
                array('title' => $this->Paginator->sort('Historique.created', __('Date'))),
                array('title' => __('User')),
                array('title' => $this->Paginator->sort('Deliberation.id', __('id délibération'))),
                array('title' => __('Commentaire')),
                ), array('striped')
            );
foreach ($historique as $data) {
    if (!empty($data['Historique']['commentaire'])) {
        $affichage .= 
            $this->Bs->cell($data['Historique']['created']) .
            $this->Bs->cell($data['User']['nom'] . ' ' . $data['User']['prenom']) .
            $this->Bs->cell($this->Html->link($data['Deliberation']['id'], array(
                'admin'=>false,
                'prefix'=> null,
                'controller' => 'deliberations', 
                'action' => 'view', $data['Deliberation']['id']), array(
                    'class' => 'btn', 
                    'escape' => false, 
                    'alt' => 'Nouvelle recherche parmi tous les projets', 
                    'title' => 'Nouvelle recherche parmi tous les projets'))).
            $this->Bs->cell($data['Historique']['commentaire'], 'text-justified');
    }
}
$affichage .= $this->Bs->endTable() .
              //affichage des pages numéroté
              $this->element('paginator', array('paginator' => $this->Paginator)).
              $this->Bs->close(2);

//AFFICHAGE
$this->Html->addCrumb('Historiques');
echo $this->element('filtre') . $this->Bs->tag('h3', 'Historiques') . $affichage;
?>
<script>
    $(document).ready(function () {
        $('#CritereDifDate, #CritereDateDebut, #CritereDateFin').on('change', function () {
            //on genere la date de debut avec le temps de la plage choisie dans la liste
            if($('#CritereDateFin').val()  && $('#CritereDifDate').val()){
                $('#CritereDateDebut').val($('#CritereDateFin').val());
                $('#CritereDateDebut').val(modifierDate('#CritereDifDate', $('#CritereDateFin').val(), -1));
            }
            //on ajoute a la date de debut le temps de la plage
            else if ($('#CritereDateDebut').val() && $('#CritereDifDate').val()){
                $('#CritereDateFin').val($('#CritereDateDebut').val());
                $('#CritereDateFin').val(modifierDate('#CritereDifDate', $('#CritereDateDebut').val(), 1));
            }
        });
    });
</script>