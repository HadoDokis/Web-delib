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

    }
?>
