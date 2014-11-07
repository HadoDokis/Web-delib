<?php

App::uses('FormHelper', 'View/Helper');
App::uses('AppTools', 'Lib');

class DurationPickerHelper extends FormHelper {

    var $helpers = array('Html');

    function picker($fieldName, $options = array()) {
        // initialisations
        $out = '';
        $div = true;
        $divOptions = array();

        if (array_key_exists('div', $options)) {
            $div = $options['div'];
            unset($options['div']);
        }

        if (!empty($div)) {
            $divOptions['class'] = 'date';
            if (is_string($div)) {
                $divOptions['class'] = $div;
            } elseif (is_array($div)) {
                $divOptions = array_merge($divOptions, $div);
            }
            if (isset($this->fieldset['validates']) && in_array($this->field(), $this->fieldset['validates'])) {
                $divOptions = $this->addClass($divOptions, 'required');
            }
            if (!isset($divOptions['tag'])) {
                $divOptions['tag'] = 'div';
            }
        }

        // label
        $label = null;
        if (isset($options['label'])) {
            $label = $options['label'];
            unset($options['label']);
        }
        if ($label !== false) {
            $labelAttributes = $this->domId(array(), 'for');
            $labelText = $label;
            if (isset($options['id'])) {
                $labelAttributes = array_merge($labelAttributes, array('for' => $options['id']));
            }
            $out = $this->label($fieldName, $labelText, $labelAttributes);
        }

        // empty
        $empty = array('year' => false, 'month' => false, 'day' => false, 'hour' => false, 'minute' => false);
        if (isset($options['empty'])) {
            if (is_array($options['empty'])) {
                $emptyOptionDefault = array('year' => '(ans)', 'month' => '(mois)', 'day' => '(jours)', 'hour' => '(heures)', 'minute' => '(minutes)');
                $empty = array_merge($emptyOptionDefault, $options['empty']);
            }
            else
                $empty = array('year' => $options['empty'], 'month' => $options['empty'], 'day' => $options['empty'], 'hour' => $options['empty'], 'minute' => $options['empty']);
            unset($options['empty']);
        }
        // valeur
        $durationValue = $options['value'];
        unset($options['value']);
        if (is_array($durationValue))
            $value = $durationValue;
        else
            $value = AppTools::durationToArray($durationValue);
        
        // affichage
        $display = array('year' => true, 'month' => true, 'day' => true, 'hour' => true, 'minute' => true);
        if (isset($options['display'])) {
            if (is_array($options['display']) && !empty($options['display'])) {
                $displayDefault = array('year' => false, 'month' => false, 'day' => false, 'hour' => false, 'minute' => false);
                $display = array_merge($displayDefault, $options['display']);
            }
            unset($options['display']);
        }
        if ($display['year'])
            $out .= $this->select($fieldName . ".year", $this->__generateOptions('year'), array(
                 "value" => $value['year'], 'empty' => $empty['year']
            ));
        else
            $out .= $this->hidden($fieldName . ".year", array('value' => ''));
        if ($display['month'])
            $out .= $this->select($fieldName . ".month", $this->__generateOptions('month'), array(
                 "value" => $value['month'], 'empty' => $empty['month']
            ));
        else
            $out .= $this->hidden($fieldName . ".month", array('value' => ''));
        if ($display['day'])
            $out .= $this->select($fieldName . ".day", $this->__generateOptions('day'), array(
                 "value" => $value['day'], 'empty' => $empty['day']
            ));
        else
            $out .= $this->hidden($fieldName . ".day", array('value' => ''));
        if ($display['hour'])
            $out .= $this->select($fieldName . ".hour", $this->__generateOptions('hour'), array(
                 "value" => $value['hour'], 'empty' => $empty['hour']
            ));
        else
            $out .= $this->hidden($fieldName . ".hour", array('value' => ''));
        if ($display['minute'])
            $out .= $this->select($fieldName . ".minute", $this->__generateOptions('minute'), array(
                 "value" => $value['minute'], 'empty' => $empty['minute']
            ));
        else
            $out .= $this->hidden($fieldName . ".minute", array('value' => ''));

        // erreur
        $modelName = '';
        if (strpos($fieldName, '.') !== false)
            $modelName = substr($fieldName, 0, strpos($fieldName, '.'));
        else {
            //Deprecated
//          $view =& ClassRegistry::getObject('view');
//          $modelName = $view->model;
            //Cause bug (a voir l'utilité ?)
//          $view = $this->_View;
//          $modelName = $view->model;
        }
        if (!empty($modelName) && isset($this->validationErrors[$modelName][$fieldName]))
            $out .= $this->Html->tag('div', $this->validationErrors[$modelName][$fieldName], array('class' => 'error-message'));

        if (isset($divOptions) && isset($divOptions['tag'])) {
            $tag = $divOptions['tag'];
            unset($divOptions['tag']);
            $out = $this->Html->tag($tag, $out, $divOptions);
        }
        return $out;
    }

    /**
     * Generates option lists for common <select /> menus
     * @access private
     */
    function __generateOptions($name, $options = array()) {
        if (!empty($this->options[$name])) {
            return $this->options[$name];
        }
        $data = array();

        switch ($name) {
            case 'minute':
                if (isset($options['interval'])) {
                    $interval = $options['interval'];
                } else {
                    $interval = 1;
                }
                $i = $interval;
                while ($i < 60) {
                    if ($i == 1)
                        $data[1] = '1 minute';
                    else
                        $data[$i] = $i . ' minutes';
                    $i += $interval;
                }
                break;
            case 'hour':
                for ($i = 1; $i <= 23; $i++) {
                    if ($i == 1)
                        $data[1] = '1 heure';
                    else
                        $data[$i] = $i . ' heures';
                }
                break;
            case 'day':
                $min = isset($options['min']) ? $options['min'] : 1;
                $max = isset($options['max']) ? $options['max'] : 30;
                if ($min > $max)
                    list($min, $max) = array($max, $min);
                for ($i = $min; $i <= $max; $i++) {
                    if ($i == 0)
                        $data[0] = 0;
                    elseif ($i == 1)
                        $data[1] = '1 jour';
                    else
                        $data[$i] = $i . ' jours';
                }
                break;
            case 'month':
                $min = isset($options['min']) ? $options['min'] : 1;
                $max = isset($options['max']) ? $options['max'] : 11;
                if ($min > $max)
                    list($min, $max) = array($max, $min);
                for ($i = $min; $i <= $max; $i++) {
                    if ($i == 0)
                        $data[0] = 0;
                    else
                        $data[$i] = $i . ' mois';
                }
                break;
            case 'year':
                $min = isset($options['min']) ? $options['min'] : 1;
                $max = isset($options['max']) ? $options['max'] : 10;
                if ($min > $max)
                    list($min, $max) = array($max, $min);
                for ($i = $min; $i <= $max; $i++) {
                    if ($i == 0)
                        $data[0] = 0;
                    elseif ($i == 1)
                        $data[1] = '1 an';
                    else
                        $data[$i] = $i . ' ans';
                }
                break;
        }
        $options[$name] = $data;
        return $options[$name];
    }

}
