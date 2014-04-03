<div class="seances">
    <h2>Information de votre collectivité</h2>

    <table cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <th>Collectivité</th>
            <th>Logo</th>
            <th>Actions</th>
        </tr>
        <tr height='36px'>
            <td style="text-align:center"><?php echo $collectivite['Collectivite']['nom']; ?>
                <br/><br/><?php echo $collectivite['Collectivite']['adresse']; ?>
                <br/><?php echo $collectivite['Collectivite']['CP'] . ' ' . $collectivite['Collectivite']['ville']; ?>
                <br/><br/><?php echo $collectivite['Collectivite']['telephone']; ?>
            </td>
            <td class="text-center"><?php echo $this->Html->image($logo_path, array('alt' => 'logo de la collectivité', 'style' => 'max-width: 500px')); ?></td>
            <td class="actions">
                <?php echo $this->Html->link(SHY, '/collectivites/edit', array('class' => 'link_modifier', 'escape' => false, 'title' => 'Modifier'), false) ?>
                <?php echo $this->Html->link(SHY, '/collectivites/setLogo', array('class' => 'link_inserer_logo', 'escape' => false, 'title' => 'Changer de logo (page de connexion)'), false) ?>
            </td>

        </tr>
    </table>

</div>
