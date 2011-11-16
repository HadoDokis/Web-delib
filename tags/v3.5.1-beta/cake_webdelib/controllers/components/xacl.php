<?php

	App::import('Component','Acl');

	class XaclComponent extends AclComponent {

		public function check($userId, $aco, $action = "*") {
			return parent::check(array('model'=>'Utilisateur', 'foreign_key'=>$userId), $aco, $action);
		}

	}
?>
