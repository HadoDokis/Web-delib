<?php
	/**
	* CREATE INDEX referents_nom_complet_idx ON referents ( ( nom || ' ' || prenom ) );
	* CREATE INDEX personnes_nom_complet_idx ON personnes ( ( nom || ' ' || prenom ) );
	*/

	function __translate( $value ) {
		switch( gettype( $value ) ) {
			case 'NULL':
				$value = 'NULL';
				break;
			case 'boolean':
				$value = ( $value ? 'true' : 'false' );
				break;
			case 'integer':
			case 'double':
				$value = $value;
				break;
			case 'string':
				$value = "'$value'";
				break;
			default:
				trigger_error( gettype( $value ), E_USER_WARNING );
		}
		return $value;
	}

	/**
	*
	*/

	function dump( $datas = false, $showHtml = false, $showFrom = true ) {
		if (Configure::read() > 0) {
			if ($showFrom) {
				$calledFrom = debug_backtrace();
				echo '<strong>' . substr(str_replace(ROOT, '', $calledFrom[0]['file']), 1) . '</strong>';
				echo ' (line <strong>' . $calledFrom[0]['line'] . '</strong>)';
			}
			echo "\n<pre class=\"cake-debug\">\n";

			if( is_array( $datas ) ) {
				$datas = Set::flatten( $datas, '_____' );

				foreach( $datas as $key => $value ) {
					$datas[$key] = __translate( $value );
				}

				$datas = Xset::bump( $datas, '_____' );
			}
			else {
				$datas = __translate( $datas );
			}

			$datas = print_r($datas, true);

			if ($showHtml) {
				$datas = str_replace('<', '&lt;', str_replace('>', '&gt;', $datas));
			}
			echo $datas . "\n</pre>\n";
		}
	}

	/**
		* ...
		*
		* @param array $array
		* @param array $filterKeys
		* @return array $newArray
	*/

	function array_filter_keys( array $array, array $filterKeys, $remove = false ) { // FIXME ?
		$newArray = array();
		foreach( $array as $key => $value) {
			if( $remove && !in_array( $key, $filterKeys ) ) {
				$newArray[$key] = $value;
			}
			else if( !$remove && in_array( $key, $filterKeys ) ) {
				$newArray[$key] = $value;
			}
		}
		return $newArray;
	}

	/**
	* FIXME: remplacer la wootFonction dans AppModel
	* @param array $array
	* @param array $replacements
	* @return array
	*/

	function recursive_key_value_preg_replace( array $array, array $replacements ) {
		$newArray = array();
		foreach( $array as $key => $value ) {
			foreach( $replacements as $pattern => $replacement ) {
				$key = preg_replace( $pattern, $replacement, $key );
			}

			if( is_array( $value ) ) {
				$value = recursive_key_value_preg_replace( $value, $replacements );
			}
			else {
				foreach( $replacements as $pattern => $replacement ) {
					$value = preg_replace( $pattern, $replacement, $value );
				}
			}
			$newArray[$key] = $value;
		}
		return $newArray;
	}

	/**
	* INFO: http://fr.php.net/manual/fr/function.strpos.php#49739
	*/

	function strallpos( $pajar, $aguja, $offset = 0, &$count = null ) {
		if( $offset > strlen( $pajar ) ) {
			trigger_error("strallpos(): Offset not contained in string.", E_USER_WARNING);
		}
		$match = array();
		for( $count = 0; ( ( $pos = strpos( $pajar, $aguja, $offset ) ) !== false ); $count++ ) {
			$match[] = $pos;
			$offset = $pos + strlen( $aguja );
		}
		return $match;
	}

	/**
	* Extracts model name and field name from a path.
	* @param string $path ie. User.username, User.0.id
	* @return array( string $model, string $field ) ie. array( 'User', 'username' ), array( 'User', 'id' )
	*/

	function model_field( $path ) {
		if( preg_match( "/(?<!\w)(\w+)(\.|\.[0-9]+\.)(\w+)$/", $path, $matches ) ) {
			return array( $matches[1], $matches[3] );
		}

		trigger_error( "Could not extract model and field names from the following path: \"{$path}\"", E_USER_WARNING );
		return null;
	}

	/**
	* Returns a human-readable amount from a number of bytes
	* SOURCE: http://www.phpfront.com/php/Convert-Bytes-to-corresponding-size/
	*/

	function byteSize( $bytes ) {
		$size = $bytes / 1024;
		if($size < 1024) {
			$size = number_format($size, 2);
			$size .= ' KB';
		}
		else {
			if($size / 1024 < 1024) {
				$size = number_format($size / 1024, 2);
				$size .= ' MB';
			}
			else if ($size / 1024 / 1024 < 1024) {
				$size = number_format($size / 1024 / 1024, 2);
				$size .= ' GB';
			}
		}
		return $size;
	}

	function valid_int( $value ) {
		return !( !is_numeric( $value ) || !( (int)$value == $value ) );
	}

	function date_short( $sqldate ) {
		if( !empty( $sqldate ) ) {
			return strftime( '%d/%m/%Y', strtotime( $sqldate ) );
		}
	}

	function required( $label ) {
		return h( $label ).' '.REQUIRED_MARK;
	}

	function cake_version() {
		$versionData = explode( "\n", file_get_contents( ROOT.DS.'cake'.DS.'VERSION.txt' ) );
		$version = explode( '.', $versionData[count( $versionData) - 1] );
		return implode( '.', $version );
	}

	/**
	* Get real timeout (in seconds) based on core.php configuration
	*/

	function readTimeout() {
		if( Configure::read( 'Session.save' ) == 'cake' ) {
			App::import( 'Component', 'Session' );
			$session = new SessionComponent();
			return ( $session->sessionTime - $session->time );
		}
		else {
			return ini_get( 'session.gc_maxlifetime' );
		}
	}

	/**
	* Formatteun timestamp en H:M:S
	*
	* @see http://snipplr.com/view.php?codeview&id=4688
	*/

	function sec2hms( $sec, $padHours = false ) {
		$hms = "";

		// there are 3600 seconds in an hour, so if we
		// divide total seconds by 3600 and throw away
		// the remainder, we've got the number of hours
		$hours = intval(intval($sec) / 3600);

		// add to $hms, with a leading 0 if asked for
		$hms .= ($padHours)
				? str_pad($hours, 2, "0", STR_PAD_LEFT). ':'
				: $hours. ':';

		// dividing the total seconds by 60 will give us
		// the number of minutes, but we're interested in
		// minutes past the hour: to get that, we need to
		// divide by 60 again and keep the remainder
		$minutes = intval(($sec / 60) % 60);

		// then add to $hms (with a leading 0 if needed)
		$hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ':';

		// seconds are simple - just divide the total
		// seconds by 60 and keep the remainder
		$seconds = intval($sec % 60);

		// add to $hms, again with a leading 0 if needed
		$hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);

		return $hms;
	}

	/**
	*
	*/

	function suffix( $value, $separator = '_' ) { // FIXME: preg_escape separator + erreur si plus d'un caractère
		return preg_replace( '/^(.*'.$separator.')([^'.$separator.']+)$/', '\2', $value );
	}

	function array_depth( $array ) {
		$max_depth = 1;
		foreach ($array as $value) {
			if (is_array($value)) {
				$depth = array_depth($value) + 1;

				if ($depth > $max_depth) {
					$max_depth = $depth;
				}
			}
		}
		return $max_depth;
	}

	function dateComplete( $data, $key ) {
		$dateComplete = Set::extract( $data, $key );
		if( !is_array( $dateComplete ) || empty( $dateComplete ) ) {
			return !empty( $dateComplete );
		}
		else {
			$empty = true;
			foreach( $dateComplete as $tmp ) {
				if( !empty( $tmp ) ) {
					$empty = false;
				}
			}
			return !$empty;
		}
	}


	/**
		* ...
		*
		* @param array $array
		* @param array $filterKeys
		* @return array $newArray
	*/

	/*function array_filter_keys( array $array, array $filterKeys, $remove = false ) { // FIXME ?
		$newArray = array();
		foreach( $array as $key => $value) {
			if( $remove && !in_array( $key, $filterKeys ) ) {
				$newArray[$key] = $value;
			}
			else if( !$remove && in_array( $key, $filterKeys ) ) {
				$newArray[$key] = $value;
			}
		}
		return $newArray;
	}*/


	/**
		@input  multisized array (eg. array( 'Foo' => array( 'Bar' => 'value' ) ) )
		@output unisized array (eg. array( 'Foo__Bar' => 'value' ) )
	*/
	function array_unisize( $array, $prefix = null ) {
		$newArray = array();
		if( is_array( $array ) && !empty( $array ) ) {
			foreach( $array as $key => $value ) {
				$newKey = ( !empty( $prefix ) ? $prefix.'__'.$key : $key );
				if( is_array( $value ) ) {
					$newArray = Set::merge( $newArray, array_unisize( $value, $newKey ) );
				}
				else {
					$newArray[$newKey] = $value;
				}
			}
		}
		return $newArray;
	}

	/**
		@output multisized array (eg. array( 'Foo' => array( 'Bar' => 'value' ) ) )
		@input  unisized array (eg. array( 'Foo__Bar' => 'value' ) )
	*/
	function array_multisize( array $array, $prefix = null ) {
		$newArray = array();
		if( is_array( $array ) && !empty( $array ) ) {
			foreach( $array as $key => $value ) {
				$newArray = Set::insert( $newArray, implode( '.', explode( '__', $key ) ), $value );
			}
		}
		return $newArray;
	}

	function implode_assoc( $outer_glue, $inner_glue, $array, $allowempty = true ) {
		$ret = array();
		foreach( $array as $key => $value ) {
			if( !empty( $value ) || $allowempty ) {
				if( is_array( $value ) ) {
					$ret[] = $key.'[]'.$inner_glue.implode( $outer_glue.$key.'[]'.$inner_glue, $value );
				}
				else {
					$ret[] = $key.$inner_glue.$value;
				}
			}
		}
		return implode( $outer_glue, $ret );
	}

	/**
	* SOURCE: http://fr2.php.net/manual/fr/function.array-sum.php#58441
	*/

	function array_avg( $array ) {
		$avg = 0;
		if( !is_array( $array ) || count( $array ) == 0 ) {
			return false;
		}

		return ( array_sum( $array ) / count( $array ) );
	}

	function array_range( $low, $high, $step = 1 ) {
		$return = array();
		foreach( range( $low, $high, $step ) as $value ) {
			$return[$value] = $value;
		}
		return $return;
	}

	function array_intersects( array $needle, array $haystack ) {
		$return = array();
		foreach( $needle as $n ) {
			if( in_array( $n, $haystack ) ) {
				$return[] = $n;
			}
		}
		return $return;
	}
	/**
		* ...
		*
		* @param array $array
		* @param array $filterValues
		* @return array $newArray
	*/

	function array_filter_values( array $array, array $filterValues, $remove = false ) { // FIXME ?
		$newArray = array();
		foreach( $array as $key => $value) {
			if( $remove && !in_array( $value, $filterValues ) ) {
				$newArray[$key] = $value;
			}
			else if( !$remove && in_array( $value, $filterValues ) ) {
				$newArray[$key] = $value;
			}
		}
		return $newArray;
	}

	/**
	* Classes manipulation
	*/

	/// SOURCE: http://www.php.net/manual/en/function.get-class-methods.php#51795
	function get_overriden_methods($class) {
		$rClass = new ReflectionClass($class);
		$array = NULL;

		foreach ($rClass->getMethods() as $rMethod)
		{
			try
			{
				// attempt to find method in parent class
				new ReflectionMethod($rClass->getParentClass()->getName(),
									$rMethod->getName());
				// check whether method is explicitly defined in this class
				if ($rMethod->getDeclaringClass()->getName()
					== $rClass->getName())
				{
					// if so, then it is overriden, so add to array
					$array[] .=  $rMethod->getName();
				}
			}
			catch (exception $e)
			{    /* was not in parent class! */    }
		}

		return $array;
	}

	/// SOURCE: http://www.php.net/manual/en/function.get-class-methods.php#43379
	function get_this_class_methods($class){
		$array1 = get_class_methods($class);
		if($parent_class = get_parent_class($class)){
			$array2 = get_class_methods($parent_class);
			$array3 = array_diff($array1, $array2);
		}else{
			$array3 = $array1;
		}
		return($array3);
	}

	// -------------------------------------------------------------------------

	function is_behavior_name( $string ) {
		return ( strpos( $string, '_behavior' ) !== false );
	}

	function is_model_name( $string ) {
		return ( !is_behavior_name( $string ) && ( $string != 'view' ) );
	}

	function class_registry_models() {
      
		$keys = ClassRegistry::keys();
                debug(array_filter( $keys, 'is_model_name' ));
		return array_filter( $keys, 'is_model_name' );
	}

	function class_registry_models_count() {
		return count( class_registry_models() );
	}

	// -------------------------------------------------------------------------

	/** ********************************************************************
	*	Vérifie si au moins une des valeurs des clés existe en tant que clé
	*	dans le second paramètre
	** ********************************************************************/

	function array_any_key_exists( $keys, $search ) {
		if( !is_array( $keys ) ) {
			$keys = array( $keys );
		}
		foreach( $keys as $key ) {
			if( array_key_exists( $key, $search ) ) {
				return true;
			}
		}
		return false;
	}

	/** ********************************************************************
	*
	** ********************************************************************/

	function nullify_empty_values( array $array ) {
		$newArray = array();
		foreach( $array as $key => $value ) {
			if( ( is_string( $value ) && strlen( trim( $value ) ) == 0 ) || ( !is_string( $value ) && empty( $value ) ) ) {
				$newArray[$key] = null;
			}
			else {
				$newArray[$key] = $value;
			}
		}
		return $newArray;
	}

	/** ************************************************************************
	* Remplace les caractères accentués par des caractères non accentués
	* dans une chaîne de caractères
	* @param string $string chaîne de caractères à modifier
	* @return string chaîne de caractères sans accents
	* TODO: http://www.nabble.com/Accent-insensitive-search--td22583222.html
	*     (liste des accents)
	* ATTENTION: il faut utiliser les fonctions mb_internal_encoding et mb_regex_encoding
	*     pour que le système sache quels encodages il traite, afin que le remplacement
	*     d'acents se passe bien.
	*** ***********************************************************************/

	function replace_accents( $string ) {
		$accents = array(
			'[ÂÀ]',
			'[âà]',
			'[Ç]',
			'[ç]',
			'[ÉÊÈË]',
			'[éêèë]',
			'[ÎÏ]',
			'[îï]',
			'[ÔÖ]',
			'[ôö]',
			'[ÛÙ]',
			'[ûù]'
		);

		$replace = array(
			'A',
			'a',
			'C',
			'c',
			'E',
			'e',
			'I',
			'i',
			'O',
			'o',
			'U',
			'u'
		);

		foreach( $accents as $key => $accent ) {
			$string = mb_ereg_replace( $accent, $replace[$key], $string );
		}

		return $string;
	}

	/**
	* Retourne true pour un RIB bien formé, false pour un RIB mal formé
	* INFO: http://fr.wikipedia.org/wiki/Cl%C3%A9_RIB#Algorithme_de_calcul_qui_fonctionne_avec_des_entiers_32_bits
	*/

	function validRib( $etaban, $guiban, $numcomptban, $clerib ) {
		$replacements = array(
			1 => array( 'A', 'J' ),
			2 => array( 'B', 'K', 'S' ),
			3 => array( 'C', 'L', 'T' ),
			4 => array( 'D', 'M', 'U' ),
			5 => array( 'E', 'N', 'V' ),
			6 => array( 'F', 'O', 'W' ),
			7 => array( 'G', 'P', 'X' ),
			8 => array( 'H', 'Q', 'Y' ),
			9 => array( 'I', 'R', 'Z' )
		);

		foreach( $replacements as $number =>  $letters ) {
			foreach( $letters as $letter ) {
				$numcomptban = str_replace( $letter, $number, $numcomptban );
			}
		}

		$numcomptban1 = substr( $numcomptban, 0, 6 );
		$numcomptban2 = substr( $numcomptban, 6, 5 );

		return ( (int) $clerib == ( 97 - ( (89 * $etaban + 15 * $guiban + 76 * $numcomptban1 + 3 * $numcomptban2 ) % 97 ) ) );
	}

	/**
	* Calcul l'âge en années à partir de la date de naissance.
	* @param string $date date de naissance au format yyyy-mm-dd
	* @return integer âge en années
	*/

	function age( $date ) {
		list( $year, $month, $day ) = explode( '-', $date );
		$today = time();
		return date( 'Y', $today ) - $year + ( ( ( $month > date( 'm', $today ) ) || ( $month == date( 'm', $today ) && $day > date( 'd', $today ) ) ) ? -1 : 0 );
	}

	/**
	* Calcule la clé pour un NIR donné
	*
	* @param string $nir NIR sur 13 caractères
	* @return string Clé du NIR, sur 2 caractères
	*/

	function cle_nir( $nir ) {
		$nir = strtoupper( $nir );

		if( !preg_match( '/^([0-9]+|[0-9]{5}2(A|B)[0-9]{6})$/', $nir ) ) {
			trigger_error( sprintf( __( 'Le NIR suivant n\'est pas composé que de chiffres: %s', true ), $nir ), E_USER_WARNING );
		}

		if( strlen( $nir ) != 13 ) {
			trigger_error( sprintf( __( 'Le NIR suivant n\'est pas composé de 13 caractères: %s', true ), $nir ), E_USER_WARNING );
		}

		$correction = 0;

		if( preg_match( '/^([0-9]{5}2)(A|B)([0-9]{6})/', $nir, $matches ) ) {
			if( $matches[2] == 'A' ) {
				$correction = 1000000;
			}
			else {
				$correction = 2000000;
			}
			$nir = preg_replace( '/(A|B)/', '0', $nir );
		}

		$modulo = bcmod( bcsub( $nir, $correction ), 97 );
		return str_pad( ( 97 - $modulo ), 2, '0', STR_PAD_LEFT);
	}

	/**
	* Vérifie la validité d'un NIR sur 15 caractères
	*
	* @param string $nir NIR sur 15 caractères
	* @return boolean
	*/

	function valid_nir( $nir ) {
		return preg_match(
			'/^(1|2|7|8)[0-9]{2}(0[1-9]|10|11|12|[2-9][0-9])((0[1-9]|[1-8][0-9]|9[0-5]|2A|2B)(00[1-9]|0[1-9][0-9]|[1-8][0-9][0-9]|9[0-8][0-9]|990)|(9[7-8][0-9])(0[1-9]|0[1-9]|[1-8][0-9]|90)|99(00[1-9]|0[1-9][0-9]|[1-8][0-9][0-9]|9[0-8][0-9]|990))(00[1-9]|0[1-9][0-9]|[1-9][0-9][0-9]|)(0[1-9]|[1-8][0-9]|9[0-7])$/i',
			$nir
		);
		/*if( !preg_match( '/^[0-9]+$/', $nir ) ) {
			trigger_error( sprintf( __( 'Le NIR suivant n\'est pas composé que de chiffres: %s', true ), $nir ), E_USER_WARNING );
		}

		if( strlen( $nir ) != 13 ) {
			trigger_error( sprintf( __( 'Le NIR suivant n\'est pas composé de 13 caractères: %s', true ), $nir ), E_USER_WARNING );
		}

		$modulo = bcmod( $nir, 97 );
		return str_pad( ( 97 - $modulo ), 2, '0', STR_PAD_LEFT);*/
	}

	/**
	* Exemple:
	*	$subject = array( 'Foo.id' => array( 'Bar' => 1 ), 'Foobar' => array( 'Foo.bar = Bar.foo' ) );
	*	$replacement = array( 'Foo' => 'Baz' );
	*	Résultat: array( 'Baz.id' => array( 'Bar' => 1 ), 'Foobar' => array( 'Baz.bar = Bar.foo' ) );
	*
	* @param array $subject
	* @param array $replacement
	* @return array
	*/
	function array_words_replace( array $subject, array $replacement ) {
		$regexes = array();
		foreach( $replacement as $key => $value ) {
			$key = "/(?<!\.)(?<!\w)({$key})(?!\w)/";
			$regexes[$key] = $value;
		}
		return recursive_key_value_preg_replace( $subject, $regexes );
	}

	/**
	* Retourne une chaine de caractère pour remplir un tableau associatif javascript
	* à partir d'un tableau associatif php
	*
	* @param string $array Tableau associatif
	* @return string
	*/

	function php_associative_array_to_js( $array ) {
		$return = array();

		foreach( $array as $key => $value ) {
			$return[] = "\"{$key}\" : \"{$value}\"";
		}

		return  "{ ".implode( ', ', $return )." }";
	}
?>
