<?php
    class Nature extends AppModel {
       var $name = 'Nature';
         /* retourne la liste des natures [id]=>[libelle] pour utilisation html->selectTag */
        function generateList($order_by=null) {
                $generateList = array();

                if ($order_by==null)
                    $natures = $this->find('all', array('fields'=>'id, name'));
                else
                    $natures = $this->find('all', array('fields'=> 'id, name', 'order' => $order_by.' DESC'));

                foreach($natures as $nature) {
                        $generateList[$nature['Nature']['id']] = $nature['Nature']['name'];
                }

                return $generateList;
        }

        function makeBalise(&$oMainPart, $nature_id) {
            $nature = $this->find('first', array('conditions' => array('Nature.id' => $nature_id),
                                                 'fields'     => array('name'),
                                                 'recursive'  => -1));

            $oMainPart->addElement(new GDO_FieldType('nature_projet', ($nature['Nature']['name']), 'text'));
        }

    }
?>
