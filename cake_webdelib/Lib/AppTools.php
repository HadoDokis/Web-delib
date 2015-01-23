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
        if (empty($dateBD))
            return '';
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
        if (empty($duration))
            return '';
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
        if (!empty($duration['year']))
            $periode .= $duration['year'] . 'Y';
        if (!empty($duration['month']))
            $periode .= $duration['month'] . 'M';
        if (!empty($duration['day']))
            $periode .= $duration['day'] . 'D';
        if (!empty($duration['hour']))
            $temps .= $duration['hour'] . 'H';
        if (!empty($duration['minute']))
            $temps .= $duration['minute'] . 'M';
        if (!empty($duration['seconde']))
            $temps .= $duration['seconde'] . 'S';

        if (!empty($periode) || !empty($temps)) {
            $ret = 'P' . $periode;
            if (!empty($temps))
                $ret .= 'T' . $temps;
        }

        return $ret;
    }

    public static function url_exists($url) {
        if (!$fp = curl_init($url))
            return false;
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
     * @param string $data
     * @return bool|string
     */
    public static function FileMime($data) {

        App::uses('File', 'Utility');
        App::uses('Fido', 'ModelOdtValidator.Lib');

        $file = new File($data, false);
        if (!$file->exists()) {
            $fileFlux = new File(AppTools::newTmpDir(TMP . 'files/test') . '/test_', true, 0777);
            $fileFlux->write($data);
            $allowed = Fido::analyzeFile($fileFlux->path);
            $fileFlux->delete();
        } else {
            $allowed = Fido::analyzeFile($file->path);
            $file->close();
        }

        return $allowed;
    }

    /**
     * @param int $bytes
     * @param int $decimals
     * @return string
     */
    public static function human_filesize($bytes, $decimals = 2) {
        $sz = array('B', 'Ko', 'Mo', 'Go');
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }

    public static function xml_entity_encode($_string) {
        //UTILISER  htmlentities() à partir de php 5.4.0
        // Set up XML translation table
        $_xml = array();
        $_xl8_iso = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
        //Compatibilité php <5.3.3
        foreach ($_xl8_iso as $key => $value)
            $_xl8[utf8_encode($key)] = utf8_encode($value);

        while (list($_key, $_val) = each($_xl8)) {
            $_xml[$_key] = '&#' . AppTools::uniord($_key) . ';';
        }

        return strtr($_string, $_xml);
    }

    public static function uniord($u) {
        $k = mb_convert_encoding($u, 'UCS-2LE', 'UTF-8');
        $k1 = ord(substr($k, 0, 1));
        $k2 = ord(substr($k, 1, 1));

        return $k2 * 256 + $k1;
    }

    /**
     * Retourne la date en francais et en toute lettre du timestamp passé,
     * seule les dates a quatre chifres sont gérés.
     * 
     * @param type $timestamp de la date voulue
     * @return string date en toute lettre
     */
    public static function dateLettres($timestamp) {
        $days = array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');
        $months = array('', 'janvier', 'février', 'mars', 'avril', 'mai', 'juin',
            'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre');
        $jour = $days[date('w', $timestamp)];
        $nbJour = date('d', $timestamp);
        // jour en toute lettre
        $nbJour = self::_dizaines($nbJour);
        $mois = $months[date('n', $timestamp)];
        // année en toute lettre
        $nbAnnee = date('Y', $timestamp);
        if (substr($nbAnnee, 0, 1) == 0) {
            $annee = '';
        } else if (substr($nbAnnee, 0, 1) == 1) {
            $annee = 'mille' . ' ';
        } else {
            $annee = self::_unite(substr($nbAnnee, 0, 1)) . ' ' . 'mille' . ' ';
        }
        if (substr($nbAnnee, 1, 1) == 0) {
            $annee .= '';
        } else if (substr($nbAnnee, 1, 1) == 1) {
            $annee .= 'cent' . ' ';
        } else {
            $annee .= self::_unite(substr($nbAnnee, 1, 1)) . ' ' . 'cent' . ' ';
        }
        $annee .= self::_dizaines(substr($nbAnnee, 2));
        return("L'an $annee, le $nbJour $mois ");
    }

    /**
     * change des chiffres précis en lettre
     * 
     * @param string $data le chiffre à transformer
     * @return string|boolean soit le chiffre voulu en lettre soit false
     */
    private static function _latin_irregulier($data) {
        switch ($data) {
            case 11:
                return "onze";
            case 12:
                return "douze";
            case 13:
                return "treize";
            case 14:
                return "quatorze";
            case 15:
                return "quinze";
            case 16:
                return "seize";
        }
        return false;
    }

    /**
     * Renvoie les unitées en lettre 
     * 
     * @param string $data le chiffre à transformer
     * @return string|boolean soit le chiffre voulu en lettre soit false
     */
    private static function _unite($data) {
        switch ($data) {
            case 0:
                return '';
            case 1:
                return "un";
            case 2:
                return "deux";
            case 3:
                return "trois";
            case 4:
                return "quatre";
            case 5:
                return "cinq";
            case 6:
                return "six";
            case 7:
                return "sept";
            case 8:
                return "huit";
            case 9:
                return "neuf";
        }
        return false;
    }

    /**
     * renvoie les dixaines en toute lettres
     * 
     * @param type $data le chiffre à transformer
     * @return string|boolean soit le chiffre voulu en lettre soit false
     */
    private static function _dizaines($data) {
        $ret = self::_latin_irregulier($data);
        if (!$ret) {
            $et = '';
            if (substr($data, 1) == 1) {
                $et = 'et ';
            }
            switch (substr($data, 0, 1)) {
                case 0:
                    return '' . self::_unite(substr($data, 1));
                case 1:
                    return "dix" . ' ' . self::_unite(substr($data, 1));
                case 2:
                    return "vingt" . ' ' . $et . self::_unite(substr($data, 1));
                case 3:
                    return "trente" . ' ' . $et . self::_unite(substr($data, 1));
                case 4:
                    return "quarante" . ' ' . $et . self::_unite(substr($data, 1));
                case 5:
                    return "cinquante" . ' ' . $et . self::_unite(substr($data, 1));
                case 6:
                    return "soixante" . ' ' . $et . self::_unite(substr($data, 1));
                case 7:
                    return "soixante" . ' ' . $et . self::_dizaines('1' . substr($data, 1));
                case 8:
                    return "quatre vingt" . ' ' . self::_unite(substr($data, 1));
                case 9:
                    return "quatre vingt" . ' ' . self::_dizaines('1' . substr($data, 1));
            }
        } else {
            return $ret;
        }
        return false;
    }

}
