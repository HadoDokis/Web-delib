<?php
class Localisation extends AppModel {

	var $name = 'Localisation';
	var $displayField="libelle";
	var $validate = array(
		'libelle' => VALID_NOT_EMPTY,
	);

	function hasNoSon ($id_loc) {
		$condition = "parent_id = $id_loc";
		$result = $this->findAll($condition);
		return (0 == count($result));
	}

	function getParent($id_loc) {
		if ($id_loc!=0){
			$condition = "id = $id_loc";
			$parent = $this->findAll($condition);
			if ($parent[0]['Localisation']['parent_id']==0)
				return $id_loc;
			else
				return $parent[0]['Localisation']['parent_id'];
		}else
			return 0;
	}

}
?>