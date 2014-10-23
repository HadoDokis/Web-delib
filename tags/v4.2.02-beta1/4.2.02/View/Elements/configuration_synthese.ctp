<script>
function moveItem(zoneId, etiquette) {
    $('<input>').attr({ type: 'hidden',
                          id: 'zone_'+zoneId,
                        name: 'zone_'+zoneId,
                        value : etiquette }).appendTo('form');

}
$(function() {
        $(".item").draggable({
             revert: "invalid",
             connectToSortable:  ".container_item",
        });
         
        $(".container_item").droppable({
             accept:  ".item", 
             revert : true,
             drop: function(event, ui) { moveItem($(this).attr('id'), ui.draggable.attr('id')); }
        });

});
</script>

<h3>Paramètrage de la vue de synthèse</h3>

<table width="100%" cellspacing="0" cellpadding="0"  >
	<tr>
		<th width='5%' align="right"></th>
		<th width='15%' align="left">Vue synth&eacute;tique</th>
		<th width='46%'> &nbsp;</th>
		<th width='18%' >&nbsp;</th>
		<th width='250px'>Actions</th>
	</tr>
	<tr>
		<td colspan='5' class='Border' height='1' >
		</td>
	</tr>

	<tr>
		<td rowspan=3 style="text-align:center;"><br />Icone d'état  de l'acte</td>
		<td><div id="1" class='container_item' >1</div></td>
		<td><div id="2" class='container_item' >2</div><div class="item" id='objet'>Objet de l'acte</div></td>
		<td><div id="3" class='container_item'>3</div></td>
		<td rowspan=3 class="actions">
                <br />
                    Actions possibles
		</td>
	</tr>
	<tr>
		<td><div id="4"  class='container_item'>4</div></td>
		<td class='corps' rowspan=1 ><div id="5"  class='container_item'>5</div</td>
		<td><div id="6"  class='container_item'>6</div></td>
	</tr>
	<tr>
		<td><div id="7" class='container_item' >7</div><div class="item" id='nature'><b>Nature de l'acte : Id de l'acte</b></div> </td>
		<td class='corps' rowspan=1 ><div id="8" class='container_item' >8</div><div class="item" id='theme'>Th&egrave;me :</div> </td>
		<td><div id="9"  class='container_item'>9</div></td>
	</tr>
	<tr>
		<td colspan='5' class='Border' height='1' >
		</td>
	</tr>
</table>
<div id='seances'        class="item">Séances inscrites au projet</div> 
<div id='emetteur'       class="item">Service émetteur du projet</div> 
<div id='circuit'        class="item">Circuits affecté au projet</div> 
<div id='acteur'         class="item">Viseur précédent du circuit </div>
<div id='etat'           class="item">Etat du projet</div> 
<div id='description'    class="item">Description du projet</div> 
<div id='limite'         class="item">Date limite du projet</div> 
<div id='classification' class="item">Classification du projet</div> 
