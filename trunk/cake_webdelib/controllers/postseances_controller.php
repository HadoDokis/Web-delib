<?php
class PostseancesController extends AppController {

	var $name = 'Postseances';
	var $helpers = array('Html', 'Form', 'Javascript', 'Fck', 'fpdf', 'Html2' );
	var $components = array('Date');
	var $uses = array('Deliberation', 'Seance', 'User', 'Collectivite', 'Listepresence', 'Vote', 'Model', 'Theme', 'Typeseance');

	// Gestion des droits
	var $aucunDroit = array('getNom', 'getPresence', 'getVote');
	var $commeDroit = array('changeObjet'=>'Postseances:index', 'afficherProjets'=>'Postseances:index', 'generateDeliberation'=>'Postseances:index', 'generatePvComplet'=>'Postseances:index', 'generatePvSommaire'=>'Postseances:index', 'changeStatus'=>'Postseances:index', 'downloadPV'=>'Postseances:index');

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

        function changeStatus ($seance_id) {
            $result = false;
            $this->data=$this->Seance->read(null,$seance_id);

            // Avant de cloturer la séance, on stock les délibérations en base de données au format pdf
            $result = $this->_stockPvs($seance_id);
exit;
            if ($result || $this->data['Typeseance']['action']== 1) {
                $this->data['Seance']['pv_figes']=1;
                if ($this->Seance->save($this->data))
                $this->redirect('/postseances/afficherProjets/'.$seance_id);
            }
            else
                $this->Session->setFlash("Au moins un PV n'a pas été généré correctement...");
        }

        function _stockPvs($seance_id) {
	    require_once ('vendors/progressbar.php');
	    Initialize(200, 100,200, 30,'#000000','#FFCC00','#006699');
            $result = true;

            $seance = $this->Seance->read(null, $seance_id);
            $model_pv_sommaire = $seance['Typeseance']['modelpvsommaire_id'];
            $model_pv_complet  = $seance['Typeseance']['modelpvdetaille_id'];
	    ProgressBar(1, 'Génération du PV Sommaire');
            $retour1 = $this->requestAction("/models/generer/null/$seance_id/$model_pv_sommaire/0/1/pv_sommaire.pdf/1/false");
	    ProgressBar(50, 'Génération du PV Complet');
            $retour2 = $this->requestAction("/models/generer/null/$seance_id/$model_pv_complet/0/1/pv_complet.pdf/1/false");
            ProgressBar(99, 'Sauvegarde des PVs');
            echo ('<script>');
            echo ('    document.getElementById("pourcentage").style.display="none"; ');
            echo ('    document.getElementById("progrbar").style.display="none";');
            echo ('    document.getElementById("affiche").style.display="none";');
            echo ('    document.getElementById("contTemp").style.display="none";');
            echo ('</script>');
            $path = WEBROOT_PATH."/files/generee/PV/$seance_id";
            $pv_sommaire = file_get_contents("$path/pv_sommaire.pdf");
            $pv_complet = file_get_contents("$path/pv_complet.pdf");

	    if (!empty($pv_sommaire) && !empty($pv_complet)) {
	        $seance['Seance']['pv_figes'] = 1 ;
	        $seance['Seance']['pv_sommaire'] = $pv_sommaire ;
	        $seance['Seance']['pv_complet'] = $pv_complet;
                if ($this->Seance->save($seance))
		    die ("Enregistrement des pvs effectués<br> <a href='/postseances/index'>Retour en Post-Séances</a>");
	    }
	    else {
	        echo('Au moins une génération a échouée, les pvs ne peuvent être figés');
		die ("<br> <a href='/postseances/index'>Retour en Post-Séances</a>'");
            }   
	}

        function downloadPV($seance_id, $type) {
            $seance = $this->Seance->read(null, $seance_id);
            header('Content-type: application/pdf');
            if ($type == "sommaire") {
                header('Content-Length: '.strlen($seance['Seance']['pv_sommaire']));
                header('Content-Disposition: attachment; filename=pv_sommaire.pdf');
                die($seance['Seance']['pv_sommaire']);
            }
            else { 
                header('Content-Length: '.strlen($seance['Seance']['pv_complet']));
                header('Content-Disposition: attachment; filename=pv_complet.pdf');
                die($seance['Seance']['pv_complet']);
            }
        }


}
?>
