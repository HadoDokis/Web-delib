<?php

echo $this->Bs->tag('h2', 'Edition du logo de la Collectivité') .
 $this->BsForm->create('Collectivites', array('url' => $this->webroot . 'collectivites/setLogo', 'type' => 'file')) .
 $this->BsForm->input('Collectivite.logo', array('label' => 'Fichier image','help' => 'Format de fichier png, jpg ou jpeg', 'type' => 'file', 'class'=>false)) .
 $this->Html2->btnSaveCancel('', "index", "Ajouter le logo", 'Sauvegarder logo') .
 $this->BsForm->end();
