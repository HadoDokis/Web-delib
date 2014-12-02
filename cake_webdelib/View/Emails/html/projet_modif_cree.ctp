<p>Bonjour <?php echo $prenom; ?> <?php echo $nom; ?>,</p>
<p>Un projet dont vous êtes le rédacteur vient d'être modifié.</p>
<p>
Objet : <?php echo $projet_objet; ?><br />
Séance : <?php echo $seance_deliberante; ?><br />
Identifiant : <?php echo $projet_identifiant; ?>
</p>
<p>Vous pouvez le visualiser : <?php echo $this->Html->link('Visualiser le projet',$projet_url_visualiser); ?></p>
<p>Très cordialement.</p>