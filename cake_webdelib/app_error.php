<?php
class AppError extends ErrorHandler {

    function gedooo($params) {
        $this->controller->set('error', $params['error']);
        $this->controller->set('lastUrl', $params['url']);
        $this->_outputMessage('gedooo');
    }

}	
?>

