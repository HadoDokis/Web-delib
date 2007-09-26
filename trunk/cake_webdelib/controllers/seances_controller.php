<?php
class SeancesController extends AppController {

	var $name = 'Seances';
	var $helpers = array('Html', 'Form', 'Html2', 'Javascript','fpdf');
	var $components = array('Date');
	var $uses = array('Deliberation','Seance','User','SeancesUser', 'Collectivite');
	
	function index() {
		$this->Seance->recursive = 0;
		$seances = $this->Seance->findAll(null,null,'date asc'); 
		
		for ($i=0; $i<count($seances); $i++)
		    $seances[$i]['Seance']['date'] = $this->Date->frenchDate(strtotime($seances[$i]['Seance']['date']));
			     
		$this->set('seances', $seances);	
	}
	

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalide id pour la seance.');
			$this->redirect('/seances/index');
		}
		$this->set('seance', $this->Seance->read(null, $id));
	}

	function add() {
		if (empty($this->data)) {
			$this->set('typeseances', $this->Seance->Typeseance->generateList());
			$this->set('selectedTypeseances', null);
			$this->render();
		} else {
			$this->cleanUpFields('Seance');
		
			$this->data['Seance']['date']=  $this->Utils->FrDateToUkDate($this->params['form']['date']);
			$this->data['Seance']['date'] = $this->data['Seance']['date'].' '.$this->data['Seance']['date_hour'].':'.$this->data['Seance']['date_min'];

			if ($this->Seance->save($this->data)) {
				$this->Session->setFlash('La s&eacute;ance a &eacute;t&eacute; sauvegard&eacute;');
				$this->redirect('/seances/index');
			} else {
				$this->Session->setFlash('Corrigez les erreurs ci-dessous.');
				$this->set('typeseances', $this->Seance->Typeseance->generateList());
				if (empty($this->data['Typeseance']['Typeseance'])) {
					$this->data['Typeseance']['Typeseance'] = null;
				}
				$this->set('selectedTypeseances', $this->data['Typeseance']['Typeseance']);
			}
		}
	}

	function edit($id = null) {
		if (empty($this->data)) {
			if (!$id) {
				$this->Session->setFlash('Invalide id pour la seance');
				$this->redirect('/seances/index');
			}
			$this->data = $this->Seance->read(null, $id);
			$this->set('typeseances', $this->Seance->Typeseance->generateList());
			if (empty($this->data['Typeseance'])) { $this->data['Typeseance'] = null; }
				$this->set('selectedTypeseances', $this->_selectedArray($this->data['Typeseance']));
		} else {
			$this->cleanUpFields('Seance');
			if ($this->Seance->save($this->data)) {
				$this->Session->setFlash('La s&eacute;ance a &eacute;t&eacute; sauvegard&eacute;');
				$this->redirect('/seances/index');
			} else {
				$this->Session->setFlash('Corrigez les erreurs ci-dessous.');
				if (empty($this->data['Typeseance']['Typeseance'])) { $this->data['Typeseance']['Typeseance'] = null; }
					$this->set('selectedTypeseances', $this->data['Typeseance']['Typeseance']);
			}
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Invalide id pour la seance');
			$this->redirect('/seances/index');
		}
		if ($this->Seance->del($id)) {
			$this->Session->setFlash('La s&eacute;ance a &eacute;t&eacute; suprim&eacute;e');
			$this->redirect('/seances/index');
		}
	}
	
	function listerFuturesSeances()
	{
		if (empty ($this->data)) {
			$condition= 'date >= "'.date('Y-m-d H:i:s').'"';
			$seances = $this->Seance->findAll(($condition),null,'date asc'); 
		
			for ($i=0; $i<count($seances); $i++)
			    $seances[$i]['Seance']['date'] = $this->Date->frenchDate(strtotime($seances[$i]['Seance']['date']));
			     
			$this->set('seances', $seances);	
		}
	}
	
	function listerAnciennesSeances()
	{
		if (empty ($this->data))
		{
			$condition= 'date <= "'.date('Y-m-d H:i:s').'"';
			$this->set('seances', $this->Seance->findAll(($condition),null,'date ASC'));
		}
	}

	function afficherCalendrier ($annee=null){
		
		vendor('Calendar/includeCalendarVendor');
	
		define ('CALENDAR_MONTH_STATE',CALENDAR_USE_MONTH_WEEKDAYS);
        
		if (!isset($annee))
		     $annee = date('Y');
   
 		$tabJoursSeances = array();  
 		$fields = 'date';
        $condition = "annee = $annee";
        $joursSeance = $this->Seance->findAll(null, $fields);
        foreach ($joursSeance as $date) {
        	$date = strtotime(substr($date['Seance']['date'], 0, 10));	
        	array_push($tabJoursSeances,  $date);
        }

  		$Year = new Calendar_Year($annee);
		$Year->build();
	    $today = mktime('0','0','0');
	    $i = 0;
		
		$calendrier = "<table>\n<tr   style=\"vertical-align:top;\">\n";
		while ( $Month = $Year->fetch() ) {

	  		$calendrier .= "<td><table class=\"month\">\n" ;
	     	$calendrier .= "<caption class=\"month\">".$this->Date->months[$Month->thisMonth('int')]."</caption>\n" ;
	     	$calendrier .= "<tr><th>Lu</th><th>Ma</th><th>Me</th><th>Je</th><th>Ve</th><th>Sa</th><th>Di</th></tr>\n";
	   		$Month->build();
	   		
			while ( $Day = $Month->fetch() ) {
		        if ( $Day->isFirst() == 1 ) {
		       		$calendrier .= "<tr>\n" ;
		        }
		        
		        if ( $Day->isEmpty() ) {
		           $calendrier .=  "<td>&nbsp;</td>\n" ;
		        } 
		        else {
		        	
		            if ($today == $Day->thisDay('timestamp')){
		                 $balise="today";
		            }
		            elseif (in_array ($Day->thisDay('timestamp'), $tabJoursSeances) )
		            {
		            	$balise="seance";
		            }
		            else {
		            	$balise="normal";
		            }
		            $calendrier .=  "<td><p class=\"$balise\">".$Day->thisDay()."</p></td>\n" ;
		        }
		        if ( $Day->isLast() ) {
		           $calendrier .=  "</tr>\n" ;
		        }
			}

     		$calendrier .= "</table>\n</td>\n" ;
	
	    	if ($i==5)
	        	$calendrier .= "</tr><tr   style=\"vertical-align:top;\">\n" ;
	
	    	$i++;
		}
		$calendrier .=  "</tr>\n</table>\n" ;
		
		$this->set('annee', $annee);
		$this->set('calendrier',$calendrier);
	}
	
	function afficherProjets ($id=null, $return=null)
	{
		$condition= "seance_id=$id ";
		if (!isset($return)) {
		    $this->set('lastPosition', $this->requestAction("deliberations/getLastPosition/$id"));
		    $this->set('projets', $this->Deliberation->findAll($condition,null,'position ASC'));
		}
		else 
		    return ($this->Deliberation->findAll($condition,null,'position ASC'));
	}

	
	function getDate($id)
    {
		$condition = "Seance.id = $id";
        $objCourant = $this->Seance->findAll($condition);
		return $objCourant['0']['Seance']['date'];
    }

	function getType($id)
    {
		$condition = "Seance.id = $id";
        return $this->Seance->findAll($condition);
    }
	
	function addListUsers($seance_id=null) {
		if (empty($this->data)) {
			$this->data=$this->Seance->read(null,$seance_id);
			$this->set('seance_id',$seance_id);
			$this->set('users', $this->User->generateList('statut=1'));
			if (empty($this->data['SeancesUser'])) { 
				$this->data['SeancesUser'] = null; 
			}
			$this->set('selectedUsers', $this->_selectedArray($this->data['SeancesUser'],'user_id'));
			$this->render();
		} else {	
			
/*			$seance_id = $this->data['Seance']['id'];
			$seancesUser = $this->SeancesUser->find("seance_id=$seance_id");
			foreach ($seancesUser as $seanceUser){
				$this->EffacerListe($seanceUser['id']);
			}*/
		
		
			$this->EffacerListe($this->data['Seance']['id']);
			
			foreach($this->data['User']['id']as $user_id) {
				$this->params['data']['SeancesUser']['id']='';
			    $this->params['data']['SeancesUser']['seance_id'] = $this->data['Seance']['id'];
			    $this->params['data']['SeancesUser']['user_id'] = $user_id ;
			    
			    if ($this->SeancesUser->save($this->params['data']))
			    {
			    	$this->redirect('/seances/listerFuturesSeances');
			    }else
			    {
			    	$this->Session->setFlash('Corrigez les erreurs ci-dessous.');
			    	$this->set('seance_id',$seance_id);
					$this->set('users', $this->User->generateList());
					$this->set('selectedUsers',null);
			    }
				    
			}   	
		}
	}
	
	function effacerListe($seance_id=null) {
		$condition = "seance_id = $seance_id";
		$presents = $this->SeancesUser->findAll($condition);
		foreach($presents as $present)
  		    $this->SeancesUser->del($present['SeancesUser']['id']);
	}
	
	function generateConvocationList ($id=null) {
		$this->set('data', $this->SeancesUser->findAll("seance_id =$id"));
		$type_infos = $this->getType($id);

		$this->set('type_infos', $type_infos );
		$this->set('projets', $this->afficherProjets($id, 1));
		$this->set('jour', $this->Date->days[intval(date('w'))]);
		$this->set('mois', $this->Date->months[intval(date('m'))]);
		$this->set('collectivite',  $this->Collectivite->findAll());
		$this->set('date_seance',  $this->Date->frenchDate(strtotime($type_infos[0]['Seance']['date'])));
	}
}
?>