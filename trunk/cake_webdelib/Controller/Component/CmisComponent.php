<?php

class CmisComponent extends Component {

	var $client;
	var $folder;

	function CmisComponent() {
		require_once(APP.'/Vendor/cmis_repository_wrapper.php');
		if (Configure::read('USE_GED')) {
			$this->client = new CMISService(Configure::read('GED_URL'), Configure::read('GED_LOGIN'), Configure::read('GED_PASSWD'));
			$this->folder = $this->client->getObjectByPath(Configure::read('GED_REPO'));
		}
	}

	function list_objs($objs) {
		foreach ($objs->objectList as $obj) {
			if ($obj->properties['cmis:baseTypeId'] == "cmis:document") {
				print "Document: " . $obj->properties['cmis:name'] . "\n";
			}
			elseif ($obj->properties['cmis:baseTypeId'] == "cmis:folder") {
				print "Folder: " . $obj->properties['cmis:name'] . "\n";
			} else {
				print "Unknown Object Type: " . $obj->properties['cmis:name'] . "\n";
			}
		}
	}

	function check_response($client) {
		if ($this->client->getLastRequest()->code > 299) {
			print "There was a problem with this request!\n";
			exit (255);
		}
	}
}
?>
