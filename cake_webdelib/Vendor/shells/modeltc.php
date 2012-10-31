<?php

	App::import(array('Model', 'AppModel', 'File'));
	
	class ModeltcShell extends Shell{
		
		function main() {
			if ($this->args && $this->args[0] == '?') {
				return $this->out('Usage: ./cake modeltc <model> [-force] [-all]');
			}
			
			$options = array(
				'force' => false,
				'all' => false,
			);
			
			foreach ($this->params as $key => $val) {
				foreach ($options as $name => $option) {
					if (isset($this->params[$name]) || isset($this->params['-'.$name]) || isset($this->params[$name{0}])) {
						$options[$name] = true;
					}
				}
			}
			
			if ($options['all']) {
				$dir=opendir(sprintf('%smodels/',APP));
				while (($file = readdir($dir)) !== false) {
					$explose=explode('~',$file);
					if ((count($explose)==1)&&!is_dir($file)) $this->args[] = $file;
        		}
        		closedir($dir);
        		if (!file_exists(sprintf('%sapp_model.php',APP))) $this->args[]='app';
			}
			else {
				foreach($this->args as $model) {
					if ($model=='app') {
						if (!file_exists(sprintf('%sapp_model.php',APP))) $this->err('The app model don\'t exists.');
					}
					else {
					}
						if (!file_exists(sprintf('%smodel/%s.php',APP,$model))) $this->err(sprintf('The %s model don\'t exists.',$model));
				}
			}
			
			if (empty($this->args)) {
				return $this->err('Usage: ./cake modeltc <model>');
			}
			
			foreach ($this->args as $model) {
				$name = Inflector::classify($model);
				$explose = explode('.php',$name);
				$name = $explose[0];
				$file = sprintf('%stests/cases/models/%s.test.php', APP, Inflector::underscore($name));
				$File = new File($file);
				if ($File->exists() && !$options['force']) {
					$this->err(sprintf('File %s already exists, use --force option.', $file));
					continue;
				}
				
				$out = array();
				$out[] = '<?php';
				$out[] = '';
				$out[] = '	require_once( dirname( __FILE__ ).\'/../cake_app_model_test_case.php\' );';
				$out[] = '';
				$out[] = sprintf('	App::import(\'Model\', \'%s\');',$name);
				$out[] = '';
				$out[] = sprintf('	class %sTestCase extends CakeAppModelTestCase {', $name);
				$out[] = '';
				/*$out[] = '		public function testFunction() {';
				$out[] = '			';
				$out[] = '		}';
				$out[] = '';*/
				$out[] = '	}';
				$out[] = '';
				$out[] = '?>';
				
				$File->write(join("\n", $out));
				$this->out(sprintf('-> Create %s TestCase', $name));
			}
		}
	}

?>
