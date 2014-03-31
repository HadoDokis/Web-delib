<div class="deliberations">
    <h2>Autres actes validés non transmissibles</h2>
    <table style='width:100%'>
        <tr>
            <th>Identifiant</th>
            <th>Numéro généré</th>
            <th>Libellé de l'acte</th>
            <th>Titre</th>
            <th>Type d'acte</th>
            <th>Service</th>
        </tr>
        <?php foreach ($this->data as $acte) : ?>
            <tr>
                <td><?php echo $acte['Deliberation']['id']; ?></td>
                <td><?php echo $this->Html->link($acte['Deliberation']['num_delib'],array('action'=>'view', $acte['Deliberation']['id']), array('title'=>'Voir les détails de l\'acte '.$acte['Deliberation']['num_delib'])); ?></td>
                <td><?php echo $acte['Deliberation']['objet']; ?></td>
                <td><?php echo $acte['Deliberation']['titre']; ?></td>
                <td><?php echo $acte['Typeacte']['libelle']; ?></td>
                <td><?php echo $acte['Service']['libelle']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
