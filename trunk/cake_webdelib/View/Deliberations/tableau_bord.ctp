<?php
echo $this->Html->css('/components/pivottable/dist/pivot.css');
echo $this->Html->script('/components/pivottable/dist/pivot.js');
echo $this->Html->script('/components/pivottable/dist/pivot.fr.js');

$this->Html->addCrumb(__('Tableau de bord'));
echo $this->element('filtre');
echo $this->Html->tag('h2', __('Tableau de bord', true));

echo '<div id="pivot"></div>';
echo '<div id="view" class="modal fade in" role="dialog" ></div>';       
?>
<script type="text/javascript">

    $(document).ready(function () {
        
        $('#CritereDifDate').on('change', function () {

            if ($('#CritereDifDate').val() == '') {
                $('#CritereDateDebut').val('');
                $('#CritereDateFin').val('');
            } else {
                var date = new Date(Date.now());
   
                if ($('#CritereDifDate').val() == 4) {
                    document.getElementById('CritereDateDebut').disabled = true;
                    document.getElementById('CritereDateFin').disabled = true;
                    $('#CritereDateFin').val(modifierDate(1, date.getFullYear() + '-' + ajoutZero((date.getMonth() + 1).toString()) + '-' + ajoutZero(date.getDate().toString()) + ' 23:59:59',  -1));
                    $('#CritereDateDebut').val(modifierDate(1, date.getFullYear() + '-' + ajoutZero((date.getMonth() + 1).toString()) + '-' + ajoutZero(date.getDate().toString()) + ' 00:00:00', -1));
                } else if ($('#CritereDifDate').val() == 5) {
                    document.getElementById('CritereDateDebut').disabled = true;
                    document.getElementById('CritereDateFin').disabled = true;
                    $('#CritereDateFin').val(date.getFullYear() + '-' + ajoutZero((date.getMonth() + 1).toString()) + '-' + ajoutZero(date.getDate().toString()) + ' 23:59:59');
                    $('#CritereDateDebut').val(modifierDate(1, date.getFullYear() + '-' + ajoutZero((date.getMonth() + 1).toString()) + '-' + ajoutZero(date.getDate().toString()) + ' 00:00:00',  -6));
                } else if ($('#CritereDifDate').val() == 6) {
                    document.getElementById('CritereDateDebut').disabled = true;
                    document.getElementById('CritereDateFin').disabled = true;
                    $('#CritereDateFin').val(date.getFullYear() + '-' + ajoutZero((date.getMonth() + 1).toString()) + '-' + ajoutZero(date.getDate().toString()) + ' 23:59:59');
                    $('#CritereDateDebut').val(date.getFullYear() + '-' + ajoutZero((date.getMonth() + 1).toString()) + '-' + ajoutZero(date.getDate().toString()) + ' 00:00:00');
                } else {
                    document.getElementById('CritereDateDebut').disabled = false;
                    document.getElementById('CritereDateFin').disabled = false;
                    if ($('#CritereDateDebut').val() == '' && $('#CritereDateFin').val() == '') {

                        $('#CritereDateDebut').val(date.getFullYear() + '-' + ajoutZero((date.getMonth() + 1).toString()) + '-' + ajoutZero(date.getDate().toString()) + ' ' + ajoutZero(date.getHours().toString()) + ':00:00');
                        $('#CritereDateFin').val(modifierDate($('#CritereDifDate').val(), $('#CritereDateDebut').val(), 1));

                    } else if ($('#CritereDateDebut').val() == '' && $('#CritereDateFin').val() != '') {
                        $('#CritereDateDebut').val(modifierDate($('#CritereDifDate').val(), $('#CritereDateFin').val(), -1 ));
                    } else {
                        $('#CritereDateFin').val(modifierDate($('#CritereDifDate').val(), $('#CritereDateDebut').val(), 1));
                    }
                }
            }
        });


        $('#CritereDateDebut').on('focus', function () {
            if ($('#CritereDifDate').val() == 4 || $('#CritereDifDate').val() == 5) {
                $('#CritereDateDebut').datetimepicker('hide');
            }

        });
        $('#CritereDateDebut').on('change', function () {
            //si la date de fin est null on initialise avec la date de début
            if($('#CritereDateFin').val() == ''){
                $('#CritereDateFin').val(modifierDate($('#CritereDifDate').val(), $('#CritereDateDebut').val(), 1));
            }
        });


        $('#CritereDateFin').on('change', function () {
            //si la date de début est null on initialise avec la date de fin
            if($('#CritereDateDebut').val() == ''){
                $('#CritereDateDebut').val(modifierDate($('#CritereDifDate').val(), $('#CritereDateFin').val(), 1));
            }
        });

        //console.log('Line : <?php // echo __LINE__; ?> Action : ready');
        //$depth
        var data = <?php echo $json ?>;
        var cols = ["Service_Validateur"];
        var rows = ["Service_Rédacteurs"];
<?php
for ($i = 1; $depth >= $i; $i++) {
    ?>rows[rows.length] = "Service_Rédacteurs_de_niveau_" +<?php echo $i; ?>;
                     cols[cols.length] = "Service_Validateur_de_niveau_" +<?php echo $i; ?>;
    <?php
}
?>

                 $(function () {
                     var derivers = $.pivotUtilities.derivers;
                     var tpl = $.pivotUtilities.aggregatorTemplates;
                     try{
                         
                         $("#pivot").pivotUI(data, {
                             aggregators: {
                                 "somme": function () {
                                     return tpl.sum()(["amount"])
                                 },
                                 /*"Average": function () {
                                     return tpl.average()(["amount"])
                                 },
                                 "Sum as Fraction of Total": function () {
                                     return tpl.fractionOf(tpl.sum(), "total")(["amount"])
                                 },*/
                             },
                             rows: rows,
                             cols: cols,
                             vals: ["amount"],
                             hiddenAttributes: ["amount", "SV", "SR", "url","date_limite"],
                         }, false, "fr");
                         
                     } catch(err) {
                         console.log(err.message);
                     }
                    //var buff = $("#pivot>table>tbody>tr>td>table>tr>td");
                    //console.log($("#pivot").find('*')); 
                    //console.log($("table#pvtTable.pvtTable").find('*'));
                    //console.log($("table#pvtTable.pvtTable>tr>td#17A21"));
                 });

             }); 
</script>