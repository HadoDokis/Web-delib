<p>Bonjour <?php echo $prenom; ?> <?php echo $nom; ?>,</p>
<p>Un projet vient d'être inséré dans un circuit dont vous êtes valideur<?php echo $seance_deliberante; ?>.</p>
<p>Objet : <?php echo $projet_objet; ?><br />
Identifiant : <?php echo $projet_identifiant; ?></p>
<p>Le projet peut à nouveau être édité : <?php echo $this->Html->link('Editer le projet',$projet_url_modifier); ?></p>
<p>Très cordialement.</p>