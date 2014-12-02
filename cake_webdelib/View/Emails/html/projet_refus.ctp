<p>Bonjour <?php echo $prenom; ?> <?php echo $nom; ?>,</p>
<p>Un projet que vous avez validé ou créé a été refusé.</p>
<p>
Objet : <?php echo $projet_objet; ?><br />
Séance : <?php echo $seance_deliberante; ?><br />
Identifiant : <?php echo $projet_identifiant; ?>
</p>
<p>Le projet peut à nouveau être édité : <?php echo $this->Html->link('Editer le projet',$projet_url_modifier); ?></p>
<p>Très cordialement.</p>