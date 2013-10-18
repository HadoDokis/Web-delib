<div id="vue_cadre">
<h3>Fiche Type de séance</h3>

<dl>
	<div class="demi">
		<dt>Libellé</dt>
		<dd>&nbsp;<?php echo $typeseance['Typeseance']['libelle']?></dd>
	</div>
	<div class="demi">
	<dt>Nombre de jours avant retard</dt>
	<dd><?php echo $typeseance['Typeseance']['retard']; ?></dd>
	</div>
	<div class="spacer"></div>
	<div class="demi">
		<dt>Action</dt>
		<dd>&nbsp;<?php echo $typeseance['Typeseance']['action'] ? 'Avis' : 'Vote'?></dd>
	</div>
	<div class="demi">
		<dt>Compteur</dt>
		<dd>&nbsp;<?php echo $typeseance['Compteur']['nom']?></dd>
	</div>
	<div class="spacer"></div>

	<div class="demi">
		<dt>Modèle de la convocation</dt>
		<dd>&nbsp;<?php echo $typeseance['Modelconvocation']['modele']; ?></dd>
	</div>
	<div class="demi">
		<dt>Modèle de l'ordre du jour</dt>
		<dd>&nbsp;<?php echo $typeseance['Modelordredujour']['modele']?></dd>
	</div>
	<div class="spacer"></div>

	<div class="demi">
		<dt>Modèle du PV sommaire</dt>
		<dd>&nbsp;<?php echo $typeseance['Modelpvsommaire']['modele']?></dd>
	</div>
	<div class="demi">
		<dt>Modèle du PV détaillé</dt>
		<dd>&nbsp;<?php echo $typeseance['Modelpvdetaille']['modele']?></dd>
	</div>
	<div class="spacer"></div>

	<div class="demi">
		<dt>Date de création</dt>
		<dd>&nbsp;<?php echo $typeseance['Typeseance']['created']?></dd>
	</div>
	<div class="demi">
		<dt>Date de modification</dt>
		<dd>&nbsp;<?php echo $typeseance['Typeseance']['modified']?></dd>
	</div>
	<div class="spacer"></div>
</dl>

<br />
<ul id="actions_fiche">
	<li><?php 
        echo $this->Html->link('<i class="icon-arrow-left"></i> Retour', '/typeseances/index', array('class'=>'btn', 
                                                                          'title'=>'Retourner à la liste', 
                                                                          'escape' =>false), false);
        ?></li>
	<li><?php 
        echo $this->Html->link('<i class="icon-edit"></i> Modifier', '/typeseances/edit/' . $typeseance['Typeseance']['id'], array('class'=>'btn', 
                                                                                                            'title'=>'Modifier', 
                                                                                                            'escape' => false), false);
        ?></li>
</ul>

</div>
