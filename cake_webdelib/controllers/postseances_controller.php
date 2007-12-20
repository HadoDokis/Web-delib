<?php
class PostseancesController extends AppController {

	var $name = 'Postseances';
	var $helpers = array('Html', 'Form', 'Javascript', 'Fck', 'fpdf', 'Html2' );
	var $components = array('Date');
	var $uses = array('Deliberation','Seance','User','SeancesUser', 'Collectivite', 'Listepresence', 'Vote','Model','Theme');

	function index() {
		$this->Postseances->recursive = 0;
		$condition= 'date <= "'.date('Y-m-d H:i:s').'"';
		$seances = $this->Seance->findAll(($condition),null,'date asc');

		for ($i=0; $i<count($seances); $i++)
		    $seances[$i]['Seance']['date'] = $this->Date->frenchDateConvocation(strtotime($seances[$i]['Seance']['date']));

		$this->set('seances', $seances);

	}

	function afficherProjets ($id=null, $return=null)
	{
		$condition= "seance_id=$id AND etat>=2";
		if (!isset($return)) {
		    $this->set('lastPosition', $this->requestAction("deliberations/getLastPosition/$id"));
			$deliberations = $this->Deliberation->findAll($condition,null,'position ASC');
		    $this->set('seance_id', $id);
		    $this->set('projets', $deliberations);
		    $this->set('date_seance', $this->Date->frenchDateConvocation(strtotime($this->requestAction("seances/getDate/$id"))));
		}
		else
		    return ($this->Deliberation->findAll($condition,null,'position ASC'));
	}

	function getVote($id_delib){
		$condition = "delib_id = $id_delib";
		$votes = $this->Vote->findAll($condition);
		if (!empty($votes)){$resultat =$votes[0]['Vote']['commentaire'];
		return $resultat;}
	}
	
	function generatePvSommaire ($id=null) {
		$delib=array();
		$this->set('presents', $this->Listepresence->findAll("seance_id =$id AND present=1"));
		$this->set('absents', $this->Listepresence->findAll("seance_id =$id AND present=0"));
		$seance = $this->Seance->findAll("Seance.id = $id");
		$this->set('seance',$seance);
		$this->set('model',$this->Model->findAll());
		$condition= "seance_id=$id AND etat>=2";
		$projets =  $this->Deliberation->findAll($condition,null,'position ASC');
		$this->set('jour', $this->Date->days[intval(date('w'))]);
		$this->set('mois', $this->Date->months[intval(date('m'))]);
		$this->set('collectivite',  $this->Collectivite->findAll());
		$this->set('date_seance',  $this->Date->frenchDateConvocation(strtotime($seance[0]['Seance']['date'])));
		foreach ($projets as $proj)
		{
			$projId = $proj['Deliberation']['id'];
			$proj['vote']= $this->getVote($projId);
			array_push($delib, $proj);	
		}
		$this->set('projets',$delib);	
	}

	function generatePvComplet ($id=null) {
		$delib=array();
		$this->set('presents', $this->Listepresence->findAll("seance_id =$id AND present=1"));
		$this->set('absents', $this->Listepresence->findAll("seance_id =$id AND present=0"));
		$seance = $this->Seance->findAll("Seance.id = $id");
		$this->set('seance',$seance);
		$this->set('model',$this->Model->findAll());
		//$this->set('themes',$this->Theme->findAll());
		$condition= "seance_id=$id AND etat>=2";
		$projets =  $this->Deliberation->findAll($condition,null,'position ASC');
		$this->set('jour', $this->Date->days[intval(date('w'))]);
		$this->set('mois', $this->Date->months[intval(date('m'))]);
		$this->set('collectivite',  $this->Collectivite->findAll());
		$this->set('date_seance',  $this->Date->frenchDateConvocation(strtotime($seance[0]['Seance']['date'])));
		foreach ($projets as $proj)
		{
			$projId = $proj['Deliberation']['id'];
			$proj['vote']= $this->getVote($projId);
			array_push($delib, $proj);	
		}
		$this->set('projets',$delib);	
	}

	function generateDeliberation ($id=null, $dl=1) {
		$this->set('id',$id);
		$this->set('model',$this->Model->findAll());
		$this->set('themes',$this->Theme->findAll());
		$projet = $this->Deliberation->findAll("Deliberation.id=$id");
		$this->set('projet',$projet);
		$this->set('jour', $this->Date->days[intval(date('w'))]);
		$this->set('mois', $this->Date->months[intval(date('m'))]);
		$this->set('collectivite',  $this->Collectivite->findAll());
		$this->set('date_seance',  $this->Date->frenchDateConvocation(strtotime($projet[0]['Seance']['date'])));
		//debug($projet);
		$this->set('votespour', $this->Vote->findAll("delib_id=$id AND resultat=1"));
		$this->set('abstenus', $this->Vote->findAll("delib_id=$id AND resultat in (2,3)"));
		$this->set('vote',$this->Vote->findAll("delib_id=$id"));
		$this->set('dl', $dl);
	}


	function getNom($id)
	{
		$data = $this->User->findAll("User.id = $id");
		return $data['0']['User']['nom'].' '.$data['0']['User']['prenom'];
	}

}
?>