<div class="typeactes">
    <h2>Liste des types d'acte</h2>

    <table style='width:100%'>
        <tr>
            <th>Libellé</th>
            <th>Compteur</th>
            <th>Modèles</th>
            <th>Nature</th>
            <th style='width:90px'>Actions</th>
        </tr>
        <?php foreach ($typeactes as $typeacte): ?>
            <tr>
                <td><?php echo $typeacte['Typeacte']['libelle']; ?></td>
                <td><?php echo $typeacte['Compteur']['nom']; ?></td>
                <td><?php
                    echo 'Document préparatoire : ' . $typeacte['Modelprojet']['name'] . '<br/>';
                    echo 'Document final : ' . $typeacte['Modeldeliberation']['name'] . '<br/>';
                    ?></td>
                <td><?php echo $typeacte['Nature']['libelle']; ?></td>
                <td class="actions">
                    <?php
                    echo $this->Html->link(SHY, '/typeactes/view/' . $typeacte['Typeacte']['id'], array('class' => 'link_voir', 'escape' => false, 'title' => 'Voir'), false);
                    echo $this->Html->link(SHY, '/typeactes/edit/' . $typeacte['Typeacte']['id'], array('class' => 'link_modifier', 'escape' => false, 'title' => 'Modifier'), false);
                    if ($typeacte['Typeacte']['is_deletable'])
                        echo $this->Html->link(SHY, '/typeactes/delete/' . $typeacte['Typeacte']['id'], array('class' => 'link_supprimer', 'escape' => false, 'title' => 'Supprimer'), 'Êtes vous sur de vouloir supprimer ' . $typeacte['Typeacte']['libelle'] . ' ?');
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php $this->Html2->boutonAdd("Ajouter un type d'acte", "Ajouter"); ?>
</div>
