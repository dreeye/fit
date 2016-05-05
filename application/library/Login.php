<?php

class Login extends Core {


    public function init()
    {
        parent::init();
        if (! $this->_sessionToken) $this->Response->error('40009');
    }

}
