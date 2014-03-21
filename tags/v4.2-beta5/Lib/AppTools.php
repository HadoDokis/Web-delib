<?php

/**
 * Created on 5 avr. 2011
 *
 * Librairie regroupant des fonctions hors MVC
 */
class AppTools {

    /**
     * Ajout ou soustrait un délai (xs:duration) à une date
     * @param string $date date
     * @param string $duration délai sous la forme xs:duration
     * @param string $format format de sortie de la date
     * @param string $operateur 'add' pour l'ajout et 'sub' pour la soustraction du délai
     * @return string résultat formaté ou null en cas d'erreur
     */
    public static function addSubDurationToDate($date, $duration, $format = 'Y-m-d', $operateur = 'add') {
        // initialisation
        $ret = null;
        try {
            $thisDate = new DateTime($date);
            $thisDuration = new DateInterval($duration);
            if ($operateur == 'add')
                $thisDate->add($thisDuration);
            elseif ($operateur == 'sub')
                $thisDate->sub($thisDuration);
            $ret = $thisDate->format($format);
        } catch (Exception $e) {
            debug('Fonction webdelib::addSubDurationToDate : ' . $e->getMessage());
        }
        return $ret;
    }

    /**
     * formate une date issue de la base de donnée
     * @param string $dateBD date issue de la lecture d'un enregistrement en base de données
     * @param string $format format de sortie utilisée par la fonction date()
     * @return string date mise en forme
     */
    public static function timeFormat($dateBD, $format = 'Y-m-d') {
        if (empty($dateBD)) return '';
        $dateTime = strtotime($dateBD);
        return date($format, $dateTime);
    }

    /**
     * formate une xs:duration sous forme litérale
     * @param string $duration délai sous la forme xs:duration
     * @return string délai mise en forme litérale
     */
    public static function durationToString($duration) {
        // initialisation
        $format = array();
        if (empty($duration)) return '';
        $thisDuration = new DateInterval($duration);
        $annees = $thisDuration->y;
        $mois = $thisDuration->m;
        $jours = $thisDuration->d;
        $heures = $thisDuration->h;
        $minutes = $thisDuration->i;
        $secondes = $thisDuration->s;
        if (!empty($annees))
            $format[] = $annees > 1 ? '%y ans' : '%y an';
        if (!empty($mois))
            $format[] = '%m mois';
        if (!empty($jours))
            $format[] = $jours > 1 ? '%d jours' : '%d jour';
        if (!empty($heures))
            $format[] = $heures > 1 ? '%h heures' : '%h heure';
        if (!empty($minutes))
            $format[] = $minutes > 1 ? '%i minutes' : '%i minute';
        if (!empty($secondes))
            $format[] = $secondes > 1 ? '%s secondes' : '%s seconde';

        if (empty($format))
            return '';
        else
            return $thisDuration->format(implode(', ', $format));
    }

    /**
     * transforme une xs:duration sous forme de tableau
     * @param string $duration délai sous la forme xs:duration
     * @return array délai sous forme de tableau array('year', 'month', 'day', 'hour', 'minute', 'seconde')
     */
    public static function durationToArray($duration) {
        // initialisation
        $ret = array('year' => 0, 'month' => 0, 'day' => 0, 'hour' => 0, 'minute' => 0, 'seconde' => 0);

        if (!empty($duration)) {
            $thisDuration = new DateInterval($duration);
            $ret['year'] = $thisDuration->y;
            $ret['month'] = $thisDuration->m;
            $ret['day'] = $thisDuration->d;
            $ret['hour'] = $thisDuration->h;
            $ret['minute'] = $thisDuration->i;
            $ret['seconde'] = $thisDuration->s;
        }

        return $ret;
    }

    /**
     * transforme une tableau (array('year', 'month', ...)) en xs:duration ('D1Y...')
     * @param array $duration délai sous forme de tableau array('year', 'month', 'day', 'hour', 'minute', 'seconde')
     * @return string délai sous la forme xs:duration
     */
    public static function arrayToDuration($duration) {
        // initialisation
        $ret = $periode = $temps = '';
        $defaut = array('year' => 0, 'month' => 0, 'day' => 0, 'hour' => 0, 'minute' => 0, 'seconde' => 0);

        if (empty($duration) || !is_array($duration))
            return '';

        $duration = array_merge($defaut, $duration);
        if (!empty($duration['year'])) $periode .= $duration['year'] . 'Y';
        if (!empty($duration['month'])) $periode .= $duration['month'] . 'M';
        if (!empty($duration['day'])) $periode .= $duration['day'] . 'D';
        if (!empty($duration['hour'])) $temps .= $duration['hour'] . 'H';
        if (!empty($duration['minute'])) $temps .= $duration['minute'] . 'M';
        if (!empty($duration['seconde'])) $temps .= $duration['seconde'] . 'S';

        if (!empty($periode) || !empty($temps)) {
            $ret = 'P' . $periode;
            if (!empty($temps)) $ret .= 'T' . $temps;
        }

        return $ret;
    }

    public static function url_exists($url) {
        if (!$fp = curl_init($url)) return false;
        return true;
    }

    /**
     * Retourne un répertoire temporaire disponible dans le dossier passé en parametre
     * @param string $patchDir
     * @return bool|string
     */
    public static function newTmpDir($patchDir) {
        App::uses('Folder', 'Utility');
        $folder = new Folder($patchDir, true, 0777);
        //Création du répertoire temporaire par la fonction tempnam
        $outputDir = tempnam($folder->path, '');
        unlink($outputDir);
        $folder = new Folder($outputDir, true, 0777);
        return $folder->path;
    }

    public static function getNameFile($file) {
        $info = pathinfo($file);
        return basename($file, '.' . $info['extension']);
    }
    
    /**
     * Retourne le type mime d'un flux passé en parametre
     * @param string $patchDir
     * @return bool|string
     */
    public static function FileMime($data) {

        App::uses('File', 'Utility');
        App::uses('Fido', 'ModelOdtValidator.Lib/Fido');
        
        $file = new File($data, false);
        if(!$file->exists()){
            $fileFlux = new File(AppTools::newTmpDir(TMP.'file/test').'/test_', true, 0777);
            $fileFlux->write($data);
            $allowed = Fido::analyzeFile($fileFlux->path);
            $fileFlux->delete();
        }else{
            $allowed = Fido::analyzeFile($file->path);
            $file->close();
        }
        
        return $allowed;
    }
}

