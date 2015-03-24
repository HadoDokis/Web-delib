<!--<div id="waiter" class="modal hide fade" tabindex="-1" role="waiter" aria-labelledby="waiter" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
        <h3 id="waiter-title">Action en cours de traitement</h3>
    </div>
    <div class="modal-body">
        <?php echo $this->Html->image('loader-circle.gif', array('alt' => 'Loading', 'id' => 'waiter-image')) ?>
        <div class="spacer"></div>
        <p>Veuillez patienter...</p>
    </div>
</div>-->


<div id="waiter" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Action en cours de traitement</h4>
      </div>
      <div class="modal-body">
        <?php echo $this->Html->image('loader-circle.gif', array('alt' => 'Loading', 'id' => 'waiter-image')) ?>
        <div class="spacer"></div>
        <p>Veuillez patienter...</p>
      </div>
<!--      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>-->
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
