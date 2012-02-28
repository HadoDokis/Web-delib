<?php

	App::import('Model', 'AppModel');
	
	class MajArosAcosShell extends Shell{
	
		public $uses = array('Aco', 'Aro', 'AroAco');
		
		function main() {
			$acos = $this->Aco->find('all', array('recursive'=>-1));
			foreach($acos as $aco) {
				$parent = $this->getParentFromTree($this->Aco, $aco['Aco']['id'], 'Aco');
				if (empty($parent)) $parent['Aco']['id']=0;
					$this->Aco->read(null, $aco['Aco']['id']);
				$this->Aco->set('parent_id', $parent['Aco']['id']);
				$this->Aco->save();
			}
			
			$aros = $this->Aro->find('all', array('recursive'=>-1));
			foreach($aros as $aro) {
                            $this->Aro->query("INSERT INTO aros_ados (aro_id, ado_id, _create, _read, _update, _delete) 
                                               VALUES (".$aro['Aro']['foreign_key'].", 1, 1, 1 ,1 ,1 );");

				$parent = $this->getParentFromTree($this->Aro, $aro['Aro']['id'], 'Aro');
				if (empty($parent)) $parent['Aro']['id']=0;
					$this->Aro->read(null, $aro['Aro']['id']);
				$this->Aro->set('parent_id', $parent['Aro']['id']);
				$this->Aro->save();
			}

			//$this->addMissingArosAcos();
		}
		
		function getParentFromTree($Model, $id, $modelName) {
			$droit = $Model->find('first',array('conditions'=>array('id'=>$id)));
			$conditions = array(
				$modelName.'.lft <' => $droit[$modelName]['lft'],
				$modelName.'.rght >' => $droit[$modelName]['rght'],
			);
			return $Model->find('first', array(
				'conditions' => $conditions,
				'order' => array($modelName.'.lft' => 'desc'),
			));
		}
		
		/*function addMissingArosAcos() {
			$new_rights=array('Deliberations:sendToParapheur', 'Deliberations:toSend', 'Deliberations:verserAsalae', 'Deliberations:goNext');
		
			$aros = $this->Aro->find('all',array('recursive'=>-1));
			foreach($aros as $aro) {
				foreach($new_rights as $new_right) {
					$aco = $this->Aco->find('first',array('conditions'=>array('alias LIKE'=>$new_right)));
					debug($aco);
					$aro_aco = $this->AroAco->find('first',array('conditions'=>array('aro_id'=>$aro['Aro']['id'],'aco_id'=>$aco['Aco']['id']),'recursive'=>-1));
					if (empty($aro_aco)) {
						$this->AroAco->create();
						$data['aro_id']=$aro['Aro']['id'];
						$data['aco_id']=$aco['Aco']['id'];
						$data['create']=-1;
						$data['read']=-1;
						$data['update']=-1;
						$data['delete']=-1;
						$this->AroAco->save($data);
					}
				}
			}
		}*/
		
	}

?>
