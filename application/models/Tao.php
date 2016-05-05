<?php

class TaoModel extends Model {

    const TBL_TAO = 'tao'; 

    public function getAppData($appName)
    {
        $this->_db->where('appName', $appName);
        $data = $this->_db->getOne(SELF::TBL_TAO);
        if(!$data) {
            return FALSE;
        } 
        return $data;
    }

}
