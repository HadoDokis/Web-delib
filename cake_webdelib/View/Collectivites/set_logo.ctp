<h2>Ajout du logo</h2>

	<?php echo $form->create('Collectivites',array('url'=>$this->webroot.'collectivites/setLogo','type'=>'file')); ?>

    <p>Image : &nbsp; &nbsp; &nbsp; &nbsp; <?php echo $form->input('Image.logo', array('label'=>'', 'div'=>false, 'type'=>'file', 'size' => '40'))?>
    <br/><br/><i>(au format jpg ou jpeg)</i>
    </p>
    <br/><br/>
    <div class="submit">
		<?php echo $form->submit('Ajouter logo', array('div'=>false, 'class'=>'bt_save_border', 'name'=>'Ajouter'));?>
		<?php echo $html->link('Annuler', '/collectivites/index', array('class'=>'link_annuler', 'name'=>'Annuler'))?>
	</div>
          
	<?php echo $form->end(); ?>
