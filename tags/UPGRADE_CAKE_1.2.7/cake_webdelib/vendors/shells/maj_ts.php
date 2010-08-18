<?php

	App::import(array('Model', 'AppModel', 'File'));
	
	class MajTsShell extends Shell{
	
		var $uses = array('Theme', 'Service');
		
		function main() {
			$themes = $this->Theme->find('all', array('conditions'=>array('parent_id'=>0), 'recursive'=>-1));
			$rang = $this->tri($themes, 1, 'Theme');
			
			$services = $this->Service->find('all', array('conditions'=>array('parent_id'=>0), 'recursive'=>-1));
			$rang = $this->tri($services, 1, 'Service');
		}
		
		function tri($peres, $rang, $name) {
			foreach ($peres as $pere) {
				$id=$pere[$name]['id'];
				
				$this->{$name}->read(null, $id);
				$this->{$name}->saveField('lft',$rang);
				$this->{$name}->saveField('rght',($rang+1+2*$this->nbFils($id, $name)));
				
				$fils = $this->{$name}->find('all', array('conditions'=>array('parent_id'=>$id),'recursive'=>-1));
				$rang++;
				if ($fils) $rang = $this->tri($fils,$rang, $name);
				else $rang++;
			}
			$rang++;
			return $rang;
		}
		
		function nbFils($parent_id, $name) {
			$nb = $this->{$name}->find('count',array('conditions'=>array('parent_id'=>$parent_id),'recursive'=>-1));
			$enfants = $this->{$name}->find('all', array('conditions'=>array('parent_id'=>$parent_id), 'recursive'=>-1));
			foreach ($enfants as $enfant) {
				$nb+=$this->nbFils($enfant[$name]['id'], $name);
			}
			return $nb;
		}
	}

?>
