<?php

echo $this->Html->script('/libs/bootstrap-calendar/components/underscore/underscore-min.js');
echo $this->Html->script('/libs/bootstrap-calendar/js/language/fr-FR.js');
echo $this->Html->script('/libs/bootstrap-calendar/js/calendar.js');
echo $this->Html->css('/libs/bootstrap-calendar/css/calendar.css');

$this->Html->addCrumb('Séance à traiter', array($this->request['controller'], 'action'=>'listerFuturesSeances'));

echo $this->Bs->tag('h3', __('Calendrier des séances'));
$this->Html->addCrumb(__('Calendrier des séances'));

echo $this->Bs->row().
$this->Bs->col('xs4').
    $this->Bs->tag('h4', 'Année').$this->Bs->tag('small', 'Pour voir un séance cliquez sur la date').
$this->Bs->close().
$this->Bs->col('xs8').
    $this->Bs->div('pull-right form-inline').
        $this->Bs->div('btn-group').
            $this->Bs->btn('<span class="fa fa-arrow-left"></span> Précédent', null, array('tag'=>'button', 'type'=>'primary', 'data-calendar-nav'=>'prev','escape'=>false)).
            $this->Bs->btn('Aujourd\'hui', null, array('tag'=>'button', 'type'=>'default', 'data-calendar-nav'=>'today')).
            $this->Bs->btn('Suivant <span class="fa fa-arrow-right"></span>', null, array('tag'=>'button', 'type'=>'primary', 'data-calendar-nav'=>'next','escape'=>false)).
        $this->Bs->close().
        $this->Bs->div('btn-group').
            $this->Bs->btn('Année', null, array('tag'=>'button', 'type'=>'warning', 'data-calendar-view'=>'year','class'=>'active')).
            $this->Bs->btn('Mois', null, array('tag'=>'button', 'type'=>'warning', 'data-calendar-view'=>'month')).
            $this->Bs->btn('Jour', null, array('tag'=>'button', 'type'=>'warning', 'data-calendar-view'=>'day')).
        $this->Bs->close().
        $this->Bs->div('btn-group').
            $this->Bs->btn('<span class="fa fa-plus"></span> Ajouter une séance', array('controler'=>'seances','action'=>'add'), array('type'=>'primary','escape'=>false)).
$this->Bs->close(4);
                                     
echo $this->Bs->div(null, null, array('id'=>'calendar'));

?>
<script type="text/javascript">
        var calendar = $("#calendar").calendar(
            {
                view: 'year',
                language: 'fr-FR',
                tmpl_path: "/libs/bootstrap-calendar/tmpls/",
                events_source: function(){
                return  <?php 
                        $seances_calendrier=array();
                        foreach ($seances as $seance)
                        {
                            $seances_calendrier[]=array(
                            'id'=> $seance['id'],
                            'url'=> '/seances/edit/'.$seance['id']/*$this->Html->requestAction(array(
                                'controller'=>'seances', 
                                'action'=>'edit', $seance['id'])
                                    )*/,  
                            'class'=> 'event-warning',
                            'title'=> $seance['libelle'].' à '.date('H\Hi', $seance['strtotime']).' le '.date('d/m/Y', $seance['strtotime']),
                            'start' => $seance['strtotime'] . '000',
                            'end' => $seance['strtotime'] . '000'
                            ); 
                        }
                        
                        echo json_encode($seances_calendrier);
                       ?>;
                },
                onAfterEventsLoad: function(events) {
			if(!events) {
				return;
			}
			var list = $('#eventlist');
			list.html('');

			$.each(events, function(key, val) {
				$(document.createElement('li'))
					.html('<a href="' + val.url + '">' + val.title + '</a>')
					.appendTo(list);
			});
		},
                onAfterViewLoad: function(view) {
			$('#principal h4').text(this.getTitle());
			$('.btn-group button').removeClass('active');
			$('button[data-calendar-view="' + view + '"]').addClass('active');
		},
            });   
    
        $('.btn-group button[data-calendar-nav]').each(function() {
		var $this = $(this);
		$this.click(function() {
			calendar.navigate($this.data('calendar-nav'));
		});
	});

	$('.btn-group button[data-calendar-view]').each(function() {
		var $this = $(this);
		$this.click(function() {
			calendar.view($this.data('calendar-view'));
		});
	});

	$('#first_day').change(function(){
		var value = $(this).val();
		value = value.length ? parseInt(value) : null;
		calendar.setOptions({first_day: value});
		calendar.view();
	});

	$('#language').change(function(){
		calendar.setLanguage($(this).val());
		calendar.view();
	});

	$('#events-in-modal').change(function(){
		var val = $(this).is(':checked') ? $(this).val() : null;
		calendar.setOptions({modal: val});
	});
	$('#events-modal .modal-header, #events-modal .modal-footer').click(function(e){
		//e.preventDefault();
		//e.stopPropagation();
	});
    </script>