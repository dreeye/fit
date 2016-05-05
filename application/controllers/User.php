<?php

require '../vendor/autoload.php';

class UserController extends Login
{
    /**
     * 注销
     *
     *
     *
     */
    public function logoutAction()
    {
        // 必要参数是否存在且不为空        
        $userId = ( $this->_post['userId'] ?? $this->Response->error('40016')) ? : $this->Response->error('40033');
       echo '<pre>';print_r($this->Predis->get('wangwei'));echo '</pre>';exit();  
        if ( ! $this->Predis
                    ->transaction()
                    ->set('wangwei', 'bbb', 'ex',100)
                    ->get('wangwei')
                    ->execute() ) 
        {
           echo '<pre>';print_r('fuck');echo '</pre>';exit();   
        }



    }
    

}
