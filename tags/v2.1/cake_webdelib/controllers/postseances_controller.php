<?php
class PostseancesController extends AppController {

	var $name = 'Postseances';
	var $helpers = array('Html', 'Form', 'Javascript', 'Fck', 'fpdf', 'Html2' );
	var $components = array('Date');
	var $uses = array('Deliberation', 'Seance', 'User', 'Collectivite', 'Listepresence', 'Vote', 'Model', 'Theme', 'Typeseance');

	// Gestion des droits
	var $aucunDroit = array('getNom', 'getPresence', 'getVote');
	var $commeDroit = array('changeObjet'=>'Postseances:index', 'afficherProjets'=>'Postseances:index', 'generateDeliberation'=>'Postseances:index', 'generatePvComplet'=>'Postseances:index', 'generatePvSommaire'=>'Postseances:index');

	function index() {
		$this->set ('USE_GEDOOO', USE_GEDOOO);
		$this->Postseances->recursive = 0;
		$condition= 'Seance.traitee = 1';
		$seances = $this->Seance->findAll(($condition),null,'date desc');

		for ($i=0; $i<count($seances); $i++)
		    $seances[$i]['Seance']['date'] = $this->Date->frenchDateConvocation(strtotime($seances[$i]['Seance']['date']));

		$this->set('seances', $seances);

	}

	function afficherProjets ($id=null, $return=null)
	{
	    $this->set ('USE_GEDOOO', USE_GEDOOO);
	    $condition= "seance_id=$id AND etat>=2";
	    if (!isset($return)) {
	        $this->set('lastPosition', $this->Deliberation->getLastPosition($id));
	        $deliberations = $this->Deliberation->findAll($condition,null,'Deliberation.position ASC');
	        for ($i=0; $i<count($deliberations); $i++)
		     $deliberations[$i]['Model']['id'] = $this->Typeseance->modeleProjetDelibParTypeSeanceId($deliberations[$i]['Seance']['type_id'], $deliberations[$i]['Deliberation']['etat']);
		$this->set('seance_id', $id);
		$this->set('projets', $deliberations);
		$this->set('date_seance', $this->Date->frenchDateConvocation(strtotime($this->requestAction("seances/getDate/$id"))));
	    }
	    else
	        return ($this->Deliberation->findAll($condition,null,'Deliberation.position ASC'));
	}

	function getVote($id_delib){
		$condition = "delib_id = $id_delib";
		$votes = $this->Vote->findAll($condition);
		if (!empty($votes)){
			$resultat =$votes[0]['Vote']['commentaire'];
			return $resultat;
		}
	}

	function getPresence($id_delib,$present){
		$condition ="delib_id =$id_delib AND present=$present";
		$presences = $this->Listepresence->findAll($condition);
		return $presences;
	}

	function generatePvSommaire ($id=null) {
		vendor('fpdf/html2fpdf');
	    $pdf = new HTML2FPDF();
	    $pdf->AddPage();
	    $pdf->WriteHTML($this->requestAction("/models/generatePVSommaire/$id"));
	    $pos =  strrpos ( getcwd(), 'webroot');
	    $path = substr(getcwd(), 0, $pos);
	    $seance_path = $path."webroot/files/seances/PV_Sommaire_$id.pdf";
	    $pdf->Output($seance_path ,'F');
	    $pdf->Output("PV_sommaire_$id.pdf",'D');
	}

	function generatePvComplet ($id=null) {
		vendor('fpdf/html2fpdf');
	    $pdf = new HTML2FPDF();
	    $pdf->AddPage();
	    $pdf->WriteHTML($this->requestAction("/models/generatePVDetaille/$id"));
	    $pos =  strrpos ( getcwd(), 'webroot');
	    $path = substr(getcwd(), 0, $pos);
	    $seance_path = $path."webroot/files/seances/PV_Complet_$id.pdf";
	    $pdf->Output($seance_path ,'F');
	    $pdf->Output("PV_Complet_$id.pdf",'D');
	}

	function generateDeliberation ($id=null, $dl=1) {
	    vendor('fpdf/html2fpdf');
	    $pdf = new HTML2FPDF();
	    $pdf->AddPage();
	    $pdf->WriteHTML($this->requestAction("/models/generateDeliberation/$id"));
	    $pos =  strrpos ( getcwd(), 'webroot');
	    $path = substr(getcwd(), 0, $pos);
	    $delib_path = $path."webroot/files/delibs/DELIBERATION_$id.pdf";
	    $pdf->Output($delib_path ,'F');
	    $pdf->Output('deliberation.pdf','D');
	}


	function getNom($id)
	{
		$data = $this->User->findAll("User.id = $id");
		return $data['0']['User']['prenom'].' '.$data['0']['User']['nom'];
	}

	function changeObjet($delib_id) {
		$this->set('delib_id', $delib_id);

	    if (!empty($this->data)) {
	        $data = $this->Deliberation->read(null, $delib_id);

			$data['Deliberation']['objet'] = $this->data['Deliberation']['objet'];
			if ($this->Deliberation->save($data))
			     $this->redirect('/deliberations/transmit');
	    }
	}

}
?>
