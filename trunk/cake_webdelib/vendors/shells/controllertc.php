<?php

	App::import(array('Model', 'AppModel', 'File'));
	
	class ControllertcShell extends Shell{
		
		function main() {
			if ($this->args && $this->args[0] == '?') {
				return $this->out('Usage: ./cake controllertc <controller> [-force] [-all]');
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
				$dir=opendir(sprintf('%scontrollers/',APP));
				while (($file = readdir($dir)) !== false) {
					$explose=explode('~',$file);
					if ((count($explose)==1)&&(!is_dir($file))&&($file!='.svn')&&($file!='empty')) $this->args[] = $file;
        		}
        		closedir($dir);
        		if (!file_exists(sprintf('%sapp_controller.php',APP))) $this->args[]='app';
			}
			else {
				foreach($this->args as $controller) {
					if ($controller=='app') {
						if (!file_exists(sprintf('%sapp_controller.php',APP))) $this->err('This app controller don\'t exists.');
					}
					else {
						if (!file_exists(sprintf('%scontrollers/%s_controller.php',APP,$controller))) $this->err(sprintf('The %s controller don\'t exists.',$controller));
					}
				}
			}
			
			if (empty($this->args)) {
				return $this->err('Usage: ./cake controllertc <controller>');
			}
			
			foreach ($this->args as $controller) {
				$explose = explode('.php',$controller);
				$nameFile = $explose[0];
				$name = Inflector::classify($controller);
				$explose = explode('Controller.php',$name);
				$name = $explose[0];
				
				$file = sprintf('%stests/cases/controllers/%s.test.php', APP, Inflector::underscore($nameFile));
				$File = new File($file);
				if ($File->exists() && !$options['force']) {
					$this->err(sprintf('File %s already exists, use --force option.', $file));
					continue;
				}
				
				$out = array();
				$out[] = '<?php';
				$out[] = '';
				$out[] = '	require_once( dirname( __FILE__ ).\'/../cake_app_controller_test_case.php\' );';
				$out[] = '';
				$out[] = sprintf('	App::import(\'Controller\', \'%s\');',$name);
				$out[] = '';
				$out[] = sprintf('	class Test%sController extends %sController {',$name,$name);
				$out[] = '';
				$out[] = '		var $autoRender = false;';
				$out[] = '		var $redirectUrl;';
				$out[] = '		var $redirectStatus;';
				$out[] = '		var $renderedAction;';
				$out[] = '		var $renderedLayout;';
				$out[] = '		var $renderedFile;';
				$out[] = '		var $stopped;';
				$out[] = sprintf('		var $name=\'%s\';',$name);
				$out[] = '';
				$out[] = '		public function redirect($url, $status = null, $exit = true) {';
				$out[] = '			$this->redirectUrl = $url;';
				$out[] = '			$this->redirectStatus = $status;';
				$out[] = '		}';
				$out[] = '';
				$out[] = '		public function render($action = null, $layout = null, $file = null) {';
				$out[] = '			$this->renderedAction = $action;';
				$out[] = '			$this->renderedLayout = (is_null($layout) ? $this->layout : $layout);';
				$out[] = '			$this->renderedFile = $file;';
				$out[] = '		}';
				$out[] = '';
				$out[] = '		public function _stop($status = 0) {';
				$out[] = '			$this->stopped = $status;';
				$out[] = '		}';
				$out[] = '';
				$out[] = '		public function assert( $condition, $error = \'error500\', $parameters = array() ) {';
				$out[] = '			$this->condition = $condition;';
				$out[] = '			$this->error = $error;';
				$out[] = '			$this->parameters = $parameters;';
				$out[] = '		}';
				$out[] = '';
				$out[] = '	}';
				$out[] = '';
				$out[] = sprintf('	class %sControllerTest extends CakeAppControllerTestCase {', $name);
				$out[] = '';
				
				$out[] = '		public function testFunction() {';
				$out[] = '			';
				$out[] = '		}';
				$out[] = '';
				$out[] = '	}';
				$out[] = '';
				$out[] = '?>';
				
				$File->write(join("\n", $out));
				$this->out(sprintf('-> Create %s TestCase', $name));
			}
		}
	}

?>
