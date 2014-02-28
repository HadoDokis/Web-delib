<div id="waiter" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="waiter" aria-hidden="true">
    <div class="modal-header">
        <h3 id="waiter-title">Action en cours de traitement</h3>
    </div>
    <div class="modal-body">
        <?php echo $this->Html->image('loader-circle.gif', array('alt' => 'Loading', 'id' => 'waiter-image')) ?>
        <p>Veuillez patienter...</p>
    </div>
    <div class="modal-footer">
    </div>
</div>