<?php
	$pos = @strripos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6');
	if ($pos === false) {
?>

	<script type="text/javascript">
		<?php if (isset($type) && $type == 'important') : ?>
			jQuery.jGrowl("<?php echo '<div class=\'important\'>'.$content_for_layout.'</div>'; ?>", { header:'Important :', glue:'before' });
		<?php elseif (isset($type) && $type == 'erreur') : ?>
			jQuery.jGrowl("<?php echo '<div class=\'error_message\'>'.$content_for_layout.'</div>'; ?>", { header:'Erreur :', sticky:true });
		<?php elseif (isset($type) && $type == 'erreurTDT') : ?>
			jQuery.jGrowl("<?php echo '<div class=\'error_message\'>'.$content_for_layout.'</div>'; ?>", { header:'Rapport :', theme: 'tdt',  sticky:true  });
		<?php else : ?>
			jQuery.jGrowl("<?php echo '<div class=\'info\'>'.$content_for_layout.'</div>'; ?>", {});
		<?php endif; ?>
	</script>
<?php
	}
?>
