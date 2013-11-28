<?php

/*
 * Création : 13 févr. 2006
 * Christophe Espiau
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

class Html2Helper extends HtmlHelper {

    var $helpers = array('Form');

    /*
     * Retourne deux élements SELECT pour la saisie des heures et des minutes.
     *
     * Cette fonction est une surcharge de la fonction dateTimeOptionTag.
     * Elle est faite :
     * -  pour afficher seulement les heures et les minutes
     * -  pour que les deux champs SELECT n'affichent rien s'il n'y a pas encore eu de pointage enregistré
     * (alors que la fonction dateTimeOptionTag sélectionne l'heure courante par défaut).
     *
     * @param string $tagName Prefix name for the SELECT element
     * @param string $selected Option which is selected. Must be a timestamp. See also getTimestamp().
     * @param array $optionAttr Attribute array for the option elements
     *
     * @return string The HTML formatted SELECT elements
     *
     */

    function timeOptionTag($tagName, $selected = null, $optionAttr = null) {
        $output = $this->dateTimeOptionTag($tagName, 'NONE', '24', $selected, $optionAttr);

        if ($selected == null) {
            return $this->selectBlankOption($output);
        }
        return $output;
    }

    /*
     *
     */

    function dateOptionTag($tagName, $selected = null, $optionAttr = null) {
        $output = $this->dateTimeOptionTagFr($tagName, 'DMY', 'NONE', $selected, $optionAttr);
        if ($selected == null) {
            return $this->selectBlankOption($output);
        }
        return $output;
    }

    /*
     *
     */

    function selectBlankOption($output) {
        $output = str_replace(" selected=\"selected\"", "", $output);
        $output = str_replace(" value=\"\"", " value=\"\" selected=\"selected\"", $output);
        return $output;
    }

    /*
     * Retourne un timestamp Unix à partir d'une date et d'une heure.
     *
     * Un pointage est enregistré en base avec la date dans un champ,
     * l'heure de début dans un autre et l'heure de fin dans un troisième.
     *
     * Dans l'écran de pointage, pour pouvoir afficher les heure déjà enregistrées,
     * il faut passer à la fonction timeOptionTag() (voir ci-dessus) le paramètre $selected
     * sous forme de timestamp, qu'il faut créer à partir du champ date et du champ heureDebut
     * (ou bien date et heureFin).
     *
     * @param string $date Une date au format aaaa-mm-jj
     * @param string $time Une heure au format hh:mm:ss
     *
     * @return  int A Unix timestamp, or null if $time is null.
     */

    function getTimestamp($date, $time) {
        // Test pour l'heure de fin.
        // Si celle-ci n'est pas encore enregistrée, elle est à NULL dans la base.
        // Et si $time = null, la fonction doit retourner null.
        if ($time == null)
            return null;

        $amj = explode("-", $date);
        $hms = explode(":", $time);

        return mktime($hms[0], $hms[1], $hms[2], $amj[1], $amj[2], $amj[0]);
    }

    function yearOptionTagFr($tagName, $value = null, $minYear = null, $maxYear = null, $selected = null, $selectAttr = null, $optionAttr = null, $showEmpty = true) {
        $yearValue = empty($selected) ? null : $selected;
        $currentYear = date('Y');
        $maxYear = is_null($maxYear) ? $currentYear + 11 : $maxYear + 1;
        $minYear = is_null($minYear) ? $currentYear - 42 : $minYear;

        if ($minYear > $maxYear) {
            $tmpYear = $minYear;
            $minYear = $maxYear;
            $maxYear = $tmpYear;
        }
        $minYear = $currentYear < $minYear ? $currentYear : $minYear;
        $maxYear = $currentYear > $maxYear ? $currentYear : $maxYear;

        for ($yearCounter = $minYear; $yearCounter < $maxYear; $yearCounter++) {
            $years[$yearCounter] = $yearCounter;
        }
        $option = $this->selectTag($tagName . "_year", $years, $yearValue, $selectAttr, $optionAttr, $showEmpty);
        return $option;
    }

    function dateTimeOptionTagFr($tagName, $dateFormat = 'DMY', $timeFormat = '12', $selected = null, $selectAttr = null, $optionAttr = null, $showEmpty = false) {
        $day = null;
        $month = null;
        $year = null;
        $hour = null;
        $min = null;
        $meridian = null;

        if (!empty($selected)) {
            if (is_int($selected)) {
                $selected = strftime('%Y-%m-%d  %H:%M', $selected);
            }
            $date = explode('-', $selected);
            $days = explode(' ', $date[2]);

            $day = $days[0];
            $month = $date[1];
            $year = $date[0];

            if ($timeFormat != 'NONE' && !empty($timeFormat)) {
                $time = explode(':', $days[2]);
                if (($time[0] > 12) && $timeFormat == '12') {
                    $time[0] = $time[0] - 12;
                    $meridian = 'pm';
                } elseif ($time[0] > 12) {
                    $meridian = 'pm';
                }
                $hour = $time[0];
                $min = $time[1];
            }
        }

        switch ($dateFormat) {
            case 'DMY' :
                $opt = $this->dayOptionTag($tagName, null, $day, $selectAttr, $optionAttr, $showEmpty) . '-' . $this->monthOptionTagFr($tagName, null, $month, $selectAttr, $optionAttr, $showEmpty) . '-' . $this->yearOptionTagFr($tagName, null, null, null, $year, $selectAttr, $optionAttr, $showEmpty);
                break;
            case 'MDY' :
                $opt = $this->monthOptionTagFr($tagName, null, $month, $selectAttr, $optionAttr, $showEmpty) . '-' . $this->dayOptionTag($tagName, null, $day, $selectAttr, $optionAttr, $showEmpty) . '-' . $this->yearOptionTagFr($tagName, null, null, null, $year, $optionAttr, $selectAttr, $showEmpty);
                break;
            case 'YMD' :
                $opt = $this->yearOptionTagFr($tagName, null, null, null, $year, $selectAttr, $optionAttr, $showEmpty) . '-' . $this->monthOptionTagFr($tagName, null, $month, $selectAttr, $optionAttr, $showEmpty) . '-' . $this->dayOptionTag($tagName, null, $day, $optionAttr, $selectAttr, $showEmpty);
                break;
            case 'NONE':
                $opt = '';
                break;
            default:
                $opt = '';
                break;
        }
        switch ($timeFormat) {
            case '24':
                $opt .= $this->hourOptionTag($tagName, null, true, $hour, $selectAttr, $optionAttr, $showEmpty) . ':' . $this->minuteOptionTag($tagName, null, $min, $selectAttr, $optionAttr, $showEmpty);
                break;
            case '12':
                $opt .= $this->hourOptionTag($tagName, null, false, $hour, $selectAttr, $optionAttr, $showEmpty) . ':' . $this->minuteOptionTag($tagName, null, $min, $selectAttr, $optionAttr, $showEmpty) . ' ' . $this->meridianOptionTag($tagName, null, $meridian, $selectAttr, $optionAttr, $showEmpty);
                break;
            case 'NONE':
                $opt .='';
                break;
            default :
                $opt .='';
                break;
        }
        return $opt;
    }

    /**
     * Returns a SELECT element for years
     *
     * @param string $tagName Prefix name for the SELECT element
     * @deprecated  string $value
     * @param integer $minYear First year in sequence
     * @param integer $maxYear Last year in sequence
     * @param string $selected Option which is selected.
     * @param array $optionAttr Attribute array for the option elements.
     * @param boolean $showEmpty Show/hide the empty select option
     * @return mixed
     * @access public
     */
    function yearOptionTag($tagName, $value = null, $minYear = null, $maxYear = null, $selected = null, $selectAttr = null, $optionAttr = null, $showEmpty = true) {
        if (empty($selected) && ($this->tagValue($tagName))) {
            $selected = date('Y', strtotime($this->tagValue($tagName)));
        }

        $yearValue = empty($selected) ? ($showEmpty ? NULL : date('Y')) : $selected;
        $currentYear = date('Y');
        $maxYear = is_null($maxYear) ? $currentYear + 11 : $maxYear + 1;
        $minYear = is_null($minYear) ? $currentYear - 60 : $minYear;

        if ($minYear > $maxYear) {
            $tmpYear = $minYear;
            $minYear = $maxYear;
            $maxYear = $tmpYear;
        }

        for ($yearCounter = $minYear; $yearCounter < $maxYear; $yearCounter++) {
            $years[$yearCounter] = $yearCounter;
        }

        return $this->selectTag($tagName . "_year", $years, $yearValue, $selectAttr, $optionAttr, $showEmpty);
    }

    function ukToFrenchDate($date) {
        return date("d-m-Y", strtotime($date));
    }

    function ukToFrenchDateWithHour($date) {
        return date("d/m/Y \à H\hi", strtotime($date));
    }

    /* affiche une flèche vers le bas et une flèche vers le bas pour le tri asc et desc de $urlChampTri */

    function boutonsTri($urlChampTri = null, $inverse = false) {
        echo $this->link(SHY, $urlChampTri . '/' . ($inverse ? 'DESC' : 'ASC'), array('class' => 'link_triasc', 'title' => 'Trier par ordre croissant'), false, false);
        echo $this->link(SHY, $urlChampTri . '/' . ($inverse ? 'ASC' : 'DESC'), array('class' => 'link_tridesc', 'title' => 'Trier par ordre décroissant'), false, false);
    }

    function boutonAdd($value = "Ajouter", $title = "Ajouter", $action = 'add', $class = 'btn-primary', $center = true, $id='bouton_ajouter') {
        if ($center)
            echo $this->tag('div', null, array('style' => 'text-align:center; margin-top:10px;'));

        echo $this->link("<i class='fa fa-plus-sign fa fa-large'></i> $value", array("action" => $action), array('id' => $id, 'class' => "btn $class", 'escape' => false, 'title' => $title));

        if ($center)
            echo $this->tag('/div', null);
    }

    function boutonRetour($action = "index", $style = '', $center = false, $value="Retour") {
        if ($center)
            echo $this->tag('div', null, array('style' => 'text-align:center;'));

        echo $this->link("<i class='fa fa-arrow-left'></i> $value", array('action'=>$action), array('class' => "btn", 'escape' => false, 'title' => 'Revenir à la page précédente', 'style' => $style));

        if ($center)
            echo $this->tag('/div', null);
    }

    function boutonRetourUrl($url = "javascript:history.go(-1)", $style = '', $center = false, $value="Retour") {
        if ($center)
            echo $this->tag('div', null, array('style' => 'text-align:center;'));

        echo $this->link("<i class='fa fa-arrow-left'></i> $value", $url, array('class' => "btn", 'escape' => false, 'title' => 'Revenir à la page précédente', 'style' => $style));

        if ($center)
            echo $this->tag('/div', null);
    }

    function boutonModifier($action = 'edit', $value = 'Modifier', $title = 'Modifier', $style = '', $class = 'btn-primary', $center = false) {
        if ($center)
            echo $this->tag('div', null, array('style' => 'text-align:center;'));

        echo $this->link("<i class='fa fa-edit'></i> $value", array("action" => $action), array('class' => "btn $class", 'escape' => false, 'title' => $title, 'style' => $style));

        if ($center)
            echo $this->tag('/div', null);
    }

    function boutonSubmit($value = 'Enregistrer', $title = 'Envoyer le formulaire', $icon = 'save', $center = false, $class = 'btn-primary', $style = '') {
        if ($center)
            echo $this->tag('div', null, array('style' => 'text-align:center;'));
        echo $this->Form->button("<i class='fa fa-$icon'></i> $value", array('class' => "btn $class", 'escape' => false, 'title' => $title, 'style' => $style));
        if ($center)
            echo $this->tag('/div', null);
    }

    function boutonModifierUrl($url = 'edit', $value = 'Modifier', $title = 'Modifier', $style = '', $class = 'btn-primary', $center = false) {
        if ($center)
            echo $this->tag('div', null, array('style' => 'text-align:center;'));

        echo $this->link("<i class='fa fa-edit'></i> $value", $url, array('class' => "btn $class", 'escape' => false, 'title' => $title, 'style' => $style));

        if ($center)
            echo $this->tag('/div', null);
    }

    function boutonSubmitUrl($url = 'edit', $value = 'Modifier', $title = 'Modifier', $style = '', $class = 'btn-primary', $type='') {
        echo $this->link("<i class='$type'></i> $value", $url, array('class' => "btn $class", 'escape' => false, 'title' => $title, 'style' => $style));
    }

    function boutonsSaveCancel($onclick = '', $urlCancel = "javascript:history.go(-1)", $titleSave = "Sauvegarder", $valueSave = 'Sauvegarder', $center = false) {
        if ($center) {
            echo $this->tag("div", null, array("class" => "btn-group", 'style' => 'text-align:center; margin-top:10px;'));
            echo $this->link('<i class="fa fa-arrow-left"></i> Annuler', $urlCancel, array('class' => 'btn', 'escape' => false, 'title' => 'Annuler', 'style' => 'float:none;'));
            echo $this->Form->button("<i class='fa fa-save'></i> $valueSave", array('type' => 'submit', 'id' => 'boutonValider', 'class' => 'btn btn-primary', 'escape' => false, 'title' => $titleSave, 'onclick' => $onclick, 'style' => 'float:none;'));
            echo $this->tag('/div', null);
        } else {
            echo $this->tag("div", null, array("class" => "btn-group", 'style' => 'margin-top:10px;'));
            echo $this->link('<i class="fa fa-arrow-left"></i> Annuler', $urlCancel, array('class' => 'btn', 'escape' => false, 'title' => 'Annuler'));
            echo $this->Form->button("<i class='fa fa-save'></i> $valueSave", array('type' => 'submit', 'id' => 'boutonValider', 'class' => 'btn btn-primary', 'escape' => false, 'title' => $titleSave, 'onclick' => $onclick));
            echo $this->tag('/div', null);
        }
    }

    function boutonsSaveCancelUrl($urlCancel="javascript:history.go(-1)", $onclick = '', $titleSave = "Sauvegarder", $valueSave = 'Sauvegarder', $center = false) {
        if ($center) {
            echo $this->tag("div", null, array("class" => "btn-group", 'style' => 'text-align:center; margin-top:10px;'));
            echo $this->link('<i class="fa fa-arrow-left"></i> Annuler', $urlCancel, array('class' => 'btn', 'escape' => false, 'title' => 'Annuler', 'style' => 'float:none;'));
            echo $this->Form->button("<i class='fa fa-save'></i> $valueSave", array('type' => 'submit', 'id' => 'boutonValider', 'class' => 'btn btn-primary', 'escape' => false, 'title' => $titleSave, 'onclick' => $onclick, 'style' => 'float:none;'));
            echo $this->tag('/div', null);
        } else {
            echo $this->tag("div", null, array("class" => "btn-group", 'style' => 'margin-top:10px;'));
            echo $this->link('<i class="fa fa-arrow-left"></i> Annuler', $urlCancel, array('class' => 'btn', 'escape' => false, 'title' => 'Annuler'));
            echo $this->Form->button("<i class='fa fa-save'></i> $valueSave", array('type' => 'submit', 'id' => 'boutonValider', 'class' => 'btn btn-primary', 'escape' => false, 'title' => $titleSave, 'onclick' => $onclick));
            echo $this->tag('/div', null);
        }
    }

    function boutonsAddCancel($onclick = "", $urlCancel = "javascript:history.go(-1)", $titleSave = "Ajouter", $center = false) {
        if ($center) {
            echo $this->tag("div", null, array("class" => "btn-group", 'style' => 'text-align:center; margin-top:10px;'));
            echo $this->link('<i class="fa fa-arrow-left"></i> Annuler', $urlCancel, array('class' => 'btn', 'escape' => false, 'title' => 'Annuler', 'style' => 'float:none;'));
            echo $this->Form->button('<i class="fa fa-save"></i> Sauvegarder', array('type' => 'submit', 'id' => 'boutonValider', 'class' => 'btn btn-primary', 'escape' => false, 'title' => $titleSave, 'onclick' => $onclick, 'style' => 'float:none;'));
            echo $this->tag('/div', null);
        } else {
            echo $this->tag("div", null, array("class" => "btn-group", 'style' => 'margin-top:10px;'));
            echo $this->link('<i class="fa fa-arrow-left"></i> Annuler', $urlCancel, array('class' => 'btn', 'escape' => false, 'title' => 'Annuler'));
            echo $this->Form->button('<i class="fa fa-save"></i> Sauvegarder', array('type' => 'submit', 'id' => 'boutonValider', 'class' => 'btn btn-primary', 'escape' => false, 'title' => $titleSave, 'onclick' => $onclick));
            echo $this->tag('/div', null);
        }
    }

}
