<p>Bonjour <?php echo $prenom; ?> <?php echo $nom; ?>,</p>
<p>Un projet est arrivé dans votre bannette "Mes projets à traiter".</p>
<p>
Objet : <?php echo $projet_objet; ?><br />
Séance : <?php echo $seance_deliberante; ?><br />
Identifiant : <?php echo $projet_identifiant; ?><br />
Dernier commentaire : <?php echo $projet_dernier_commentaire; ?>
</p>
<p>Vous pouvez le traiter : <?php echo $this->Html->link('Traiter le projet',$projet_url_modifier); ?></p>
<p>Très cordialement.</p>