<p>Bonjour <?php echo $prenom; ?> <?php echo $nom; ?>,</p>
<p>Un projet vient d'être inséré dans un circuit dont vous êtes valideur.</p>
<p>
Objet : <?php echo $projet_objet; ?><br />
Séance : <?php echo $seance_deliberante; ?><br />
Identifiant : <?php echo $projet_identifiant; ?><br />
Dernier commentaire : <?php echo $projet_dernier_commentaire; ?>
</p>
<p>Vous pouvez le visualiser : <?php echo $this->Html->link('Visualiser le projet', $projet_url_visualiser); ?></p>
<p>Très cordialement.</p>