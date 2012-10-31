<?php

	App::import(array('Model', 'AppModel', 'File'));
	
	class ListefixturesShell extends Shell{
		
		function main() {
			if ($this->args && $this->args[0] == '?') {
				return $this->out('Usage: ./cake listefixtures [-force]');
			}
			
			$options = array(
				'force' => false
			);
			
			foreach ($this->params as $key => $val) {
				foreach ($options as $name => $option) {
					if (isset($this->params[$name]) || isset($this->params['-'.$name]) || isset($this->params[$name{0}])) {
						$options[$name] = true;
					}
				}
			}
			
			$dir=opendir(sprintf('%stests/fixtures/',APP));
			while (($file = readdir($dir)) !== false) {
				$explose=explode('~',$file);
				if ((count($explose)==1)&&!is_dir($file)) $this->args[] = $file;
    		}
    		closedir($dir);
			
			$file = sprintf('%stests/cases/cake_app_test_case.php', APP);
			$File = new File($file);
			if ($File->exists() && !$options['force']) {
				$this->err(sprintf('File %s already exists, use --force option.', $file));
			}
			else {
				$out = array();
				$out[] = '<?php';
				$out[] = '';
				$out[] = '	if( !defined( \'CAKEPHP_UNIT_TEST_EXECUTION\' ) ) {';
				$out[] = '		define( \'CAKEPHP_UNIT_TEST_EXECUTION\', 1 );';
				$out[] = '	}';
				$out[] = '';
				$out[] = '	ClassRegistry::config(array(\'ds\' => \'test_suite\'));';
				$out[] = '';
				$out[] = '	class CakeAppTestCase extends CakeTestCase {';
				$out[] = '';
				$out[] = '		/**';
				$out[] = '		* Tables de données à utiliser';
				$out[] = '		*/';
				$out[] = '';
				$out[] = '		public $fixtures = array (';
				foreach ($this->args as $fixture) {
					$explose = explode('_',$fixture);
					$name=Inflector::singularize($explose[0]);
					for ($i=1;$i<count($explose)-1;$i++) {
						$name.='_'.Inflector::singularize($explose[$i]);
					}
				
					$out[] = '			\'app.'.$name.'\',';
				}
				$out[] = '		);';
				$out[] = '';
				$out[] = '		function startCase() { Cache::clear(); clearCache(); }';
				$out[] = '';
				$out[] = '	}';
				$out[] = '';
				$out[] = '?>';
			
				$File->write(join("\n", $out));
				$this->out('-> File created');
			}
		}
	}

?>
