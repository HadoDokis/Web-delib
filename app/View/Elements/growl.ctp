<?php
$pos = @strripos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6');
if ($pos === false) {
    ?>

    <script type="text/javascript">
    <?php if (isset($type) && $type == 'important') : ?>
            jQuery.jGrowl("<?php echo '<div class=\'important\'>' . $message . '</div>'; ?>", { header:'Important :', glue:'before' });
    <?php elseif (isset($type) && $type == 'erreur') : ?>
            jQuery.jGrowl("<?php echo '<div class=\'error_message\'>' . $message . '</div>'; ?>", { header:'Erreur :', sticky:true });
    <?php else : ?>
            jQuery.jGrowl("<?php echo '<div class=\'info\'>' . $message . '</div>'; ?>", {});
    <?php endif; ?>
    </script>
    <?php
}
?>
