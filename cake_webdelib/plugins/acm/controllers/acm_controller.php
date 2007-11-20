<?php
class AcmController extends AcmAppController
{
	var $name = 'Acm';


	//Model
	var $uses = array("AcmUser", "AcmRole", "AcmPrivilege");

	//View
	var $components = array('Acl');
	var $helpers = array('Html', 'Ajax', 'Javascript', 'Acm');
	var $layout = 'cpanel';//'default';
	var $autoRender = false;

	//Variables
	var $aroTree = array();
	var $roleModel = 'AcmRole';
	var $userModel = 'AcmUser';


	function __construct()
	{
		parent::__construct();
		if(ACMAUTOLOAD)
		{
			if(defined('ACMUSERS') && defined('ACMROLES'))
			{
				$conv = new Inflector();
				//Get Roles
				$this->roleModel = $conv->camelize($conv->singularize(ACMROLES));
				                   array_push($this->uses,$this->roleModel);
				//Get Users
				$this->userModel = $conv->camelize($conv->singularize(ACMUSERS));
							       array_push($this->uses,$this->userModel);
			}
			else
			{
			}
		}
	}

	function index()
	{
		$this->render();
	}

	/*ACL Method */

	function aclgui()
	{

		$data = array();

		//Get Aro Tree
		$Aro = new Aro;
		$aros = $Aro->findAll('','','lft ASC', 500);
		if(isset($aros) && is_array($aros) && count($aros))
		{
			$data['aros'] = $this->_get_nested_array($aros, 'Aro');
		}

		$arolist = array();
		foreach($aros AS $aro)
		{
			$arolist[$aro['Aro']['alias']] = $aro['Aro']['alias'];
		}
		$this->set('arolist', $arolist);


		//Get ACO Tree
		$Aco = new Aco;
		$acos = $Aco->findAll('','','lft ASC', 500);

		if(isset($acos) && is_array($acos) && count($acos))
		{
			$data['acos'] = $this->_get_nested_array($acos, 'Aco');
		}

		$acolist = array();
		foreach($data['acos'] AS $aco)
		{
			$acolist[$aco['alias']] = $aco['alias'];
		}

		$this->set('acolist', $acolist);

		$this->render();
	}

	function acl()	{

		$this->layout='';
	 	$data['aro'] = $this->_getAxo('Aro', $this->params['form']['aro']);
		$data['aco'] = $this->_getAxo('Aco', $this->params['form']['aco']);
		$data = $this->_getAclArray($data);

		//debug($data['acl']);

		$this->set('data', $data);
		$this->render();
	}

	function allow()
	{
		list($aro,$controller,$method) = split(':', $this->params['url']['id']);
		$aco = $controller.":".$method;
		$this->Acl->allow($aro, $aco);
		echo "You <span style='color:green;'>granted</span> <b> ",strtoupper($aro) ." </b> access to <b><i>$aco</i></b>";
	}

	function deny()
	{
		list($aro,$controller,$method) = split(':', $this->params['url']['id']);
		$aco = $controller.":".$method;
		$this->Acl->deny($aro, $aco);
		echo "You <span style='color:red;'>denied</span> <b> ",strtoupper($aro) ." </b> access to <b><i>$aco</i></b>";

	}

	/* User Methods */

	function users()
	{

		$data = array();

		//Get Aro Tree
		$Aro = new Aro;
		$aros = $Aro->findAll('','','lft ASC', 500);
		if(isset($aros) && is_array($aros) && count($aros))
		{
			$data = $this->_get_nested_array($aros, 'Aro');
			$this->set('data', $data);

		}

		$arolist = array();
		foreach($aros AS $aro)
		{
			$arolist[$aro['Aro']['alias']] = $aro['Aro']['alias'];
		}
		$this->set('arolist', $arolist);

		$this->render();
	}

	function create()
	{
		if (!empty($this->params['data']))
		{
			if ($this->_aclCreateAro(0, $this->params['data']['AcmUser']['parent'], $this->params['data']['AcmUser']['alias']))
			{
				$this->flash($this->params['data']['AcmUser']['alias'].'</b> Has Been Added.','/acm/Users',$pause=1);
			}
			else
			{
				$this->set('data', $this->params['data']);
				$this->validateErrors($this->AcmUser);
			}
		}
		$this->action = 'users';
		$this->users();
	}


	function remove($id)
	{
		$Aro = new Aro;
		$Aro->remove($id);
		$this->redirect('/acm/');
	}

	/*Privileges Methods*/

	function privileges()
	{
		$this->set('controllers', $this->_getCakeControllers());
		$this->render();
	}

	function set_controller($controllerName,$status)
	{
		switch($status)
		{
			case "off":
			$this->_unloadControllerClassesToAco($controllerName);
			$altstatus = "on";
			break;

			case "on":
			$this->_loadControllerClassesToAco($controllerName);
			$altstatus = "off";
			break;
			default:
			break;
		}
		//display the opposites
		$this->set('controller',array("name"=>$controllerName,"status"=>$status, "altstatus"=>$altstatus));
		$this->render('set_controller','ajax');
	}

	/* Role Methods */

	function roles()
	{
		$data = array();

		//Get Aro Tree
		$Aro = new Aro;
		$aros = $Aro->findAll('','','lft ASC', 500);
		if(isset($aros) && is_array($aros) && count($aros))
		{
			$data['aros'] = $this->_get_nested_array($aros, 'Aro');
		}

		//Get ACO Tree
		$Aco = new Aco;
		$acos = $Aco->findAll('','','lft ASC', 500);

		if(isset($acos) && is_array($acos) && count($acos))
		{
			//pr($acos);
			$data['acos'] = $this->_get_nested_array($acos, 'Aco');
		}

		$this->set('data', $data);

		$this->render();
	}

	function auto()
	{
		$this->_autoLoad();
		$this->render('autoload');
	}


	/* PRIVATE METHODS */

	function _getAxo($x,$xalias=null)
	{
		$Axo = new $x;
		$axos = $Axo->findAll('','','lft ASC', 500);
		if(isset($axos) && is_array($axos) && count($axos))
		{
			$axoArray = $this->_get_nested_array($axos, $x);
		}

		if(is_null($xalias))
		{
			return $axoArray;
		}

		foreach($axoArray AS $axo)
		{
			if($axo['alias'] == $xalias)
			{
				return $axo;
			}
			if(!empty($axo['children']))
			{
			  foreach($axo['children'] AS $child)
			  {
			  	if($child['alias'] == $xalias)
				{
					return $child;
				}
			  }
			}
		}
	}

	function _getAclArray($data)
	{
		$aro = $data['aro']['alias'];

		foreach($data['aco']['children'] AS $aco)
		{
		    if($this->_checkAccess($aro, $aco['alias']))
		    {
		    	$data['acl'][$aro][$data['aco']['alias']][$aco['alias']] = "allow";
		    }
		    else
		    {
		    	$data['acl'][$aro][$data['aco']['alias']][$aco['alias']] = "deny";
		    }
		}

		if(!empty($data['aro']['children']))
		{
			foreach($data['aro']['children'] AS $child)			{

				foreach($data['aco']['children'] AS $aco)
				{
				    if($this->_checkAccess($child['alias'], $aco['alias']))
				    {
				    	$data['acl'][$aro]['children'][$child['alias']][$data['aco']['alias']][$aco['alias']] = "allow";
				    }
				    else
				    {
				    	$data['acl'][$aro]['children'][$child['alias']][$data['aco']['alias']][$aco['alias']] = "deny";
				    }
				}

			}
		}

		return $data;
		}

	function _checkAccess($aro, $aco)
	{
		 // Check access using the component:
        $access = $this->Acl->check($aro, $aco, $action = "*");

        //access denied
        if ($access === false)
        {
            return false;
        }
        //access allowed
        else
        {
            return true;
        }
	}

	function _getCakeControllers()
	{
		$controllers = array();
		$controllerList = listClasses(APP."/controllers/");
		foreach($controllerList AS $controller => $file)
		{
			list($name) = explode('.',$file);
			$controllerName = Inflector::camelize(str_replace('_controller','',$name));
			$Aco = new Aco;
			$acos = $Aco->findByAlias($controllerName);
			//pr($acos);
			if(!empty($acos['Aco']['alias']))
			{
				$status = "on";
				$altstatus = "off";
			}else
			{
				$status = "off";
				$altstatus = "on";
			}
			$controllers[] = array("name"=>$controllerName, "file"=>$file, "status"=>$status, "altstatus"=>$altstatus);
		}
		if(!empty($controllers))
		{
			return $controllers;
		}
		else
		{
			return false;
		}
	}

	function tester()
	{
		$aro = new Aro();

		$aro->create( 1, null, 'Bob Marley' );
		$aro->create( 2, null, 'Jimi Hendrix');
		$aro->create( 3, null, 'George Washington');
		$aro->create( 4, null, 'Abraham Lincoln');
		// Now, we can make groups to organize these users:
		// Notice that the IDs for these objects are 0, because
		//     they will never tie to users in our system

		$aro->create(0, null, 'Presidents');
		$aro->create(0, null, 'Artists');



		//Now, hook AROs to their respective groups:

		$aro->setParent('Presidents', 'George Washington');
		$aro->setParent('Presidents', 'Abraham Lincoln');
		$aro->setParent('Artists', 'Jimi Hendrix');
		$aro->setParent('Artists', 'Bob Marley');

		echo $this->Acl->getAro('Bob Marley');
	}

	function _autoLoad()
	{
		$aro = new Aro();

		$rakey = ACMROLE_ALIAS;
		$uakey = ACMUSER_ALIAS;

		$roles = $this->{$this->roleModel}->findAll();
		foreach($roles AS $role)
		{
			$rid = 0;
			$ralias =  $role[$this->roleModel][$rakey];

				$aro->create($rid, null, $ralias);

				if(!empty($role[$this->userModel]))
				{
					foreach($role[$this->userModel] AS $user)
					{
						$uid = $user['id'];
						$ualias = $user[$uakey];

							$aro->create($uid, null, $ualias);
							$aro->setParent($ralias, $ualias);

					}
				}
		}
	}

	function _getControllerMethods($controllerName)
	{
		$file = APP."controllers".DS.Inflector::underscore($controllerName)."_controller.php";
		require_once($file);
		$parentClassMethods = get_class_methods('AppController');
		$subClassMethods = get_class_methods($controllerName.'Controller');
		$classMethods = array_diff($subClassMethods, $parentClassMethods);

		$subClassVars = get_class_vars($controllerName.'Controller');
		if(in_array('scaffold', array_keys($subClassVars)))
		{
			$scaffold_file = CAKE."libs".DS."controller".DS."scaffold.php";
			require_once($scaffold_file);
			$scaffoldClassMethods = get_class_methods("Scaffold");
			$scaffoldMethods = array_diff($scaffoldClassMethods, $parentClassMethods);
			foreach($scaffoldMethods AS $sMethod)
			{
				$classMethods[] = $sMethod;
			}
		}

		return $classMethods;
	}

	function _loadControllerClassesToAco($controllerName)
	{
		$controllerMethods = $this->_getControllerMethods($controllerName);

		$Aco = new Aco();
		$Aco->create(0, null, $controllerName);

		$i=1;
		foreach($controllerMethods As $method)
		{
			if($method == "__construct" || $method == $controllerName.'Controller' || $method == strtolower($controllerName.'Controller'))
			{
				break;
			}
			else
			{
				if($method{0} != "_" || strstr($method,'__scaffold'))
				{
					$acoAlias = $controllerName . ":" . $method;
					$Aco->create($i, $controllerName, $acoAlias);
				}
			}
			$i++;
		}
	}

	function _unloadControllerClassesToAco($controllerName)
	{
		$controllerMethods = $this->_getControllerMethods($controllerName);

		$Aco = new Aco();

		$class = $Aco->find(array('alias'=>$controllerName));
		$Aco->del($class['Aco']['id']);

		$i=0;
		foreach($controllerMethods As $method)
		{
			if($method == "__construct" || $method == $controllerName.'Controller' || $method == strtolower($controllerName.'Controller'))
			{
				break;
			}
			else
			{
				if($method{0} != "_" || strstr($method,'__scaffold'))
				{
					$acoAlias = $controllerName . ":" . $method;
					$class = $Aco->find(array('alias'=>$acoAlias));
					$Aco->del($class['Aco']['id']);
				}
			}
			$i++;
		}
	}

	function _get_nested_array(&$array, $obj, $offset = 0, $rightMax = 0)
	{
		/*$array:	   is the stuff we get from the database
		$obj:	   is it Aro or Aco
		$offset:   is our position in the array
		$rightMax: is the rght Value where we are done with our recursive level */

		// If our offset is 0, means we are at recursive level 0, we set our $rightMax to the right value of the last element in the array
		if ($offset==0)
		$rightMax = $array[count($array)-1][$obj]['rght'];;
		// $num is just the arrayId for stuff on the same recursive level
		$num = 0;
		do {
			$row = $array[$offset][$obj];  // Our $row is the Aro item at the $offset of our $array

			$lft = $row['lft'];		// Get the left value (only needed for the diff)
			$rght = $row['rght'];	// Get the right value, needed for the diff, and to know if we are done with the current level

			$diff = $rght - $lft;		// The difference between left and right
			$children = ($diff-1)/2;	// The number of children of this item

			$items[$num]['id'] = $row['id'];		// Set the id
			$items[$num]['alias'] = $row['alias'];	// Set the alias

			$items[$num]['attributes'] = array('class'=>'block'); //Style and Class for JS
			$items[$num]['children'] = "";

			if ($children>0) // If there are children
			{
				// Add them to children element. Children begin at $offset + 1 and have a $rightMax of our $rght -1
				$items[$num]['children'] = $this->_get_nested_array($array,	 $obj, $offset+1, $rght-1);
			}
			$offset = $offset + $children + 1;	// Go to the next item on the same level
			$num++; // Increase the counter for $items
		} while($rght < $rightMax);

		return $items;
	}

	function _aclCreateAro($aclAroId = 0, $aclAroParent = 1, $aclAroAlias)
	{
		$Aro = new Aro();
		if($Aro->create($aclAroId, $aclAroParent, $aclAroAlias))
		{
			if($aclAroParent != 1)
			{
				if(!$this->_aclSetAroParent($aclAroParent, $aclAroAlias))
				{
					return false;
				}
			}
			return true;
		}
		else
		{
			return false;
		}
	}

	function _aclSetAroParent($aclAroParent, $aclAroAlias)
	{
		$Aro = new Aro();
		if($Aro->setParent($aclAroParent, $aclAroAlias))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}
?>