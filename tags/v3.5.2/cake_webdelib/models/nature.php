<?php
    class Nature extends AppModel {
       var $name = 'Nature';
         /* retourne la liste des natures [id]=>[libelle] pour utilisation html->selectTag */
        function generateList($order_by=null) {
                $generateList = array();

                if ($order_by==null)
                    $natures = $this->find('all', array('fields'=>'id, libelle'));
                else
                    $natures = $this->find('all', array('fields'=> 'id, libelle', 'order' => $order_by.' DESC'));

                foreach($natures as $nature) {
                        $generateList[$nature['Nature']['id']] = $nature['Nature']['libelle'];
                }

                return $generateList;
        }

        function makeBalise(&$oMainPart, $nature_id) {
            $nature = $this->find('first', array('conditions' => array('Nature.id' => $nature_id),
                                                 'fields'     => array('libelle'),
                                                 'recursive'  => -1));

            $oMainPart->addElement(new GDO_FieldType('nature_projet', utf8_encode($nature['Nature']['libelle']), 'text'));
        }

    }
?>
